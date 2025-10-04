-- MySQL initialization script
-- This runs when the container is first created

-- Set default character set
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Grant privileges (already done by environment variables, but good to be explicit)
GRANT ALL PRIVILEGES ON hikethere.* TO 'hikethere_user'@'%';
FLUSH PRIVILEGES;

-- You can add any additional initialization SQL here
-- For example, creating additional users or setting specific configurations
