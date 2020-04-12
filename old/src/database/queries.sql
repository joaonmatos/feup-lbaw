--Get topics with the most amount of stories
SELECT topic_id, name, COUNT(topic_id) as "occurrence"
FROM "belongs_to", "topic"
WHERE belongs_to.topic_id=topic.id
GROUP BY topic_id, name
ORDER BY "occurrence" DESC

--Get stories per topic (Search)
SELECT story.id, title, reality_check, author_id, published_date
FROM "belongs_to", "story"
WHERE topic_id=$topicId and belongs_to.story_id=story.id 
ORDER BY $orderCriteria DESC

--Get friends
SELECT friend_id, username
FROM "member", "follows"
WHERE follows.user_id=$userId
and follows.friend_id=member.id

--Get comments per story
SELECT *
FROM "comment"
WHERE story_id=$storyId

--Get replies to comments
SELECT *
FROM "comment"
WHERE comment_id=$commentId

--Great favourite topics per user
SELECT topic.id, topic.name
FROM "favourites", "topic"
WHERE user_id=$userId
	AND favourites.topic_id=topic.id

--Get user feed 
DROP VIEW IF EXISTS favourite_topics;
CREATE VIEW favourite_topics
AS
SELECT topic.id as topicId, topic.name as topicName
FROM "favourites", "topic"
WHERE user_id=$userId
	AND favourites.topic_id=topic.id;
	
SELECT topicId, topicName, story.id, title, author_id, member.username, published_date, reality_check
FROM "favourite_topics", "story", "belongs_to", "member"
WHERE topic_id=favourite_topics.topicId
	AND belongs_to.story_id=story.id 
	AND story.author_id= member.id
	ORDER BY $orderCriteria DESC

--Get general feed
SELECT id, rating/extract(day from (NOW()-published_date)*86400*1000) / 1000 as priority
FROM "story"
ORDER BY priority DESC

--Get rating for user and story
SELECT rating
FROM "rates_story"
WHERE user_id=$userId 
	AND story_id=$storyId

--Get rating for user and comment
SELECT rating
FROM "rates_comment"
WHERE user_id=$userId 
	AND comment_id=$commentId

--Full text
SELECT story.title, topic.name
FROM "story", "belongs_to", "topic", 
(
	SELECT story.id as storyId, to_tsvector(story.title) as vector
	FROM "story"
) titles
WHERE story.id=belongs_to.story_id
	AND story.id= titles.storyId
	AND belongs_to.topic_id=topic.id
	AND (titles.vector @@ to_tsquery('trump')
	OR topic.name ILIKE '%politics%')