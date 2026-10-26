-- Add profile_icon column to customers table
-- This allows users to select from 8 custom avatar icons

ALTER TABLE customers 
ADD COLUMN profile_icon INT DEFAULT NULL 
COMMENT 'Selected profile icon number (1-8), NULL for default avatar';

-- Update existing users to have no icon selected (NULL is default)
-- Users can then choose their icon from the account page
