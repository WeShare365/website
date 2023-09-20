DROP DATABASE IF EXISTS weshare;

CREATE DATABASE weshare;

USE weshare;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profilepic VARCHAR(255) DEFAULT 'Default_ProfilePic.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE uploads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fk_user_id INT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fk_user_id) REFERENCES users(id)
);

CREATE TABLE upload_links (
    fk_upload_id INT UNSIGNED NOT NULL,
    upload_hash VARCHAR(255) NOT NULL UNIQUE,
    FOREIGN KEY (fk_upload_id) REFERENCES uploads(id)
);

