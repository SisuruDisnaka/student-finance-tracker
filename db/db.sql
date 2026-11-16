CREATE DATABASE IF NOT EXISTS student_finance_db
CHARACTER SET utf8mb4;

USE student_finance_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password)
VALUES ('Demo Student', 'demo@student.lk', '$2y$10$9HP4OIx7VBpB/OX1qVYE1ueWtnHtRNGQsA6m5GTGOb7e/eOPSTw.a');