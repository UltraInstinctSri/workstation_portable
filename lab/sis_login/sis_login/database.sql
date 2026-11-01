-- Run this once:  mysql -u root -p < database.sql
-- Minimal tables needed by the Registration/Login system (subset of the Lab-VI schema).

CREATE DATABASE IF NOT EXISTS sis_login CHARACTER SET utf8mb4;
USE sis_login;

CREATE TABLE IF NOT EXISTS Student (
    Student_ID VARCHAR(30)  PRIMARY KEY,
    First_Name VARCHAR(50)  NOT NULL,
    Last_Name  VARCHAR(50)  NOT NULL,
    Email      VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Faculty (
    Faculty_ID VARCHAR(30)  PRIMARY KEY,
    First_Name VARCHAR(50)  NOT NULL,
    Last_Name  VARCHAR(50)  NOT NULL,
    Email      VARCHAR(100) NOT NULL UNIQUE
);

-- User_ID holds a Student_ID or a Faculty_ID (so it cannot be a real FK to both tables);
-- User_Type tells which table it belongs to.
CREATE TABLE IF NOT EXISTS Login (
    Login_ID      INT AUTO_INCREMENT PRIMARY KEY,
    User_ID       VARCHAR(30) NOT NULL UNIQUE,
    User_Type     ENUM('Student','Faculty') NOT NULL,
    Username      VARCHAR(30) NOT NULL UNIQUE,
    Password_Hash CHAR(64)    NOT NULL,   -- SHA-256 hex digest of (salt + password)
    Random_salt   CHAR(32)    NOT NULL,   -- 16 random bytes, hex encoded
    Last_Login    DATETIME    NULL
);
