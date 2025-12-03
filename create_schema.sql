-- Hypecrews Database Schema
-- Run this SQL script to set up the complete database structure

-- Create the database
CREATE DATABASE IF NOT EXISTS hypecrews;
USE hypecrews;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mobile_number VARCHAR(20) NOT NULL,
    country VARCHAR(50) NOT NULL,
    age INT NOT NULL,
    company_name VARCHAR(100),
    company_website VARCHAR(200),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    google_auth_secret VARCHAR(255),
    google_auth_enabled TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create audition_submissions table
CREATE TABLE IF NOT EXISTS audition_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    age INT NOT NULL,
    address TEXT NOT NULL,
    music_type VARCHAR(50) NOT NULL,
    experience INT NOT NULL,
    instruments VARCHAR(255),
    photo_path VARCHAR(255),
    youtube_link VARCHAR(255) NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    selected TINYINT(1) DEFAULT 0,
    selected_by_admin_id INT,
    deselected_by_admin_id INT,
    FOREIGN KEY (selected_by_admin_id) REFERENCES admins(id),
    FOREIGN KEY (deselected_by_admin_id) REFERENCES admins(id)
);

-- Insert default admin user (username: admin, password: admin123)
INSERT IGNORE INTO admins (username, password, google_auth_enabled) 
VALUES ('admin', '$2y$10$KvX.SCJ6V6gNzaF7DFBE.e.WuVoBz6SsD2kD4qlUuIa9x7ysQaq1W', 0);

-- Display success message
SELECT 'Database schema created successfully!' AS Message;