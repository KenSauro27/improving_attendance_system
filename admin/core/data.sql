CREATE TABLE attendance_system_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR (255),
    password TEXT,
    first_name VARCHAR (255),
    last_name VARCHAR (255),
    is_admin BOOL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    sender_id INT,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES attendance_system_users(user_id)
);