-- MariaDB

DROP DATABASE IF EXISTS sype;
CREATE DATABASE sype;
USE sype;

CREATE TABLE word
(
  id INT PRIMARY KEY AUTO_INCREMENT,
  text VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE user
(
  id INT PRIMARY KEY AUTO_INCREMENT,
  nickname VARCHAR(20) NOT NULL UNIQUE,
  hash VARCHAR(255) NOT NULL,
  picture_url VARCHAR(255)
);

CREATE TABLE difficulty
(
  id INT PRIMARY KEY,
  description VARCHAR(255) NOT NULL UNIQUE,
  words_n INT NOT NULL UNIQUE
);

INSERT INTO difficulty VALUES
  (1, "Easy", 20),
  (2, "Medium", 40),
  (3, "Difficult", 60);

CREATE TABLE game
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
