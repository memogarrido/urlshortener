# urlshortener
Practice and learning PHP REST project to  shorten URLs
## Creating DB 
```sql
create database urlshortener;
CREATE TABLE `urlshortener`.`links` (
  `hash` VARCHAR(6) NOT NULL,
  `url_orig` VARCHAR(2083) NOT NULL,
  `creation_date` DATETIME NOT NULL,
  PRIMARY KEY (`hash`),
  UNIQUE INDEX `hash_UNIQUE` (`hash` ASC));

ALTER SCHEMA `urlshortener`  DEFAULT CHARACTER SET utf8mb4  DEFAULT COLLATE utf8mb4_bin ;

ALTER TABLE `urlshortener`.`links` 
CHANGE COLUMN `hash` `hash` VARCHAR(6) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ;

```

