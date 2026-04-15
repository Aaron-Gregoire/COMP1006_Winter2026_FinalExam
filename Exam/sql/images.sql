CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    image_title VARCHAR(255) NOT NULL,
    user_id INT,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);