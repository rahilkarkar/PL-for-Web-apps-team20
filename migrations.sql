-- JukeBoxed Database Schema
-- Run this file to set up all necessary tables

CREATE TABLE IF NOT EXISTS jukeboxd_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Songs table
CREATE TABLE IF NOT EXISTS songs (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    album VARCHAR(255),
    release_year INT,
    genre VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES jukeboxd_users(id) ON DELETE CASCADE,
    song_id INT NOT NULL REFERENCES songs(id) ON DELETE CASCADE,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Listen List (Wishlist) table
CREATE TABLE IF NOT EXISTS listen_list (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES jukeboxd_users(id) ON DELETE CASCADE,
    song_id INT NOT NULL REFERENCES songs(id) ON DELETE CASCADE,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, song_id)
);

-- Followers table (for Part C - follow system)
CREATE TABLE IF NOT EXISTS followers (
    id SERIAL PRIMARY KEY,
    follower_id INT NOT NULL REFERENCES jukeboxd_users(id) ON DELETE CASCADE,
    following_id INT NOT NULL REFERENCES jukeboxd_users(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(follower_id, following_id),
    CHECK (follower_id != following_id)
);

-- Insert some sample songs for testing
INSERT INTO songs (title, artist, album, release_year, genre) VALUES
('COMFORT ME', 'Malcom Todd', 'Comfort Me', 2021, 'R&B'),
('INTIMIDATED', 'Kaytranada, H.E.R', 'BUBBA', 2019, 'Electronic'),
('HMU', 'Greek', 'HMU', 2020, 'Pop'),
('LADY', 'Avenoir', 'Lady', 2021, 'Indie'),
('Vie', 'Doja Cat', 'Planet Her', 2021, 'Pop'),
('Jealous Type', 'Doja Cat', 'Planet Her', 2021, 'Pop'),
('ORENJI', 'Various Artists', 'ORENJI', 2020, 'Electronic'),
('Blinding Lights', 'The Weeknd', 'After Hours', 2020, 'Synth-pop'),
('Levitating', 'Dua Lipa', 'Future Nostalgia', 2020, 'Disco-pop'),
('Good Days', 'SZA', 'Good Days', 2020, 'R&B')
ON CONFLICT DO NOTHING;
