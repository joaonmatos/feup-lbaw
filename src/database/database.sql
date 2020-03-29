---------------------------------
-- Drop old schema
---------------------------------

DROP TABLE IF EXISTS report;
DROP TABLE IF EXISTS rates_comment;
DROP TABLE IF EXISTS rates_story;
DROP TABLE IF EXISTS belongs_to;
DROP TABLE IF EXISTS expert;
DROP TABLE IF EXISTS favourites;
DROP TABLE IF EXISTS follows;
DROP TABLE IF EXISTS comment;
DROP TABLE IF EXISTS story;
DROP TABLE IF EXISTS topic;
DROP TABLE IF EXISTS member CASCADE;

DROP FUNCTION IF EXISTS check_comment_rate() CASCADE;
DROP FUNCTION IF EXISTS check_story_rate() CASCADE;
DROP FUNCTION IF EXISTS update_rating() CASCADE;
DROP FUNCTION IF EXISTS insert_rating() CASCADE;
DROP FUNCTION IF EXISTS check_story_cardinality() CASCADE;
DROP FUNCTION IF EXISTS check_expert_cardinality() CASCADE;
DROP FUNCTION IF EXISTS check_password() CASCADE;


---------------------------------
-- Tables
---------------------------------
-- R01
CREATE TABLE member (
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    is_admin BOOLEAN NOT NULL
);

-- R05
CREATE TABLE topic(
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    creation_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

-- R06
CREATE TABLE story(
    id SERIAL PRIMARY KEY,
    title TEXT NOT NULL,
    author_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE SET NULL,
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    reality_check NUMERIC NOT NULL CONSTRAINT reality_check_ck CHECK ((reality_check >= 0) AND (reality_check <= 1)),
    rating INTEGER
);

-- R10
CREATE TABLE comment(
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    author_id INTEGER REFERENCES member(id),
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL, 
    rating INTEGER,
    comment_id INTEGER REFERENCES comment(id) ON UPDATE CASCADE ON DELETE SET NULL,
    story_id INTEGER REFERENCES story(id) ON UPDATE CASCADE ON DELETE CASCADE,
    constraint only_one_value 
        check (        (story_id is null or comment_id is null) 
               and not (story_id is null and comment_id is null) )
);

-- R02
CREATE TABLE follows(
    user_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE CASCADE,
    friend_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY (user_id, friend_id)
);

-- R03
CREATE TABLE favourites(
    user_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE CASCADE,
    topic_id INTEGER REFERENCES topic(id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY (user_id, topic_id)
);

-- R04
CREATE TABLE expert(
    user_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE CASCADE,
    topic_id INTEGER REFERENCES topic(id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY(user_id, topic_id)
);


-- R07
CREATE TABLE belongs_to(
    story_id INTEGER REFERENCES story(id) ON UPDATE CASCADE ON DELETE CASCADE PRIMARY KEY,
    topic_id INTEGER REFERENCES topic(id) ON UPDATE CASCADE ON DELETE CASCADE
);


-- R08
CREATE TABLE rates_story(
    user_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE CASCADE,
    story_id INTEGER REFERENCES story(id) ON UPDATE CASCADE ON DELETE CASCADE,
    rating BOOLEAN NOT NULL,
    PRIMARY KEY(user_id, story_id)
);

-- R09
CREATE TABLE rates_comment(
    user_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE CASCADE,
    comment_id INTEGER REFERENCES comment(id) ON UPDATE CASCADE ON DELETE CASCADE,
    rating BOOLEAN NOT NULL,
    PRIMARY KEY(user_id, comment_id)
);

-- R11
CREATE TABLE report(
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    user_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE CASCADE,
    comment_id INTEGER REFERENCES comment(id) ON UPDATE CASCADE ON DELETE CASCADE,
    story_id INTEGER REFERENCES story(id) ON UPDATE CASCADE ON DELETE CASCADE,
    constraint only_one_value 
        check (        (story_id is null or comment_id is null) 
               and not (story_id is null and comment_id is null) )
);

---------------------------------
-- Indexes
---------------------------------

---------------------------------
-- Triggers and UDFs
---------------------------------

CREATE FUNCTION check_comment_rate() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF EXISTS (SELECT * FROM rates_comment WHERE rates_comment.user_id = NEW.user_id 
                                           AND rates_comment.comment_id = NEW.comment_id 
                                           AND rates_comment.rating = NEW.rating) THEN
        RAISE EXCEPTION 'A user cannot up or downvote the same comment more than once.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE PLPGSQL;

CREATE TRIGGER check_comment_rate
    BEFORE INSERT OR UPDATE ON rates_comment
    FOR EACH ROW
    EXECUTE PROCEDURE check_comment_rate();


CREATE FUNCTION check_story_rate() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF EXISTS (SELECT * FROM rates_story WHERE rates_story.user_id = NEW.user_id 
                                           AND rates_story.story_id = NEW.story_id 
                                           AND rates_story.rating = NEW.rating) THEN
        RAISE EXCEPTION 'A user cannot up or downvote the same story more than once.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE PLPGSQL;

CREATE TRIGGER check_story_rate
    BEFORE INSERT OR UPDATE ON rates_story
    FOR EACH ROW
    EXECUTE PROCEDURE check_story_rate();


CREATE FUNCTION insert_rating() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.rating IS TRUE THEN
        UPDATE story
        SET rating = rating + 1 WHERE NEW.story_id = story.id;
    ELSE
        UPDATE story
        SET rating = rating - 1 WHERE NEW.story_id = story.id;
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER insert_rating
    AFTER INSERT ON rates_story
    FOR EACH ROW
    EXECUTE PROCEDURE insert_rating();


CREATE FUNCTION update_rating() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.rating IS TRUE AND OLD.rating IS FALSE THEN
        UPDATE story
        SET rating = rating + 2 WHERE NEW.story_id = story.id;
    ELSIF NEW.rating IS FALSE AND OLD.rating IS TRUE THEN
        UPDATE story
        SET rating = rating - 2 WHERE NEW.story_id = story.id;
    ELSE 
        RAISE EXCEPTION 'A user cannot up or downvote the same story more than once.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER update_rating
    AFTER UPDATE ON rates_story
    FOR EACH ROW
    EXECUTE PROCEDURE update_rating();


CREATE FUNCTION check_story_cardinality() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF ((SELECT count(*) FROM belongs_to WHERE belongs_to.story_id = NEW.story_id) >= 3) THEN
        RAISE EXCEPTION 'A story cannot be associated with more than 3 topics.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_story_cardinality
    BEFORE INSERT ON belongs_to
    FOR EACH ROW
    EXECUTE PROCEDURE check_story_cardinality();


CREATE FUNCTION check_expert_cardinality() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF ((SELECT count(*) FROM expert WHERE expert.user_id = NEW.user_id) >= 3) THEN
        RAISE EXCEPTION 'An expert cannot be expert in more than 3 topics.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_expert_cardinality
    BEFORE INSERT ON expert
    FOR EACH ROW
    EXECUTE PROCEDURE check_expert_cardinality();


CREATE FUNCTION check_password() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF ((SELECT password FROM member WHERE member.id = NEW.id) = NEW.password) THEN
        RAISE EXCEPTION 'New password must be different from old password';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_password
    BEFORE UPDATE ON member
    FOR EACH ROW
    EXECUTE PROCEDURE check_password();