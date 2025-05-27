CREATE TABLE IF NOT EXISTS users_profile_properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    FOREIGN KEY (id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    avatar INT,
    FOREIGN KEY (id) 
        REFERENCES images(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    profile_name VARCHAR(100) NOT NULL,
    user_about VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);