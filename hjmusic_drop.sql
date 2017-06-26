-- Created by Vertabelo (http://vertabelo.com)
-- Last modification date: 2017-06-25 06:23:08.186

-- foreign keys
ALTER TABLE collection
    DROP FOREIGN KEY collect_music;

ALTER TABLE lyric
    DROP FOREIGN KEY music_lyric;

ALTER TABLE music
    DROP FOREIGN KEY musician_music;

ALTER TABLE collection
    DROP FOREIGN KEY user_collection;

ALTER TABLE lyric
    DROP FOREIGN KEY user_lyric;

-- tables
DROP TABLE collection;

DROP TABLE lyric;

DROP TABLE music;

DROP TABLE musician;

DROP TABLE user;

-- End of file.

