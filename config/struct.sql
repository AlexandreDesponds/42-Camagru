-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema camagru
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `camagru` ;

-- -----------------------------------------------------
-- Schema camagru
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `camagru` DEFAULT CHARACTER SET utf8 ;
USE `camagru` ;

-- -----------------------------------------------------
-- Table `camagru`.`people`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `camagru`.`people` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pseudo` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `dateCreated` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `dateUpdated` DATETIME NULL,
  `ipCreated` VARCHAR(45) NULL,
  `ipUpdated` VARCHAR(45) NULL,
  `tokenValidated` VARCHAR(100) NULL,
  `tokenLost` VARCHAR(100) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `pseudo_UNIQUE` (`pseudo` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `camagru`.`selfie`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `camagru`.`selfie` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `dateCreated` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `ipCreated` VARCHAR(45) NULL,
  `visible` TINYINT(1) NULL DEFAULT 1,
  `people` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `people`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  INDEX `fk_selfie_people1_idx` (`people` ASC),
  CONSTRAINT `fk_selfie_people1`
    FOREIGN KEY (`people`)
    REFERENCES `camagru`.`people` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `camagru`.`comment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `camagru`.`comment` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `message` VARCHAR(1000) NOT NULL,
  `selfie` INT UNSIGNED NOT NULL,
  `people` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `selfie`, `people`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_comment_selfie1_idx` (`selfie` ASC),
  INDEX `fk_comment_people1_idx` (`people` ASC),
  CONSTRAINT `fk_comment_selfie1`
    FOREIGN KEY (`selfie`)
    REFERENCES `camagru`.`selfie` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comment_people1`
    FOREIGN KEY (`people`)
    REFERENCES `camagru`.`people` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `camagru`.`likes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `camagru`.`likes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `people` INT UNSIGNED NOT NULL,
  `selfie` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `people`, `selfie`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_likes_people1_idx` (`people` ASC),
  INDEX `fk_likes_selfie1_idx` (`selfie` ASC),
  CONSTRAINT `fk_likes_people1`
    FOREIGN KEY (`people`)
    REFERENCES `camagru`.`people` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_likes_selfie1`
    FOREIGN KEY (`selfie`)
    REFERENCES `camagru`.`selfie` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
