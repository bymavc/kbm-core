<?php

$host = 'localhost';
$user = 'root';
$pass = '';

$query_create_db = "CREATE DATABASE IF NOT EXISTS kbmdb CHARACTER SET utf8 COLLATE utf8_spanish2_ci;";
$query_create_tables = "USE kbmdb;

CREATE TABLE IF NOT EXISTS user (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
  username VARCHAR(20) NOT NULL UNIQUE,
  email VARCHAR(50) NOT NULL UNIQUE,
  password VARBINARY(250) NOT NULL,
  first_name VARCHAR(20) NOT NULL,
  last_name VARCHAR(20) NOT NULL,
  status TINYINT DEFAULT 3,
  profile_picture VARCHAR(140) DEFAULT NULL
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS user_register (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user INT NOT NULL,
  date DATETIME NOT NULL,
  description VARCHAR(70) NOT NULL,
  FOREIGN KEY (user) REFERENCES user(id)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS code (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user INT NOT NULL,
  code VARCHAR(10) NOT NULL,
  type TINYINT NOT NULL,
  date DATETIME NOT NULL,
  CONSTRAINT UC_User_Type UNIQUE (user, type)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS knowledge_base (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
  name VARCHAR(70) NOT NULL,
  description VARCHAR(140) NOT NULL DEFAULT 'No description',
  privacy TINYINT NOT NULL DEFAULT 1,
  status TINYINT NOT NULL DEFAULT 1
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS folder (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
  knowledge_base INT NOT NULL,
  parent_folder INT DEFAULT NULL,
  name VARCHAR(20) NOT NULL DEFAULT 'UNNAMED FOLDER',
  status TINYINT NOT NULL DEFAULT 1,
  FOREIGN KEY (knowledge_base) REFERENCES knowledge_base(id),
  FOREIGN KEY (parent_folder) REFERENCES folder(id),
  CONSTRAINT UC_Name_Folder UNIQUE (parent_folder, name)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS document (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
  knowledge_base INT NOT NULL,
  folder INT NOT NULL,
  name VARCHAR(70) NOT NULL DEFAULT 'UNNAMED DOCUMENT',
  description VARCHAR(140) NOT NULL DEFAULT 'No description',
  content TEXT NOT NULL,
  status TINYINT NOT NULL DEFAULT 1,
  FOREIGN KEY (knowledge_base) REFERENCES knowledge_base(id),
  FOREIGN KEY (folder) REFERENCES folder(id),
  CONSTRAINT UC_Name_Folder UNIQUE (folder, name)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS register (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  knowledge_base INT NOT NULL,
  folder INT DEFAULT NULL,
  document INT DEFAULT NULL,
  user INT NOT NULL,
  date DATETIME NOT NULL,
  description VARCHAR(70) NOT NULL,
  FOREIGN KEY (knowledge_base) REFERENCES knowledge_base(id),
  FOREIGN KEY (folder) REFERENCES folder(id),
  FOREIGN KEY (document) REFERENCES document(id),
  FOREIGN KEY (user) REFERENCES user(id)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS tag (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
  name VARCHAR(20) NOT NULL UNIQUE,
  status TINYINT NOT NULL DEFAULT 1
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS document_tag (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY UNIQUE,
  document INT NOT NULL,
  tag INT NOT NULL,
  FOREIGN KEY (document) REFERENCES document(id),
  FOREIGN KEY (tag) REFERENCES tag(id),
  CONSTRAINT UC_Document_Tag UNIQUE (document, tag)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS permission (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user INT NOT NULL,
  knowledge_base INT NOT NULL,
  role TINYINT NOT NULL,
  FOREIGN KEY (user) REFERENCES user(id),
  FOREIGN KEY (knowledge_base) REFERENCES knowledge_base(id),
  CONSTRAINT UC_Permission UNIQUE (user, knowledge_base)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS auth (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  token VARBINARY(250) NOT NULL UNIQUE,
  user INT NOT NULL,
  date DATETIME NOT NULL
) ENGINE = InnoDB;";

try {
  $conn = new PDO('mysql:host=' . $host, $user, $pass);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $conn->exec('set names utf8');

  $conn->exec($query_create_db);
  echo "Database created successfully<br>";

  $conn->exec($query_create_tables);
  echo "Tables created successfully<br>";

} catch (PDOException $e){
  echo $sql . $e->getMessage();
}

