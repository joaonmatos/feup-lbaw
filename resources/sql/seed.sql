---------------------------------
-- Drop old schema
---------------------------------

DROP TABLE IF EXISTS report CASCADE;
DROP TABLE IF EXISTS rates_comments CASCADE;
DROP TABLE IF EXISTS rates_stories CASCADE;
DROP TABLE IF EXISTS belong_tos CASCADE;
DROP TABLE IF EXISTS expert CASCADE;
DROP TABLE IF EXISTS favourites CASCADE;
DROP TABLE IF EXISTS follows CASCADE;
DROP TABLE IF EXISTS comments CASCADE;
DROP TABLE IF EXISTS stories CASCADE;
DROP TABLE IF EXISTS topics CASCADE;
DROP TABLE IF EXISTS member CASCADE;

DROP FUNCTION IF EXISTS check_comments_rate() CASCADE;
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
    name TEXT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    is_admin BOOLEAN NOT NULL
);

-- R05
CREATE TABLE topics(
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL UNIQUE,
    creation_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL
);

-- R06
CREATE TABLE stories(
    id SERIAL PRIMARY KEY,
    title TEXT NOT NULL,
    url TEXT NOT NULL,
    author_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE SET NULL,
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    reality_check NUMERIC NOT NULL CONSTRAINT reality_check_ck CHECK ((reality_check >= 0) AND (reality_check <= 1)),
    rating INTEGER DEFAULT 0
);

