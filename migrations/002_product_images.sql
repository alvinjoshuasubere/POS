-- Existing installations only: back up your database, then run once.
ALTER TABLE products ADD COLUMN image_path VARCHAR(255) NULL AFTER sku;
