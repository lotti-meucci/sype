-- MariaDB

CREATE DATABASE IF NOT EXISTS sype;
USE sype;

CREATE TABLE IF NOT EXISTS word
(
  id INT PRIMARY KEY AUTO_INCREMENT,
  text VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS user
(
  id INT PRIMARY KEY AUTO_INCREMENT,
  nickname VARCHAR(20) NOT NULL UNIQUE,
  hash VARCHAR(255) NOT NULL,
  picture_uri VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS difficulty
(
  id INT PRIMARY KEY,
  description VARCHAR(255) NOT NULL UNIQUE,
  words_n INT NOT NULL UNIQUE
);

INSERT IGNORE INTO difficulty VALUES
  (1, "Easy", 20),
  (2, "Medium", 40),
  (3, "Difficult", 60);

CREATE TABLE IF NOT EXISTS game
(
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  difficulty_id INT NOT NULL,
  datetime DATETIME NOT NULL DEFAULT NOW(),
  result FLOAT NOT NULL,
  errors_n INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES user(id),
  FOREIGN KEY (difficulty_id) REFERENCES difficulty(id)
);
