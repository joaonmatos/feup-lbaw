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
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    reality_check NUMBER NOT NULL CONSTRAINT reality_check_ck CHECK ((reality_check >= 0) AND (reality_check <= 1))
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