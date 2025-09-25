CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) NOT NULL,email VARCHAR(160) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL);
CREATE TABLE IF NOT EXISTS tickets (id INT AUTO_INCREMENT PRIMARY KEY,subject VARCHAR(200) NOT NULL,body TEXT NOT NULL,status ENUM('new','in_progress','resolved') NOT NULL DEFAULT 'new',created_by INT NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY (created_by) REFERENCES users(id));
CREATE TABLE IF NOT EXISTS seo_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_path VARCHAR(255) NOT NULL,
    seo_score INT NOT NULL,
    avg_speed INT NOT NULL,
    accessibility INT NOT NULL,
    label VARCHAR(160) NOT NULL,
    analysis_type ENUM('manual_upload', 'automatic_analysis', 'bulk_analysis', 'demo') DEFAULT 'manual_upload',
    url VARCHAR(500) NULL,
    seo_issues INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);