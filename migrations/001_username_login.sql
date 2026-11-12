-- Back up your database before applying this migration.
-- For existing installations only. Run once. Do not rerun.
-- Existing account usernames become user<ID> (for example user1).
-- Existing passwords and historical sales remain unchanged.
ALTER TABLE users ADD COLUMN username VARCHAR(40) NULL AFTER name;
UPDATE users SET username=CONCAT('user',id) WHERE username IS NULL;
ALTER TABLE users MODIFY COLUMN username VARCHAR(40) NOT NULL;
ALTER TABLE users ADD UNIQUE KEY uq_users_username (username);
ALTER TABLE users MODIFY COLUMN email VARCHAR(190) NULL;
