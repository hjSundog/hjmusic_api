-- Created by Vertabelo (http://vertabelo.com)
-- Last modification date: 2017-06-25 06:23:08.186

-- tables
-- Table: collection
CREATE TABLE collection (
    id int NOT NULL,
    user_id int NOT NULL,
    music_id int NOT NULL,
    collect_at datetime NOT NULL,
    CONSTRAINT collection_pk PRIMARY KEY (id)
);

-- Table: lyric
CREATE TABLE lyric (
    id int NOT NULL,
    music_id int NOT NULL,
    uploader_id int NOT NULL,
    uploaded_at datetime NOT NULL,
    lyric text NOT NULL,
    CONSTRAINT lyric_pk PRIMARY KEY (id)
);

-- Table: music
CREATE TABLE music (
    id int NOT NULL,
    name varchar(255) NOT NULL,
    cover_url varchar(255) NOT NULL,
    singer_id int NOT NULL,
    composer_id int NOT NULL,
    lyricist_id int NOT NULL,
    lyric_url varchar(255) NULL,
    album_id int NULL,
    src_url varchar(255) NOT NULL,
    published_at datetime NOT NULL,
    CONSTRAINT music_pk PRIMARY KEY (id)
);

-- Table: musician
CREATE TABLE musician (
    id int NOT NULL,
    name varchar(255) NOT NULL,
    CONSTRAINT musician_pk PRIMARY KEY (id)
);

-- Table: user
CREATE TABLE user (
    id int NOT NULL,
    username varchar(255) NOT NULL,
    email varchar(255) NOT NULL,
    gender varchar(36) NOT NULL,
    realname varchar(36) NOT NULL,
    auth varchar(36) NOT NULL,
    CONSTRAINT user_pk PRIMARY KEY (id)
);

CREATE INDEX user_idx_id ON user (id);

-- foreign keys
-- Reference: collect_music (table: collection)
ALTER TABLE collection ADD CONSTRAINT collect_music FOREIGN KEY collect_music (music_id)
    REFERENCES music (id);

-- Reference: music_lyric (table: lyric)
ALTER TABLE lyric ADD CONSTRAINT music_lyric FOREIGN KEY music_lyric (music_id)
    REFERENCES music (id);

-- Reference: musician_music (table: music)
ALTER TABLE music ADD CONSTRAINT musician_music FOREIGN KEY musician_music (singer_id,composer_id,lyricist_id)
    REFERENCES musician (id,id,id);

-- Reference: user_collection (table: collection)
ALTER TABLE collection ADD CONSTRAINT user_collection FOREIGN KEY user_collection (user_id)
    REFERENCES user (id);

-- Reference: user_lyric (table: lyric)
ALTER TABLE lyric ADD CONSTRAINT user_lyric FOREIGN KEY user_lyric (uploader_id)
    REFERENCES user (id);

-- End of file.

