---------------------------------
-- Drop old schema
---------------------------------

DROP TABLE IF EXISTS report CASCADE;
DROP TABLE IF EXISTS rates_comment CASCADE;
DROP TABLE IF EXISTS rates_stories CASCADE;
DROP TABLE IF EXISTS belong_tos CASCADE;
DROP TABLE IF EXISTS expert CASCADE;
DROP TABLE IF EXISTS favourites CASCADE;
DROP TABLE IF EXISTS follows CASCADE;
DROP TABLE IF EXISTS comment CASCADE;
DROP TABLE IF EXISTS stories CASCADE;
DROP TABLE IF EXISTS topics CASCADE;
DROP TABLE IF EXISTS member CASCADE;

DROP FUNCTION IF EXISTS check_comment_rate() CASCADE;
DROP FUNCTION IF EXISTS check_stories_rate() CASCADE;
DROP FUNCTION IF EXISTS update_rating() CASCADE;
DROP FUNCTION IF EXISTS insert_rating() CASCADE;
DROP FUNCTION IF EXISTS check_stories_cardinality() CASCADE;
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
CREATE TABLE topics(
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    creation_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

-- R06
CREATE TABLE stories(
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
    story_id INTEGER REFERENCES stories(id) ON UPDATE CASCADE ON DELETE CASCADE,
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
    topic_id INTEGER REFERENCES topics(id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY (user_id, topic_id)
);

-- R04
CREATE TABLE expert(
    user_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE CASCADE,
    topic_id INTEGER REFERENCES topics(id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY(user_id, topic_id)
);


-- R07
CREATE TABLE belong_tos(
    story_id INTEGER REFERENCES stories(id) ON UPDATE CASCADE ON DELETE CASCADE,
    topic_id INTEGER REFERENCES topics(id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY(story_id, topic_id)
);


-- R08
CREATE TABLE rates_stories(
    user_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE CASCADE,
    story_id INTEGER REFERENCES stories(id) ON UPDATE CASCADE ON DELETE CASCADE,
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
    story_id INTEGER REFERENCES stories(id) ON UPDATE CASCADE ON DELETE CASCADE,
    constraint only_one_value 
        check (        (story_id is null or comment_id is null) 
               and not (story_id is null and comment_id is null) )
);

---------------------------------
-- Indexes
---------------------------------

-- IDX11
CREATE INDEX comment_full_text ON comment USING GIST(to_tsvector('english', content));

-- IDX12
CREATE INDEX stories_title_full_text ON stories USING GIST(to_tsvector('english', title));

-- IDX01
CREATE INDEX topics_stories ON belong_tos(topic_id);

-- IDX02
CREATE INDEX user_comment_rating ON rates_comment USING btree(user_id, comment_id);

-- IDX03
CREATE INDEX user_stories_rating ON rates_stories USING btree(user_id, story_id);

-- IDX04
CREATE INDEX member_username ON member USING btree(username);

-- IDX05
CREATE INDEX user_topicss ON favourites (user_id);

---------------------------------
-- Triggers and UDFs
---------------------------------

-- TRIGGER01
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


-- TRIGGER02
CREATE FUNCTION check_stories_rate() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF EXISTS (SELECT * FROM rates_stories WHERE rates_stories.user_id = NEW.user_id 
                                           AND rates_stories.story_id = NEW.story_id 
                                           AND rates_stories.rating = NEW.rating) THEN
        RAISE EXCEPTION 'A user cannot up or downvote the same stories more than once.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE PLPGSQL;

CREATE TRIGGER check_stories_rate
    BEFORE INSERT OR UPDATE ON rates_stories
    FOR EACH ROW
    EXECUTE PROCEDURE check_stories_rate();


-- TRIGGER03
CREATE FUNCTION insert_rating() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.rating IS TRUE THEN
        UPDATE stories
        SET rating = rating + 1 WHERE NEW.story_id = stories.id;
    ELSE
        UPDATE stories
        SET rating = rating - 1 WHERE NEW.story_id = stories.id;
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER insert_rating
    AFTER INSERT ON rates_stories
    FOR EACH ROW
    EXECUTE PROCEDURE insert_rating();


-- TRIGGER04
CREATE FUNCTION update_rating() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.rating IS TRUE AND OLD.rating IS FALSE THEN
        UPDATE stories
        SET rating = rating + 2 WHERE NEW.story_id = stories.id;
    ELSIF NEW.rating IS FALSE AND OLD.rating IS TRUE THEN
        UPDATE stories
        SET rating = rating - 2 WHERE NEW.story_id = stories.id;
    ELSE 
        RAISE EXCEPTION 'A user cannot up or downvote the same stories more than once.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER update_rating
    AFTER UPDATE ON rates_stories
    FOR EACH ROW
    EXECUTE PROCEDURE update_rating();


-- TRIGGER05
CREATE FUNCTION check_stories_cardinality() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF ((SELECT count(*) FROM belong_tos WHERE belong_tos.story_id = NEW.story_id) >= 3) THEN
        RAISE EXCEPTION 'A stories cannot be associated with more than 3 topicss.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_stories_cardinality
    BEFORE INSERT ON belong_tos
    FOR EACH ROW
    EXECUTE PROCEDURE check_stories_cardinality();


-- TRIGGER06
CREATE FUNCTION check_expert_cardinality() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF ((SELECT count(*) FROM expert WHERE expert.user_id = NEW.user_id) >= 7) THEN
        RAISE EXCEPTION 'An expert cannot be expert in more than 7 topicss.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_expert_cardinality
    BEFORE INSERT ON expert
    FOR EACH ROW
    EXECUTE PROCEDURE check_expert_cardinality();


-- TRIGGER07
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


-----------------------------------------
-- Populate the database
-----------------------------------------

INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Speedy','Joao Monteiro','joaomonteiromail@gmail.com','GpY7UYPASx',TRUE);
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Goli','Gonçalo Oliveira','up201705494@fe.up.pt','3x7XJ50Tl4',TRUE);
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Ferreira','Joana Ferreira','up201705722@fe.up.pt','tlbe7OS4H8',TRUE);
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('JMatos','Joao Matos','up201705471@fe.up.pt','aRKJo0igHZ',TRUE);
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('mr_bubble','James Bubble','bubbleJ@gmail.com','9OI2Y0MQYS',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('jj_R','Randall','jjrandal@gmail.com','YsSjHUEG7n',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('shadowmose','Miranda','shadowmose@gmail.com','doGKytPq4k',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('SovietWomble','Soviet Womble','sovietwomble@gmail.com','fK5c1nNKDI',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('kerni86','Erny','kerni86@gmail.com','DcNXKjn5UA',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('remiliascarlett','Remilia Scarlett','remiliascarlett@gmail.com','bYxO52xfOq',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Hououin','Rintaro Okabe','secretlab@gmail.com','sTz1Z6xjrT',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('SortaMaliciousGaming','Ryan Haywood','ryanhay@gmail.com','u13Qz3Mcmf',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('dat_cat_guy','Cat Man','datcatguy@gmail.com','uq0OBDcGi4',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Gladiator','My name is Maximus Decimus Meridius, commander of the Armies of the North, General of the Felix Legions, loyal servant to the true emperor, Marcus Aurelius. Father to a murdered son, husband to a murdered wife. And I will have my vengeance, in this life or the next','romangladiator@gmail.com','W8mDYkiPtb',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('benyhild','Ben Hild','benyhild@gmail.com','0rTFf0PqBu',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('gearlessjoe','Joe Gears','gearlessjoe@gmail.com','IxOrHPTo4c',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('maria_of_pees','Maria Pee','mariapess@gmail.com','OzV8mDFIB7',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('enterthenick','Nick','thenick@gmail.com','hXHNW3vdaz',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('1987_azrael','Azrael','az87el@gmail.com','41oGSWRiCC',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Emiru','Emiru','emiru@gmail.com','hfAdAvujjR',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Jett','Joan Jett','joanj@gmail.com','iArsHASZ5l',FALSE);
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('rackun','Rocky Racoon','rackun@gmail.com','0foKEU3gXK',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('plantburger','Andrew Adams','plantburguer@gmail.com','WtCk6IqEOC',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('jpeg','Adams Andrew','jpeg@gmail.com','Je06p4R6QX',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('chadsong','Chad Smith','chadsong@gmail.com','KMgIIoVsnf',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('jetmechanic','Jet Grousum','jetmechanic@gmail.com','s4E9dkwdUl',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('v3ldrin','Ainsley','veldrin@gmail.com','qnfn3Dq5Vv',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Schrute','Dwight Schrute','dschrute@gmail.com','Sbe2HZtsdk',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('orangegirl','Barbara','orangegirl@gmail.com','quiy5dmOfz',FALSE);
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('TheElon','Elon Musk','elon@spacex.com','DDNoP7f2V1',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('enricoalois','Enrique Vasques','enricalois@gmail.com','inZIqtUlfp',FALSE);  


INSERT INTO "topics" (name,creation_date) VALUES ('Politics','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('fellthebern','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('Portugal','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('Coronavirus','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('Economy','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('Sports','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('Forest','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('FamousPeople','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('Japan','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('War','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('Polution','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('Nature','2020-03-24');


INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Trump is at it again',1,'2020-03-24',0.74,0);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Putin is the best',28,'2020-03-24',1,2);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Biden and Bernie face off',7,'2020-03-24',0.5,0);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Sanders wins Democrats Abroad primary',3,'2020-03-24',0.9,1);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Estado de Emergencia',4,'2020-03-24',1,1);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Cristiano Ronaldo marca na própria baliza',10,'2020-03-24',0.1,-2);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Deaths in Italy pass the thousands',25,'2020-03-24',0.8,2);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('When will the vacine come?',13,'2020-03-24',0.5,1);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Rich people have more money than poor people',20,'2020-03-24',0.7,0);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Stock exchange crashes again',16,'2020-03-24',0.2,0);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Olimpic Games canceled?',2,'2020-03-24',0.7,2);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Could NHL implement compliance buyouts following COVID-19 shutdown?',26,'2020-03-24',0.4,1);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Australian bushfires',27,'2020-03-24',0.85,1);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Indian forest regrowing',30,'2020-03-24',0.24,3);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Jean Jett launches new album',19,'2020-03-24',0.1,2);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Kylie Jenner realeses new sex tape', 20,'2020-03-24',0.9,1);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Japan is the spring is the most beautiful place',1,'2020-03-24',1,1);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Old samurai ruin discovered',19,'2020-03-24',0.5,1);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Potential war between USA and Iraq',5,'2020-03-24',0.79,1);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('What started WW2?',8,'2020-03-24',0.6,-1);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Polution levels decrease',4,'2020-03-24',0.9,1);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('The most poluted river in the world',14,'2020-03-24',0.4,2);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Extincted species resurfaces',31,'2020-03-24',0.4,0);
INSERT INTO "stories" (title,author_id,published_date,reality_check,rating) VALUES ('Frogs!',24,'2020-03-24',1,1);


INSERT INTO "belong_tos" (story_id, topic_id) VALUES (1,1);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (2,1);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (3,2);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (4,2);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (5,3);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (6,3);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (7,4);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (8,4);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (9,5);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (10,5);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (11,6);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (12,6);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (13,7);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (14,7);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (15,8);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (16,8);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (17,9);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (18,9);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (19,10);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (20,10);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (21,11);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (22,11);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (23,12);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (24,12);


INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('What he do this time?',20,'2020-03-24',1,null,1);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('You dont know?',21,'2020-03-24',1,1,null);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Phasellus in tellus placerat, commodo ligula eu, tempor ante. Etiam.',14,'2020-03-24',1,null,2);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Donec mi purus, facilisis sit amet orci ac, semper efficitur.',25,'2020-03-24',1,null,3);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Lorem ipsum dolor sit amet, consectetur adipiscing.',3,'2020-03-24',1,4,null);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Donec non luctus risus, nec finibus nunc.',10,'2020-03-24',1,4,null);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Pellentesque pulvinar orci sed pellentesque vulputate. Fusce.',11,'2020-03-24',1,null,5);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Nulla sit amet pharetra odio. Pellentesque suscipit.',31,'2020-03-24',1,null,5);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Nulla blandit nunc sit amet leo auctor.',26,'2020-03-24',1,null,7);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Suspendisse potenti. In molestie iaculis ipsum, sed.',22,'2020-03-24',1,9,null);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('In vulputate velit sit amet nisi gravida.',19,'2020-03-24',1,10,null);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Sed eget dolor magna. Cras dapibus justo.',4,'2020-03-24',1,10,null);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Donec aliquam ipsum id risus convallis maximus.',7,'2020-03-24',1,null,9);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('In vel mollis arcu, in cursus risus.',7,'2020-03-24',1,null,9);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Integer consectetur metus in rhoncus aliquet. Integer.',21,'2020-03-24',1,null,9);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Mauris volutpat eros eu posuere vestibulum. Vivamus.',13,'2020-03-24',1,null,12);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Nullam et lectus gravida, maximus magna sit.',17,'2020-03-24',1,16,null);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Fusce vel libero a leo ullamcorper interdum.',25,'2020-03-24',1,16,null);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Proin sed vulputate elit, a convallis nunc.',30,'2020-03-24',1,null,15);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Vestibulum ante ipsum primis in faucibus orci.',1,'2020-03-24',1,19,null);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Praesent tempor odio id tempus sodales. Nam.',9,'2020-03-24',1,null,18);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Sed convallis varius odio, lacinia egestas dui.',2,'2020-03-24',1,null,19);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Praesent eleifend urna et mauris sodales lacinia.',5,'2020-03-24',1,null,19);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Etiam rhoncus porta commodo. Praesent elementum diam.',15,'2020-03-24',1,null,21);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Curabitur feugiat mauris ut dolor fermentum imperdiet.',2,'2020-03-24',1,null,23);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Nam tortor leo, bibendum at condimentum quis.',2,'2020-03-24',1,25,null);
INSERT INTO "comment" (content,author_id,published_date,rating,comment_id,story_id) VALUES ('Aliquam sed dolor dui. Vestibulum condimentum lorem.',20,'2020-03-24',1,26,null);


INSERT INTO "follows" (user_id,friend_id) VALUES (5,7);
INSERT INTO "follows" (user_id,friend_id) VALUES (5,18);
INSERT INTO "follows" (user_id,friend_id) VALUES (5,20);
INSERT INTO "follows" (user_id,friend_id) VALUES (6,7);
INSERT INTO "follows" (user_id,friend_id) VALUES (7,18);
INSERT INTO "follows" (user_id,friend_id) VALUES (7,30);
INSERT INTO "follows" (user_id,friend_id) VALUES (9,14);
INSERT INTO "follows" (user_id,friend_id) VALUES (9,15);
INSERT INTO "follows" (user_id,friend_id) VALUES (9,24);
INSERT INTO "follows" (user_id,friend_id) VALUES (12,5);
INSERT INTO "follows" (user_id,friend_id) VALUES (12,9);
INSERT INTO "follows" (user_id,friend_id) VALUES (14,10);
INSERT INTO "follows" (user_id,friend_id) VALUES (15,11);
INSERT INTO "follows" (user_id,friend_id) VALUES (16,22);
INSERT INTO "follows" (user_id,friend_id) VALUES (17,11);
INSERT INTO "follows" (user_id,friend_id) VALUES (17,14);
INSERT INTO "follows" (user_id,friend_id) VALUES (17,21);
INSERT INTO "follows" (user_id,friend_id) VALUES (17,27);
INSERT INTO "follows" (user_id,friend_id) VALUES (20,8);
INSERT INTO "follows" (user_id,friend_id) VALUES (21,13);
INSERT INTO "follows" (user_id,friend_id) VALUES (21,18);
INSERT INTO "follows" (user_id,friend_id) VALUES (22,30);
INSERT INTO "follows" (user_id,friend_id) VALUES (23,24);
INSERT INTO "follows" (user_id,friend_id) VALUES (23,31);
INSERT INTO "follows" (user_id,friend_id) VALUES (25,7);
INSERT INTO "follows" (user_id,friend_id) VALUES (25,9);
INSERT INTO "follows" (user_id,friend_id) VALUES (26,10);
INSERT INTO "follows" (user_id,friend_id) VALUES (27,11);
INSERT INTO "follows" (user_id,friend_id) VALUES (28,24);
INSERT INTO "follows" (user_id,friend_id) VALUES (28,29);
INSERT INTO "follows" (user_id,friend_id) VALUES (28,31);
INSERT INTO "follows" (user_id,friend_id) VALUES (29,6);
INSERT INTO "follows" (user_id,friend_id) VALUES (30,9);
INSERT INTO "follows" (user_id,friend_id) VALUES (31,13);


INSERT INTO "favourites" (user_id,topic_id) VALUES (5,2);
INSERT INTO "favourites" (user_id,topic_id) VALUES (5,8);
INSERT INTO "favourites" (user_id,topic_id) VALUES (6,1);
INSERT INTO "favourites" (user_id,topic_id) VALUES (6,5);
INSERT INTO "favourites" (user_id,topic_id) VALUES (6,12);
INSERT INTO "favourites" (user_id,topic_id) VALUES (7,4);
INSERT INTO "favourites" (user_id,topic_id) VALUES (8,4);
INSERT INTO "favourites" (user_id,topic_id) VALUES (9,7);
INSERT INTO "favourites" (user_id,topic_id) VALUES (9,11);
INSERT INTO "favourites" (user_id,topic_id) VALUES (10,3);
INSERT INTO "favourites" (user_id,topic_id) VALUES (10,6);
INSERT INTO "favourites" (user_id,topic_id) VALUES (11,4);
INSERT INTO "favourites" (user_id,topic_id) VALUES (13,9);
INSERT INTO "favourites" (user_id,topic_id) VALUES (14,3);
INSERT INTO "favourites" (user_id,topic_id) VALUES (14,9);
INSERT INTO "favourites" (user_id,topic_id) VALUES (14,10);
INSERT INTO "favourites" (user_id,topic_id) VALUES (16,11);
INSERT INTO "favourites" (user_id,topic_id) VALUES (18,11);
INSERT INTO "favourites" (user_id,topic_id) VALUES (18,12);
INSERT INTO "favourites" (user_id,topic_id) VALUES (19,12);
INSERT INTO "favourites" (user_id,topic_id) VALUES (20,4);
INSERT INTO "favourites" (user_id,topic_id) VALUES (21,4);
INSERT INTO "favourites" (user_id,topic_id) VALUES (22,4);
INSERT INTO "favourites" (user_id,topic_id) VALUES (23,4);
INSERT INTO "favourites" (user_id,topic_id) VALUES (23,7);
INSERT INTO "favourites" (user_id,topic_id) VALUES (24,3);
INSERT INTO "favourites" (user_id,topic_id) VALUES (25,8);
INSERT INTO "favourites" (user_id,topic_id) VALUES (26,5);
INSERT INTO "favourites" (user_id,topic_id) VALUES (26,10);
INSERT INTO "favourites" (user_id,topic_id) VALUES (27,7);
INSERT INTO "favourites" (user_id,topic_id) VALUES (28,11);
INSERT INTO "favourites" (user_id,topic_id) VALUES (30,8);


INSERT INTO "expert" (user_id,topic_id) VALUES (5,2);
INSERT INTO "expert" (user_id,topic_id) VALUES (5,8);
INSERT INTO "expert" (user_id,topic_id) VALUES (7,4);
INSERT INTO "expert" (user_id,topic_id) VALUES (9,11);
INSERT INTO "expert" (user_id,topic_id) VALUES (13,9);
INSERT INTO "expert" (user_id,topic_id) VALUES (14,3);
INSERT INTO "expert" (user_id,topic_id) VALUES (14,10);
INSERT INTO "expert" (user_id,topic_id) VALUES (18,12);
INSERT INTO "expert" (user_id,topic_id) VALUES (20,4);
INSERT INTO "expert" (user_id,topic_id) VALUES (21,4);
INSERT INTO "expert" (user_id,topic_id) VALUES (24,3);
INSERT INTO "expert" (user_id,topic_id) VALUES (26,5);
INSERT INTO "expert" (user_id,topic_id) VALUES (26,10);
INSERT INTO "expert" (user_id,topic_id) VALUES (27,7);


INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (5,2,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (5,4,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (5,16,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (6,6,FALSE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (6,15,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (7,14,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (7,24,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (8,3,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (8,10,FALSE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (8,18,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (9,3,FALSE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (10,15,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (13,7,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (13,10,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (14,19,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (15,14,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (16,6,FALSE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (16,7,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (17,11,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (17,12,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (17,13,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (18,2,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (20,8,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (20,22,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (21,14,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (22,22,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (23,5,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (25,5,FALSE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (26,5,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (26,21,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (28,11,TRUE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (29,20,FALSE);
INSERT INTO "rates_stories" (user_id, story_id, rating) VALUES (30,17,TRUE);


INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (21,1,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (20,2,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (5,3,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (21,4,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (8,5,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (14,6,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (19,7,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (21,8,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (31,9,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (2,10,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (15,11,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (27,12,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (8,13,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (10,14,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (14,15,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (3,16,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (7,17,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (15,18,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (20,19,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (21,20,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (29,21,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (22,22,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (25,23,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (5,24,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (15,25,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (18,26,TRUE);
INSERT INTO "rates_comment" (user_id, comment_id, rating) VALUES (10,27,TRUE);


INSERT INTO "report" (content,published_date,user_id,comment_id,story_id) VALUES ('Explicit content','2020-03-24',15,null,24);
INSERT INTO "report" (content,published_date,user_id,comment_id,story_id) VALUES ('Racism','2020-03-24',12,12,null);
INSERT INTO "report" (content,published_date,user_id,comment_id,story_id) VALUES ('Inappropriate language','2020-03-24',20,13,null);
INSERT INTO "report" (content,published_date,user_id,comment_id,story_id) VALUES ('Inappropriate language','2020-03-24',20,14,null);
INSERT INTO "report" (content,published_date,user_id,comment_id,story_id) VALUES ('Agressive','2020-03-24',30,null,8);