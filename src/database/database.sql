/* Remove comments for testing
DROP TABLE IF EXISTS report;
DROP TABLE IF EXISTS rates_comment;
DROP TABLE IF EXISTS rates_story;
DROP TABLE IF EXISTS belongs_to;
DROP TABLE IF EXISTS expert;
DROP TABLE IF EXISTS favourites;
DROP TABLE IF EXISTS friend;
DROP TABLE IF EXISTS comment;
DROP TABLE IF EXISTS story;
DROP TABLE IF EXISTS topic;
DROP TABLE IF EXISTS member;
*/

-- Tables
DROP TABLE IF EXISTS member;

-- R01
CREATE TABLE member (
    id SERIAL PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
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
    author_id INTEGER REFERENCES member(id),
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    reality_check NUMERIC NOT NULL CONSTRAINT reality_check_ck CHECK ((reality_check >= 0) AND (reality_check <= 1))
);

-- R10
CREATE TABLE comment(
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    author_id INTEGER REFERENCES member(id),
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    reality_check NUMERIC NOT NULL CONSTRAINT reality_check_ck CHECK ((reality_check >= 0) AND (reality_check <= 1))
);

-- R02
CREATE TABLE friend(
    user_id INTEGER REFERENCES member(id),
    friend_id INTEGER REFERENCES member(id),
    PRIMARY KEY (user_id, friend_id)
);

-- R03
CREATE TABLE favourites(
    user_id INTEGER REFERENCES member(id),
    topic_id INTEGER REFERENCES topic(id),
    PRIMARY KEY (user_id, topic_id)
);

-- R04
CREATE TABLE expert(
    user_id INTEGER REFERENCES member(id) PRIMARY KEY,
    topic_id INTEGER REFERENCES topic(id)
);

-- R07
CREATE TABLE belongs_to(
    story_id INTEGER REFERENCES story(id) PRIMARY KEY,
    topic_id INTEGER REFERENCES topic(id)
);


-- R08
CREATE TABLE rates_story(
    user_id INTEGER REFERENCES member(id),
    story_id INTEGER REFERENCES story(id),
    rating BOOLEAN NOT NULL,
    PRIMARY KEY(user_id, story_id)
);

-- R09
CREATE TABLE rates_comment(
    user_id INTEGER REFERENCES member(id),
    comment_id INTEGER REFERENCES comment(id),
    rating BOOLEAN NOT NULL,
    PRIMARY KEY(user_id, comment_id)
);

-- R11
CREATE TABLE report(
    id INTEGER REFERENCES member(id),
    content TEXT NOT NULL,
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    user_id INTEGER REFERENCES member(id)
);