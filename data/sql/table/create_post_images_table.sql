CREATE TABLE IF NOT EXISTS post_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    image_id INT NOT NULL,
    FOREIGN KEY (post_id) 
        REFERENCES posts(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (image_id) 
        REFERENCES images(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- PRIMARY KEY (post_id, image_id), 
--     FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE,
--     FOREIGN KEY (image_id) REFERENCES images (id) ON DELETE CASCADE