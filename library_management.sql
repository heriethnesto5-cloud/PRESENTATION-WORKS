CREATE DATABASE IF NOT EXISTS library_management
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE library_management;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS authors (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  author_name VARCHAR(150) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS publishers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  publisher_name VARCHAR(150) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS books (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  isbn VARCHAR(30) NULL UNIQUE,
  title VARCHAR(255) NOT NULL,
  category_id INT UNSIGNED NULL,
  author_id INT UNSIGNED NULL,
  publisher_id INT UNSIGNED NULL,
  publication_year SMALLINT UNSIGNED NULL,
  total_copies INT UNSIGNED NOT NULL DEFAULT 1,
  available_copies INT UNSIGNED NOT NULL DEFAULT 1,
  shelf_location VARCHAR(100) NULL,
  description TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_books_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_books_author FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE SET NULL,
  CONSTRAINT fk_books_publisher FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS members (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  member_number VARCHAR(50) NOT NULL UNIQUE,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NULL UNIQUE,
  phone VARCHAR(30) NULL,
  member_type ENUM('Student','Staff','External') NOT NULL DEFAULT 'Student',
  registration_date DATE NOT NULL,
  status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(150) NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('Admin','Librarian') NOT NULL DEFAULT 'Librarian',
  status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS loans (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  member_id INT UNSIGNED NOT NULL,
  book_id INT UNSIGNED NOT NULL,
  borrowed_date DATE NOT NULL,
  due_date DATE NOT NULL,
  returned_date DATE NULL,
  status ENUM('Borrowed','Returned') NOT NULL DEFAULT 'Borrowed',
  issued_by INT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_loans_member FOREIGN KEY (member_id) REFERENCES members(id),
  CONSTRAINT fk_loans_book FOREIGN KEY (book_id) REFERENCES books(id),
  CONSTRAINT fk_loans_user FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_loans_status (status),
  INDEX idx_loans_due_date (due_date),
  INDEX idx_loans_member (member_id)
) ENGINE=InnoDB;

INSERT IGNORE INTO categories (category_name, description) VALUES
('ICT', 'Information and Communication Technology'),
('Science', 'Science and scientific subjects'),
('Mathematics', 'Mathematics and statistics'),
('Literature', 'Literature and novels'),
('Business', 'Business and management'),
('History', 'History and historical studies');
