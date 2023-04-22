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
  nickname VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE difficulty
(
  id INT PRIMARY KEY,
  description VARCHAR(255) NOT NULL UNIQUE,
  n_words INT NOT NULL UNIQUE
);

INSERT INTO difficulty VALUES
  (1, "Easy", 20),
  (2, "Medium", 40),
  (3, "Difficult", 60);

CREATE TABLE game
(
  id INT PRIMARY KEY AUTO_INCREMENT,
  id_user INT NOT NULL,
  id_difficulty INT NOT NULL,
  datetime DATETIME NOT NULL DEFAULT NOW(),
  FOREIGN KEY (id_user) REFERENCES user(id),
  FOREIGN KEY (id_difficulty) REFERENCES difficulty(id)
);
