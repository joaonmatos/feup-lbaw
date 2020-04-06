-- T01
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;
    
    INSERT INTO "story" (title,author_id,published_date,reality_check,rating) VALUES ('beleza',1,'2020-04-04',0.74,0);


    INSERT INTO "belongs_to" (story_id, topic_id) VALUES (lastval(), 1);
    INSERT INTO "belongs_to" (story_id, topic_id) VALUES (lastval(), 2);
    INSERT INTO "belongs_to" (story_id, topic_id) VALUES (lastval(), 3);

COMMIT;

-- T02
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;



COMMIT;
