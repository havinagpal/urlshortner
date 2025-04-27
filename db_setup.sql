-- Create the links table
CREATE TABLE IF NOT EXISTS links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    short_path VARCHAR(50) NOT NULL UNIQUE,
    long_url TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    click_count INT DEFAULT 0
);

-- Create index for faster lookups
CREATE INDEX idx_short_path ON links(short_path);
