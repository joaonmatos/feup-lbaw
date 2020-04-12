-- T01
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;
    
    INSERT INTO "story" (title, author_id, published_date, reality_check, rating) 
        VALUES ($title, $author_id, $published_date, $reality_check, $rating);

    INSERT INTO "belongs_to" (story_id, topic_id) VALUES (lastval(), $topic1);
    INSERT INTO "belongs_to" (story_id, topic_id) VALUES (lastval(), $topic2);
    INSERT INTO "belongs_to" (story_id, topic_id) VALUES (lastval(), $topic3);

COMMIT;
