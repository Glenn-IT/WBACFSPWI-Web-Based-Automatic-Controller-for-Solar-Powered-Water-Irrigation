-- Adds forgot-password security question/answer support to an existing users table.
-- Safe to run once against a database created before this migration existed.

ALTER TABLE users
    ADD COLUMN security_question VARCHAR(50) NULL AFTER is_active,
    ADD COLUMN security_answer_hash VARCHAR(255) NULL AFTER security_question;
