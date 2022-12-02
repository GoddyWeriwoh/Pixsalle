SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE IF NOT EXISTS `pixsalle`;
USE `pixsalle`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`
(
    `id`        INT                                                     NOT NULL AUTO_INCREMENT,
    `email`     VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `password`  VARCHAR(255)                                            NOT NULL,
    `createdAt` DATETIME                                                NOT NULL,
    `updatedAt` DATETIME                                                NOT NULL,
    `phone`     VARCHAR(255),
    `photo`     VARCHAR(255),
    `username`  VARCHAR(255),
    `membership`    VARCHAR(255),
    `balance`   FLOAT,
    `portfolio`  VARCHAR(255),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pictures`;
CREATE TABLE `pictures`
(
    `id`        INT             NOT NULL AUTO_INCREMENT,
    `usrId`     INT             NOT NULL,
    `createdAt` DATETIME        NOT NULL,
    `url`       VARCHAR(255)    NOT NULL,                                         
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `blogs`;
CREATE TABLE `blogs`
(
    `id`      INT             NOT NULL AUTO_INCREMENT,
    `userId`     INT             NOT NULL,
    `title`        VARCHAR(255)     NOT NULL ,
    `content`     VARCHAR(255)             NOT NULL,
    `author` VARCHAR(255) ,
    `createdAt` DATETIME        NOT NULL,
    `updatedAt` DATETIME        NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;