-- Fix tbl_user table to add AUTO_INCREMENT to id field
-- Run this SQL script in your database

-- First, check if the table exists and get current structure
-- Then modify the id field to be AUTO_INCREMENT

ALTER TABLE `tbl_user` MODIFY COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT;

-- If the above doesn't work, try this:
-- ALTER TABLE `tbl_user` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;

-- Verify the change
-- DESCRIBE `tbl_user`;

