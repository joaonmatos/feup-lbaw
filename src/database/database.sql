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
DROP TABLE IF EXISTS member

CREATE TABLE member (
    id INTEGER PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    is_admin BOOLEAN NOT NULL
);

CREATE TABLE topic(
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    creation_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

CREATE TABLE story(
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
    author_id INTEGER REFERENCES member(id),
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    reality_check NUMERIC NOT NULL CONSTRAINT reality_check_ck CHECK ((reality_check >= 0) AND (reality_check <= 1))
);

CREATE TABLE comment(
    id INTEGER PRIMARY KEY,
    content TEXT NOT NULL,
    author_id INTEGER REFERENCES member(id),
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    reality_check NUMERIC NOT NULL CONSTRAINT reality_check_ck CHECK ((reality_check >= 0) AND (reality_check <= 1))
);

CREATE TABLE friend(
    user_id INTEGER REFERENCES member(id) PRIMARY KEY,
    friend_id INTEGER REFERENCES member(id)
);

CREATE TABLE favourites(
    user_id INTEGER REFERENCES member(id) PRIMARY KEY,
    topic_id INTEGER REFERENCES topic(id)
);

CREATE TABLE expert(
    user_id INTEGER REFERENCES member(id) PRIMARY KEY,
    topic_id INTEGER REFERENCES topic(id)
);

CREATE TABLE belongs_to(
    story_id INTEGER REFERENCES story(id) PRIMARY KEY,
    topic_id INTEGER REFERENCES topic(id)
);

CREATE TABLE rates_story(
    user_id INTEGER REFERENCES member(id),
    story_id INTEGER REFERENCES story(id),
    rating BOOLEAN NOT NULL,
    PRIMARY KEY(user_id, story_id)
);

CREATE TABLE rates_comment(
    user_id INTEGER REFERENCES member(id),
    comment_id INTEGER REFERENCES comment(id),
    rating BOOLEAN NOT NULL,
    PRIMARY KEY(user_id,comment_id)
);

CREATE TABLE report(
    id INTEGER REFERENCES member(id),
    content TEXT NOT NULL,
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    user_id INTEGER REFERENCES member(id)
);