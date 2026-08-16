-- Kersa Secondary School Database
-- MySQL Database Schema

CREATE DATABASE IF NOT EXISTS kersa_school_db;
USE kersa_school_db;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student', 'parent') DEFAULT 'student',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- Students Table
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    user_id INT,
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    date_of_birth DATE NOT NULL,
    grade INT NOT NULL,
    section VARCHAR(10) NOT NULL,
    parent_name VARCHAR(100) NOT NULL,
    parent_phone VARCHAR(20) NOT NULL,
    parent_email VARCHAR(120),
    address TEXT,
    enrollment_date DATE NOT NULL,
    status ENUM('active', 'graduated', 'transferred', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_student_id (student_id),
    INDEX idx_grade_section (grade, section)
);

-- Teachers Table
CREATE TABLE teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id VARCHAR(20) UNIQUE NOT NULL,
    user_id INT,
    full_name VARCHAR(100) NOT NULL,
    qualification VARCHAR(100) NOT NULL,
    subject VARCHAR(50) NOT NULL,
    department VARCHAR(50),
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    hire_date DATE NOT NULL,
    status ENUM('active', 'retired', 'resigned', 'on_leave') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_teacher_id (teacher_id),
    INDEX idx_subject (subject)
);

-- Classes Table
CREATE TABLE classes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    class_name VARCHAR(50) NOT NULL,
    grade INT NOT NULL,
    section VARCHAR(10) NOT NULL,
    homeroom_teacher_id INT,
    academic_year VARCHAR(20) NOT NULL,
    capacity INT DEFAULT 40,
    room_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (homeroom_teacher_id) REFERENCES teachers(id) ON DELETE SET NULL,
    UNIQUE KEY unique_class (grade, section, academic_year)
);

-- Subjects Table
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    grade_level INT NOT NULL,
    teacher_id INT,
    period_weekly INT DEFAULT 3,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL,
    INDEX idx_subject_code (subject_code),
    INDEX idx_grade_level (grade_level)
);

-- Grades Table
CREATE TABLE grades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    class_id INT NOT NULL,
    term ENUM('first', 'second', 'third', 'final') NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    score DECIMAL(5,2),
    grade_letter CHAR(2),
    remarks TEXT,
    recorded_by INT,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES teachers(id) ON DELETE SET NULL,
    UNIQUE KEY unique_grade (student_id, subject_id, term, academic_year),
    INDEX idx_student_term (student_id, term)
);

-- Attendance Table
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'excused') NOT NULL,
    recorded_by INT,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES teachers(id) ON DELETE SET NULL,
    UNIQUE KEY unique_attendance (student_id, date, class_id),
    INDEX idx_student_date (student_id, date)
);

-- Admissions Table
CREATE TABLE admissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_no VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    date_of_birth DATE NOT NULL,
    applying_for_grade INT NOT NULL,
    parent_name VARCHAR(100) NOT NULL,
    relationship VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(120),
    previous_school VARCHAR(200) NOT NULL,
    previous_grade VARCHAR(20) NOT NULL,
    additional_info TEXT,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'accepted', 'rejected', 'waitlisted') DEFAULT 'pending',
    reviewed_by INT,
    review_date TIMESTAMP NULL,
    review_notes TEXT,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_application_no (application_no),
    INDEX idx_status (status)
);

-- News Table
CREATE TABLE news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    author VARCHAR(100) NOT NULL,
    image_url VARCHAR(255),
    publish_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('draft', 'published', 'archived') DEFAULT 'published',
    INDEX idx_category (category),
    INDEX idx_publish_date (publish_date)
);

-- Events Table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME,
    location VARCHAR(200),
    organizer VARCHAR(100),
    contact_email VARCHAR(120),
    contact_phone VARCHAR(20),
    max_participants INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    INDEX idx_event_date (event_date)
);

-- Contact Messages Table
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    replied_by INT,
    replied_at TIMESTAMP NULL,
    reply_message TEXT,
    FOREIGN KEY (replied_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Announcements Table
CREATE TABLE announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    target_audience ENUM('all', 'students', 'teachers', 'parents') DEFAULT 'all',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    posted_by INT NOT NULL,
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    status ENUM('active', 'expired', 'archived') DEFAULT 'active',
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_target_audience (target_audience),
    INDEX idx_priority (priority)
);

-- Insert Sample Data
-- Admin User
INSERT INTO users (full_name, email, username, password, role, status) VALUES
('Admin User', 'admin@kersaschool.edu.et', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- Sample Teachers
INSERT INTO teachers (teacher_id, full_name, qualification, subject, department, phone, email, hire_date, status) VALUES
('TCH001', 'Gemechu Desta', 'M.Sc. Physics', 'Physics', 'Science', '+251911111111', 'gemechu@kersaschool.edu.et', '2010-09-01', 'active'),
('TCH002', 'Tigist Haile', 'M.Ed. Mathematics', 'Mathematics', 'Mathematics', '+251922222222', 'tigist@kersaschool.edu.et', '2012-09-01', 'active'),
('TCH003', 'Hiwot Alemu', 'M.A. English', 'English', 'Languages', '+251933333333', 'hiwot@kersaschool.edu.et', '2016-09-01', 'active'),
('TCH004', 'Bekele Tadesse', 'B.A. History', 'History', 'Social Sciences', '+251944444444', 'bekele@kersaschool.edu.et', '2014-09-01', 'active'),
('TCH005', 'Dawit Girma', 'M.Sc. Computer Science', 'ICT', 'ICT', '+251955555555', 'dawit@kersaschool.edu.et', '2018-09-01', 'active');

-- Sample Students
INSERT INTO students (student_id, full_name, gender, date_of_birth, grade, section, parent_name, parent_phone, enrollment_date, status) VALUES
('STU2024001', 'Abebe Kebede', 'male', '2008-05-15', 9, 'A', 'Kebede Tadesse', '+251966666666', '2024-09-01', 'active'),
('STU2024002', 'Selam Tesfaye', 'female', '2008-08-20', 9, 'A', 'Tesfaye Haile', '+251977777777', '2024-09-01', 'active'),
('STU2024003', 'Mohammed Ahmed', 'male', '2007-03-10', 10, 'B', 'Ahmed Hassan', '+251988888888', '2023-09-01', 'active'),
('STU2024004', 'Sara Bekele', 'female', '2007-11-25', 10, 'B', 'Bekele Lemma', '+251999999999', '2023-09-01', 'active');

-- Sample News
INSERT INTO news (title, content, category, author, publish_date) VALUES
('Students Excel in National Examinations', 'Our students achieved outstanding results in the national exams with a 98% pass rate. This is the highest in the region and a testament to the hard work of our students and dedication of our teachers.', 'Academic', 'Admin User', '2024-01-15'),
('New Computer Lab Opens', 'State-of-the-art computer laboratory equipped with 50 new computers is now open for students. The lab will support our ICT curriculum and provide students with hands-on experience.', 'Announcement', 'Admin User', '2024-01-10'),
('Football Team Wins Regional Championship', 'Our school football team emerged victorious in the regional tournament, defeating strong competitors from across the region.', 'Sports', 'Admin User', '2024-01-05');