-- R10
CREATE TABLE comments(
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    author_id INTEGER REFERENCES member(id),
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL, 
    rating INTEGER DEFAULT 0,
    comment_id INTEGER REFERENCES comments(id) ON UPDATE CASCADE ON DELETE SET NULL,
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
CREATE TABLE rates_comments(
    user_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE CASCADE,
    comment_id INTEGER REFERENCES comments(id) ON UPDATE CASCADE ON DELETE CASCADE,
    rating BOOLEAN NOT NULL,
    PRIMARY KEY(user_id, comment_id)
);

-- R11
CREATE TABLE report(
    id SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    published_date TIMESTAMP WITH TIME zone DEFAULT now() NOT NULL,
    user_id INTEGER REFERENCES member(id) ON UPDATE CASCADE ON DELETE CASCADE,
    comment_id INTEGER REFERENCES comments(id) ON UPDATE CASCADE ON DELETE CASCADE,
    story_id INTEGER REFERENCES stories(id) ON UPDATE CASCADE ON DELETE CASCADE,
    constraint only_one_value 
        check (        (story_id is null or comment_id is null) 
               and not (story_id is null and comment_id is null) )
);

---------------------------------
-- Indexes
---------------------------------

-- IDX11
CREATE INDEX comments_full_text ON comments USING GIST(to_tsvector('english', content));

-- IDX12
CREATE INDEX stories_title_full_text ON stories USING GIST(to_tsvector('english', title));

-- IDX01
CREATE INDEX topics_stories ON belong_tos(topic_id);

-- IDX02
CREATE INDEX user_comments_rating ON rates_comments USING btree(user_id, comment_id);

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
CREATE FUNCTION check_comments_rate() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF EXISTS (SELECT * FROM rates_comments WHERE rates_comments.user_id = NEW.user_id 
                                           AND rates_comments.comment_id = NEW.comment_id 
                                           AND rates_comments.rating = NEW.rating) THEN
        RAISE EXCEPTION 'A user cannot up or downvote the same comments more than once.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE PLPGSQL;

CREATE TRIGGER check_comments_rate
    BEFORE INSERT ON rates_comments
    FOR EACH ROW
    EXECUTE PROCEDURE check_comments_rate();


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
    BEFORE INSERT ON rates_stories
    FOR EACH ROW
    EXECUTE PROCEDURE check_stories_rate();


-- TRIGGER03
CREATE FUNCTION insert_story_rating() RETURNS TRIGGER AS
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

CREATE TRIGGER insert_story_rating
    AFTER INSERT ON rates_stories
    FOR EACH ROW
    EXECUTE PROCEDURE insert_story_rating();


-- TRIGGER04
CREATE FUNCTION update_story_rating() RETURNS TRIGGER AS
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

CREATE TRIGGER update_story_rating
    AFTER UPDATE ON rates_stories
    FOR EACH ROW
    EXECUTE PROCEDURE update_story_rating();

-- TRIGGERXX
CREATE FUNCTION remove_story_rating() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF OLD.rating IS FALSE THEN
        UPDATE stories
        SET rating = rating + 1 WHERE OLD.story_id = stories.id;
    ELSIF OLD.rating IS TRUE THEN
        UPDATE stories
        SET rating = rating - 1 WHERE OLD.story_id = stories.id;
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER remove_rating
    AFTER DELETE ON rates_stories
    FOR EACH ROW
    EXECUTE PROCEDURE remove_story_rating();


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

INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Speedy','Joao Monteiro','joaomonteiromail@gmail.com','$2y$10$o4m48a2MwRmoUlTx7LMzzeC1y8L8TpM0oq/c.Ri4EB7kKY5gyMoV2',TRUE);
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Goli','Gonçalo Oliveira','up201705494@fe.up.pt','$2y$10$eX5ZufMjQ2w0GdcgxFQL/.KeM56YVpuEdKo/iI6qEJyAi3Oe/abvW',TRUE);
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Ferreira','Joana Ferreira','up201705722@fe.up.pt','$2y$10$xf6EvUH70/hgH/qKQfw6euQ8Qd1vYYfRqZwkoit9TL6kYBNIdqq2e',TRUE);
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('JMatos','Joao Matos','up201705471@fe.up.pt','$2y$10$8uF0IkoWinraIyFNjaqpZu2GN8wLY390wc1UIM3K43lEbHQe4hvK6',TRUE);
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('mr_bubble','James Bubble','bubbleJ@gmail.com','$2y$10$U9NdpiBh8nrMFcVobD700ukGKDX03UbjRuCnu5XEYULvB8k8t09Ba',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('jj_R','Randall','jjrandal@gmail.com','$2y$10$HHY8nS7Rt/ZhPwjf35YiX.8BO4yR0QwcdxB/Xu7ZHIzhHvRWHlu3y',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('shadowmose','Miranda','shadowmose@gmail.com','$2y$10$XLWdN8EX6NTxh5QJ5e8YieXNplP2CY3gkCDBbAs9egsOv0f2zR2di',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('SovietWomble','Soviet Womble','sovietwomble@gmail.com','$2y$10$2l4zxqXXNVD4WLZ3ptlYjeaYwijh3C0JH3az0wm6oaGaosEM7PPIK',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('kerni86','Erny','kerni86@gmail.com','$2y$10$L48WPcAgl67AY3rqEAQ25e/9CFaBU6kq3n6uN8yeUJBFpclcQURR6',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('remiliascarlett','Remilia Scarlett','remiliascarlett@gmail.com','$2y$10$29x90Rcm5qk3N0qExR9G1.pRhLUeQudKqSf4M8Kr85fmdY4UKkhNq',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Hououin','Rintaro Okabe','secretlab@gmail.com','$2y$10$O/v6VDxRR8f0wQjz.6tkTu4CrGkvoixQs1WcBkg7VNEJ4fSpKfEWa',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('SortaMaliciousGaming','Ryan Haywood','ryanhay@gmail.com','$2y$10$3mDv6I7mB5ujFFyJ/es95ekjOUfUgpiCkt2AYKOJtnmAqR69wKdHC',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('dat_cat_guy','Cat Man','datcatguy@gmail.com','$2y$10$9g.UNl9iDS6Oj0Kwo8yewOXIIhpEVWeJ21m8DDrFE2vlA/X6dXp4i',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Gladiator','My name is Maximus Decimus Meridius, commander of the Armies of the North, General of the Felix Legions, loyal servant to the true emperor, Marcus Aurelius. Father to a murdered son, husband to a murdered wife. And I will have my vengeance, in this life or the next','romangladiator@gmail.com','$2y$10$MxtnK45fxW63S2j8WaYtsOwAWYFlyf9K90NJUoa73raHitOCsF56m',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('benyhild','Ben Hild','benyhild@gmail.com','$2y$10$YGo4L9ROZwwgjZ6TTqt5eezY78w.qUbysV6OMExAq8lpBJkcX7SnO',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('gearlessjoe','Joe Gears','gearlessjoe@gmail.com','$2y$10$wVhGjzJCYNzYWjMhP5qb9OKNoreFxvAV5Pa5ru2BaR91Oi34mklbS',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('maria_of_pees','Maria Pee','mariapess@gmail.com','$2y$10$dEDEa20cJB6ip9OxoxBWpe18JiI2iP1vbTj6OmAlQRmEL6M6OCIEq',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('enterthenick','Nick','thenick@gmail.com','$2y$10$VZqih.PBM1.gvDg8jvhGWuJX2MWWzQwR1VhgGg8whJgrDc.J5SgXK',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('1987_azrael','Azrael','az87el@gmail.com','$2y$10$zQs/dj2dhhcM9EjY26uDdugpdQcjxiwMxfjLeGXSCnCcOLyd8Kr52',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Emiru','Emiru','emiru@gmail.com','$2y$10$8ohI6IoYqjUb4PDyBN/zA.DAAftBp3t6.clCxzDGRFGxydEZxtk1q',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Jett','Joan Jett','joanj@gmail.com','$2y$10$7B/Ez4ikZbf7vEUaA1fpK.940PqEjDRo13uBLXIYVct1YpCIYr5MG',FALSE);
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('rackun','Rocky Racoon','rackun@gmail.com','$2y$10$Qj1T/Qbbjtrcy0qJBiCmieJx1iY6qN3IOwa5.7KvE6U88VM.3Lg0W',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('plantburger','Andrew Adams','plantburguer@gmail.com','$2y$10$RvRdooxL.lNfmiLM/qDDuumBNCuTzF3UBm4tOBefuSR36Qwnx5hV2',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('jpeg','Adams Andrew','jpeg@gmail.com','$2y$10$7mGdqTVXDGj687SZt4ZoIe1hPHsBUNC/wiEVlKk//mlBvUrv6xWRW',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('chadsong','Chad Smith','chadsong@gmail.com','$2y$10$zcro.4Ez8yY.AyRI0jzv2OnXqoBTtsOxnZjxsR3pIL5B46BLVmYni',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('jetmechanic','Jet Grousum','jetmechanic@gmail.com','$2y$10$HzSSUZcnxclFqAMAb4HygeFwFulSJPMGA9XOWAAKvf2VKaTJFnUwm',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('v3ldrin','Ainsley','veldrin@gmail.com','$2y$10$y5PW2jHxLl6zv/dAHDeugeOkoYy/PNc2w.qnOTeQUuWtfojNgHOD.',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('Schrute','Dwight Schrute','dschrute@gmail.com','$2y$10$pX149vZG/VvTLJloUzLW9OIYVFJQd2UNQ6hdzkNLLHWst/G9a3jD2',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('orangegirl','Barbara','orangegirl@gmail.com','$2y$10$SDX3O3so8EPRbVq8zaPaDeRTxbG2tOABfdoI23IlVIAWxQgEnMTwS',FALSE);
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('TheElon','Elon Musk','elon@spacex.com','$2y$10$FLPk2tovTu6E7AGyvhkVMuLrXRWh6YP7Mh/ZtsVLvqO1SqKFhczQ6',FALSE); 
INSERT INTO "member" (username,name,email,password,is_admin) VALUES ('enricoalois','Enrique Vasques','enricalois@gmail.com','$2y$10$hz/.oXLRFv.AgfxVlGyG7u3PEeU/EvSXywf/rDeSKL1pD6c30dReq',FALSE);  


INSERT INTO "topics" (name,creation_date) VALUES ('politics','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('feelthebern','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('portugal','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('coronavirus','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('economy','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('sports','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('forest','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('famousPeople','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('japan','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('war','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('polution','2020-03-24');
INSERT INTO "topics" (name,creation_date) VALUES ('nature','2020-03-24');


INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Trump is at it again',1,'2020-03-24',0.74);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Putin is the best',28,'2020-03-24',1);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Biden and Bernie face off',7,'2020-03-24',0.5);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Sanders wins Democrats Abroad primary',3,'2020-03-24',0.9);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Estado de Emergencia',4,'2020-03-24',1);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Cristiano Ronaldo marca na própria baliza',10,'2020-03-24',0.1);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Deaths in Italy pass the thousands',25,'2020-03-24',0.8);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','When will the vacine come?',13,'2020-03-24',0.5);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Rich people have more money than poor people',20,'2020-03-24',0.7);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Stock exchange crashes again',16,'2020-03-24',0.2);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Olimpic Games canceled?',2,'2020-03-24',0.7);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Could NHL implement compliance buyouts following COVID-19 shutdown?',26,'2020-03-24',0.4);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Australian bushfires',27,'2020-03-24',0.85);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Indian forest regrowing',30,'2020-03-24',0.24);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Jean Jett launches new album',19,'2020-03-24',0.1);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Kylie Jenner realeses new sex tape', 20,'2020-03-24',0.9);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Japan is the spring is the most beautiful place',1,'2020-03-24',1);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Old samurai ruin discovered',19,'2020-03-24',0.5);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Potential war between USA and Iraq',5,'2020-03-24',0.79);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','What started WW2?',8,'2020-03-24',0.6);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Polution levels decrease',4,'2020-03-24',0.9);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','The most poluted river in the world',14,'2020-03-24',0.4);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Extincted species resurfaces',31,'2020-03-24',0.4);
INSERT INTO "stories" (url, title,author_id,published_date,reality_check) VALUES ('www.example.com','Frogs!',24,'2020-03-24',1);


INSERT INTO "belong_tos" (story_id, topic_id) VALUES (1,1);
INSERT INTO "belong_tos" (story_id, topic_id) VALUES (1,2);
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


INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('What he do this time?',20,'2020-03-24',null,1);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('You dont know?',21,'2020-03-24',1,null);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Phasellus in tellus placerat, commodo ligula eu, tempor ante. Etiam.',14,'2020-03-24',null,2);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Donec mi purus, facilisis sit amet orci ac, semper efficitur.',25,'2020-03-24',null,3);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Lorem ipsum dolor sit amet, consectetur adipiscing.',3,'2020-03-24',4,null);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Donec non luctus risus, nec finibus nunc.',10,'2020-03-24',4,null);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Pellentesque pulvinar orci sed pellentesque vulputate. Fusce.',11,'2020-03-24',null,5);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Nulla sit amet pharetra odio. Pellentesque suscipit.',31,'2020-03-24',null,5);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Nulla blandit nunc sit amet leo auctor.',26,'2020-03-24',null,7);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Suspendisse potenti. In molestie iaculis ipsum, sed.',22,'2020-03-24',9,null);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('In vulputate velit sit amet nisi gravida.',19,'2020-03-24',10,null);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Sed eget dolor magna. Cras dapibus justo.',4,'2020-03-24',10,null);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Donec aliquam ipsum id risus convallis maximus.',7,'2020-03-24',null,9);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('In vel mollis arcu, in cursus risus.',7,'2020-03-24',null,9);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Integer consectetur metus in rhoncus aliquet. Integer.',21,'2020-03-24',null,9);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Mauris volutpat eros eu posuere vestibulum. Vivamus.',13,'2020-03-24',null,12);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Nullam et lectus gravida, maximus magna sit.',17,'2020-03-24',16,null);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Fusce vel libero a leo ullamcorper interdum.',25,'2020-03-24',16,null);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Proin sed vulputate elit, a convallis nunc.',30,'2020-03-24',null,15);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Vestibulum ante ipsum primis in faucibus orci.',1,'2020-03-24',19,null);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Praesent tempor odio id tempus sodales. Nam.',9,'2020-03-24',null,18);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Sed convallis varius odio, lacinia egestas dui.',2,'2020-03-24',null,19);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Praesent eleifend urna et mauris sodales lacinia.',5,'2020-03-24',null,19);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Etiam rhoncus porta commodo. Praesent elementum diam.',15,'2020-03-24',null,21);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Curabitur feugiat mauris ut dolor fermentum imperdiet.',2,'2020-03-24',null,23);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Nam tortor leo, bibendum at condimentum quis.',2,'2020-03-24',25,null);
INSERT INTO "comments" (content,author_id,published_date,comment_id,story_id) VALUES ('Aliquam sed dolor dui. Vestibulum condimentum lorem.',20,'2020-03-24',26,null);


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


INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (21,1,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (20,2,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (5,3,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (21,4,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (8,5,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (14,6,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (19,7,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (21,8,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (31,9,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (2,10,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (15,11,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (27,12,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (8,13,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (10,14,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (14,15,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (3,16,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (7,17,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (15,18,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (20,19,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (21,20,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (29,21,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (22,22,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (25,23,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (5,24,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (15,25,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (18,26,TRUE);
INSERT INTO "rates_comments" (user_id, comment_id, rating) VALUES (10,27,TRUE);


INSERT INTO "report" (content,published_date,user_id,comment_id,story_id) VALUES ('Explicit content','2020-03-24',15,null,24);
INSERT INTO "report" (content,published_date,user_id,comment_id,story_id) VALUES ('Racism','2020-03-24',12,12,null);
INSERT INTO "report" (content,published_date,user_id,comment_id,story_id) VALUES ('Inappropriate language','2020-03-24',20,13,null);
INSERT INTO "report" (content,published_date,user_id,comment_id,story_id) VALUES ('Inappropriate language','2020-03-24',20,14,null);
INSERT INTO "report" (content,published_date,user_id,comment_id,story_id) VALUES ('Agressive','2020-03-24',30,null,8);