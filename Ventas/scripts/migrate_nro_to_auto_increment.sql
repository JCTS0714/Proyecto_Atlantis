-- Migration script: convert 'nro' to INT UNSIGNED AUTO_INCREMENT
-- WARNING: Backup your table before running.
-- Steps (safe path):
-- 1) Add a new temporary integer column
ALTER TABLE `contador` ADD COLUMN `nro_tmp` INT UNSIGNED DEFAULT NULL;

-- 2) Populate nro_tmp from existing nro (attempt cast), nulls -> 0
UPDATE `contador` SET nro_tmp = CAST(nro AS UNSIGNED);

-- 3) If any nro_tmp is 0 and you want them sequential, set them using variables
SET @n = (SELECT COALESCE(MAX(nro_tmp),0) FROM `contador`);
UPDATE `contador` SET nro_tmp = (@n := @n + 1) WHERE nro_tmp = 0 ORDER BY id;

-- 4) Make sure values are unique
-- If duplicates exist, you must resolve them manually before continuing
SELECT nro_tmp, COUNT(*) AS cnt FROM `contador` GROUP BY nro_tmp HAVING cnt > 1;

-- 5) When ready, drop old column and rename tmp to nro, then set AUTO_INCREMENT
ALTER TABLE `contador` DROP COLUMN `nro`;
ALTER TABLE `contador` CHANGE COLUMN `nro_tmp` `nro` INT UNSIGNED NOT NULL;

-- 6) Set nro as UNIQUE and AUTO_INCREMENT (only possible if there's a PRIMARY key or unique index and not conflicting values)
ALTER TABLE `contador` ADD UNIQUE (`nro`);
-- Set the next auto_increment to max(nro)+1
SET @maxn = (SELECT COALESCE(MAX(nro),0) FROM `contador`);
SET @next = @maxn + 1;
ALTER TABLE `contador` MODIFY COLUMN `nro` INT UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = @next;

-- End of migration. Verify the table content after running.
