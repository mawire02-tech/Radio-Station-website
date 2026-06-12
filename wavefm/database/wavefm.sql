-- ============================================================
-- WAVE FM Community Radio Station - Database Schema
-- Compatible: MySQL 5.7+ / MariaDB 10.3+
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `wavefm` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `wavefm`;

-- ── USERS ────────────────────────────────────────────────────
CREATE TABLE `users` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username`     VARCHAR(60)  NOT NULL UNIQUE,
  `email`        VARCHAR(120) NOT NULL UNIQUE,
  `password`     VARCHAR(255) NOT NULL,
  `full_name`    VARCHAR(120) NOT NULL,
  `role`         ENUM('superadmin','admin','editor') NOT NULL DEFAULT 'editor',
  `avatar`       VARCHAR(255) DEFAULT NULL,
  `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
  `last_login`   DATETIME     DEFAULT NULL,
  `login_token`  VARCHAR(64)  DEFAULT NULL,
  `token_expiry` DATETIME     DEFAULT NULL,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default superadmin: admin / Admin@123
INSERT INTO `users` (`username`,`email`,`password`,`full_name`,`role`) VALUES
('admin','admin@wavefm.local','$2y$12$YK.oFBvA0q1nfAC3TmWBCOnW5CWkRJb4/GjdJYP0JzN1U8bRpF6eW','Station Admin','superadmin');

-- ── CATEGORIES ───────────────────────────────────────────────
CREATE TABLE `categories` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(80)  NOT NULL,
  `slug`        VARCHAR(80)  NOT NULL UNIQUE,
  `color`       VARCHAR(7)   NOT NULL DEFAULT '#E82B2B',
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`name`,`slug`,`color`) VALUES
('Station News','station-news','#E82B2B'),
('Events','events','#D4A847'),
('Music','music','#2B8CE8'),
('Community','community','#27AE60'),
('Technology','technology','#8E44AD');

-- ── NEWS ─────────────────────────────────────────────────────
CREATE TABLE `news` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id`  INT UNSIGNED NOT NULL,
  `author_id`    INT UNSIGNED NOT NULL,
  `title`        VARCHAR(255) NOT NULL,
  `slug`         VARCHAR(255) NOT NULL UNIQUE,
  `excerpt`      TEXT         NOT NULL,
  `body`         LONGTEXT     NOT NULL,
  `image`        VARCHAR(255) DEFAULT NULL,
  `status`       ENUM('published','draft','archived') NOT NULL DEFAULT 'draft',
  `featured`     TINYINT(1)   NOT NULL DEFAULT 0,
  `views`        INT UNSIGNED NOT NULL DEFAULT 0,
  `published_at` DATETIME     DEFAULT NULL,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`author_id`)   REFERENCES `users`(`id`)       ON DELETE RESTRICT,
  INDEX `idx_status`     (`status`),
  INDEX `idx_published`  (`published_at`),
  INDEX `idx_featured`   (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `news` (`category_id`,`author_id`,`title`,`slug`,`excerpt`,`body`,`status`,`featured`,`published_at`) VALUES
(1,1,'WAVE FM Wins Best Community Station Award','wave-fm-wins-best-community-station','For the third consecutive year, WAVE FM has been recognised for outstanding community engagement.','<p>For the third consecutive year, WAVE FM has been recognised for outstanding community engagement and programming excellence at the national awards ceremony held in London.</p><p>The award, presented by the Community Radio Association, highlights WAVE FM\'s commitment to authentic local broadcasting, its volunteer training programme, and the innovative shows that have shaped the local music scene since 1998.</p><p>Station Manager Claire Reynolds said the award reflects the hard work of every presenter, volunteer, and listener who makes WAVE FM what it is.</p>','published',1,NOW()),
(2,1,'Live Studio Night Returns This April','live-studio-night-april','Our beloved live studio sessions are back — book your free tickets for an intimate evening.','<p>After a hugely popular 2025 season, WAVE FM Live Studio Nights return this April with an exciting lineup of local artists performing live from our flagship broadcast studio.</p><p>The event is free to attend but ticketed due to limited capacity. Doors open at 7:00 PM and performances begin at 8:00 PM. Attendees will get a behind-the-scenes look at how live radio is produced.</p>','published',1,NOW()),
(3,1,'New Genre: Afrobeats Takes Over Friday Nights','new-genre-afrobeats-friday','WAVE FM expands its genre offering with a brand new Afrobeats slot every Friday from 10PM.','<p>Starting this Friday, WAVE FM welcomes DJ Kwame to the schedule with a brand new two-hour Afrobeats programme every week from 10:00 PM to midnight.</p><p>The show will feature the latest releases from across the African continent and the diaspora, alongside listener shout-outs and music requests.</p>','published',0,NOW()),
(4,1,'Community Garden Fundraiser: WAVE FM Steps Up','community-garden-fundraiser','WAVE FM is proud to support the Greenfield Community Garden fundraiser with a special broadcast day.','<p>This coming Saturday, WAVE FM will broadcast live from the Greenfield Community Garden in a special 12-hour fundraiser broadcast to help the garden expand its facilities.</p><p>Local artists, food stalls, and family activities will all be part of the day. Tune in or come down in person from 10 AM to 10 PM.</p>','published',0,NOW());

-- ── GENRES ───────────────────────────────────────────────────
CREATE TABLE `genres` (
  `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(60) NOT NULL UNIQUE,
  `slug` VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `genres` (`name`,`slug`) VALUES
('Electronic','electronic'),('Hip-Hop','hip-hop'),('Jazz','jazz'),
('Soul & R&B','soul-rnb'),('Rock','rock'),('Afrobeats','afrobeats'),
('Classical','classical'),('Reggae','reggae'),('Pop','pop'),('Ambient','ambient');

-- ── PRESENTERS ───────────────────────────────────────────────
CREATE TABLE `presenters` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(120) NOT NULL,
  `slug`        VARCHAR(120) NOT NULL UNIQUE,
  `bio`         TEXT         NOT NULL,
  `photo`       VARCHAR(255) DEFAULT NULL,
  `role`        VARCHAR(80)  NOT NULL DEFAULT 'Presenter',
  `email`       VARCHAR(120) DEFAULT NULL,
  `twitter`     VARCHAR(80)  DEFAULT NULL,
  `instagram`   VARCHAR(80)  DEFAULT NULL,
  `facebook`    VARCHAR(80)  DEFAULT NULL,
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `presenters` (`name`,`slug`,`bio`,`role`,`twitter`,`instagram`,`sort_order`) VALUES
('DJ Solaris','dj-solaris','A pioneering force in electronic and ambient music broadcasting. DJ Solaris has been with WAVE FM since 2010 and is the creative mind behind the acclaimed Midnight Frequencies show.','Senior Presenter','@djsolaris','djsolaris_fm',1),
('Priya Kapoor','priya-kapoor','Priya brings warmth, wit and an encyclopedic knowledge of soul and jazz to every broadcast. She hosts the award-winning Morning Glow show every weekday.','Morning Presenter','@priyakfm','priyakapoor_radio',2),
('Marcus Williams','marcus-williams','Marcus is the heartbeat of WAVE FM\'s hip-hop and R&B programming. His weekly show The Cipher has launched the careers of several local artists.','Evening Presenter','@marcuswfm','marcuswilliams_dj',3),
('DJ Kwame','dj-kwame','A celebrated name in the Afrobeats scene, DJ Kwame brings the rhythms of Africa and the diaspora to WAVE FM every Friday night.','Guest Presenter','@djkwame','djkwame_official',4),
('Sophie Crane','sophie-crane','Sophie hosts the weekend arts magazine show and is passionate about connecting local culture with WAVE FM\'s audience.','Weekend Presenter','@sophiecrane','sophiecrane_radio',5);

-- ── SHOWS ────────────────────────────────────────────────────
CREATE TABLE `shows` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `presenter_id` INT UNSIGNED NOT NULL,
  `genre_id`     INT UNSIGNED NOT NULL,
  `title`        VARCHAR(120) NOT NULL,
  `slug`         VARCHAR(120) NOT NULL UNIQUE,
  `description`  TEXT         NOT NULL,
  `image`        VARCHAR(255) DEFAULT NULL,
  `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`presenter_id`) REFERENCES `presenters`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`genre_id`)     REFERENCES `genres`(`id`)     ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `shows` (`presenter_id`,`genre_id`,`title`,`slug`,`description`,`is_active`) VALUES
(1,1,'Midnight Frequencies','midnight-frequencies','Deep dives into electronic and ambient soundscapes. The perfect late-night listening experience with carefully curated playlists and exclusive mixes.',1),
(2,4,'Morning Glow','morning-glow','Start your day right with Priya\'s selection of soul, jazz and feel-good vibes. The most-listened-to breakfast show in the region.',1),
(3,2,'The Cipher','the-cipher','Hip-hop, R&B and the culture that surrounds it. Marcus brings the latest releases, classic cuts and exclusive freestyles every week.',1),
(4,6,'Afrobeats Friday','afrobeats-friday','Two hours of the hottest Afrobeats, Afropop and dancehall from across the African continent and beyond.',1),
(5,3,'The Arts Hour','the-arts-hour','A weekly magazine show exploring local arts, culture, film, theatre and music from the community and beyond.',1);

-- ── SCHEDULE ─────────────────────────────────────────────────
CREATE TABLE `schedule` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `show_id`    INT UNSIGNED NOT NULL,
  `day_of_week` TINYINT UNSIGNED NOT NULL COMMENT '0=Sunday,1=Monday,...,6=Saturday',
  `start_time` TIME         NOT NULL,
  `end_time`   TIME         NOT NULL,
  `is_repeat`  TINYINT(1)   NOT NULL DEFAULT 0,
  FOREIGN KEY (`show_id`) REFERENCES `shows`(`id`) ON DELETE CASCADE,
  INDEX `idx_day` (`day_of_week`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `schedule` (`show_id`,`day_of_week`,`start_time`,`end_time`) VALUES
-- Monday–Friday: Morning Glow 6-9
(2,1,'06:00:00','09:00:00'),(2,2,'06:00:00','09:00:00'),(2,3,'06:00:00','09:00:00'),(2,4,'06:00:00','09:00:00'),(2,5,'06:00:00','09:00:00'),
-- Mon/Wed/Fri: The Cipher 7-9 PM
(3,1,'19:00:00','21:00:00'),(3,3,'19:00:00','21:00:00'),(3,5,'19:00:00','21:00:00'),
-- Tue/Thu/Sat: Midnight Frequencies 12-2 AM
(1,2,'00:00:00','02:00:00'),(1,4,'00:00:00','02:00:00'),(1,6,'00:00:00','02:00:00'),
-- Sun: Arts Hour 2-4 PM
(5,0,'14:00:00','16:00:00'),
-- Friday: Afrobeats 10PM-12AM
(4,5,'22:00:00','23:59:00');

-- ── PODCASTS / ON-DEMAND ──────────────────────────────────────
CREATE TABLE `podcasts` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `show_id`     INT UNSIGNED NOT NULL,
  `title`       VARCHAR(255) NOT NULL,
  `description` TEXT         DEFAULT NULL,
  `audio_file`  VARCHAR(255) NOT NULL,
  `duration`    INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'seconds',
  `file_size`   INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'bytes',
  `downloads`   INT UNSIGNED NOT NULL DEFAULT 0,
  `published_at` DATETIME    NOT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`show_id`) REFERENCES `shows`(`id`) ON DELETE CASCADE,
  INDEX `idx_show` (`show_id`),
  INDEX `idx_published` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `podcasts` (`show_id`,`title`,`description`,`audio_file`,`duration`,`file_size`,`published_at`) VALUES
(1,'Midnight Frequencies — Episode 48','A deep journey through Berlin techno and ambient electronica.','ep048_midnight_freq.mp3',7200,98400000,NOW()),
(1,'Midnight Frequencies — Episode 47','Exploring the boundaries of minimal techno.','ep047_midnight_freq.mp3',6900,95000000,DATE_SUB(NOW(),INTERVAL 7 DAY)),
(2,'Morning Glow — Best of March','The finest soul and jazz selections from March.','morning_glow_march.mp3',10800,148000000,DATE_SUB(NOW(),INTERVAL 3 DAY)),
(3,'The Cipher — Episode 101','Celebrating 100 episodes with the hottest hip-hop.','cipher_ep101.mp3',7200,99000000,DATE_SUB(NOW(),INTERVAL 5 DAY));

-- ── MUSIC REQUESTS ────────────────────────────────────────────
CREATE TABLE `music_requests` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `listener_name` VARCHAR(100) NOT NULL,
  `listener_email` VARCHAR(120) DEFAULT NULL,
  `song_title`   VARCHAR(150) NOT NULL,
  `artist`       VARCHAR(120) NOT NULL,
  `shoutout`     TEXT         DEFAULT NULL,
  `show_id`      INT UNSIGNED DEFAULT NULL,
  `status`       ENUM('pending','approved','played','rejected') NOT NULL DEFAULT 'pending',
  `ip_address`   VARCHAR(45)  NOT NULL,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`show_id`) REFERENCES `shows`(`id`) ON DELETE SET NULL,
  INDEX `idx_status` (`status`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── FEEDBACK ─────────────────────────────────────────────────
CREATE TABLE `feedback` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`         VARCHAR(100) NOT NULL,
  `email`        VARCHAR(120) DEFAULT NULL,
  `subject`      VARCHAR(200) NOT NULL,
  `message`      TEXT         NOT NULL,
  `type`         ENUM('general','complaint','compliment','suggestion') NOT NULL DEFAULT 'general',
  `is_read`      TINYINT(1)   NOT NULL DEFAULT 0,
  `ip_address`   VARCHAR(45)  NOT NULL,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── GENRE POLL ────────────────────────────────────────────────
CREATE TABLE `poll_votes` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `genre_id`   INT UNSIGNED NOT NULL,
  `ip_address` VARCHAR(45)  NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`genre_id`) REFERENCES `genres`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_vote` (`genre_id`,`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── EVENTS ───────────────────────────────────────────────────
CREATE TABLE `events` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`       VARCHAR(200) NOT NULL,
  `description` TEXT         DEFAULT NULL,
  `location`    VARCHAR(200) DEFAULT NULL,
  `event_date`  DATE         NOT NULL,
  `start_time`  TIME         DEFAULT NULL,
  `end_time`    TIME         DEFAULT NULL,
  `is_featured` TINYINT(1)   NOT NULL DEFAULT 0,
  `created_by`  INT UNSIGNED NOT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  INDEX `idx_date` (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `events` (`title`,`description`,`location`,`event_date`,`start_time`,`end_time`,`is_featured`,`created_by`) VALUES
('Live Studio Night','Intimate live performances from our studio','WAVE FM Studio, Main Street',DATE_ADD(CURDATE(),INTERVAL 9 DAY),'19:00:00','22:00:00',1,1),
('Community Garden Broadcast','12-hour live broadcast fundraiser','Greenfield Community Garden',DATE_ADD(CURDATE(),INTERVAL 4 DAY),'10:00:00','22:00:00',1,1),
('Afrobeats Launch Night','Special launch party for Afrobeats Friday','The Warehouse, City Centre',DATE_ADD(CURDATE(),INTERVAL 16 DAY),'20:00:00','02:00:00',0,1);

-- ── SITE SETTINGS ─────────────────────────────────────────────
CREATE TABLE `settings` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(80)  NOT NULL UNIQUE,
  `setting_val` TEXT         NOT NULL,
  `label`       VARCHAR(120) NOT NULL,
  `group_name`  VARCHAR(60)  NOT NULL DEFAULT 'general',
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`setting_key`,`setting_val`,`label`,`group_name`) VALUES
('station_name','WAVE FM','Station Name','general'),
('station_tagline','Your Community Voice Since 1998','Station Tagline','general'),
('station_frequency','98.7','Frequency (MHz)','general'),
('station_email','hello@wavefm.local','Contact Email','contact'),
('station_phone','+44 1234 567890','Phone Number','contact'),
('station_address','123 Radio House, Media Quarter, London, W1A 1AA','Address','contact'),
('stream_url','https://stream.wavefm.local/live','Live Stream URL','streaming'),
('stream_backup_url','','Backup Stream URL','streaming'),
('facebook_url','https://facebook.com/wavefm','Facebook URL','social'),
('twitter_url','https://twitter.com/wavefm','Twitter/X URL','social'),
('instagram_url','https://instagram.com/wavefm','Instagram URL','social'),
('youtube_url','https://youtube.com/@wavefm','YouTube URL','social'),
('about_mission','WAVE FM exists to give a genuine voice to our community — celebrating local music, championing local stories, and connecting people through the power of radio.','Mission Statement','about'),
('about_history','Founded in 1998 by a group of music enthusiasts and community activists, WAVE FM began broadcasting from a small room above a record shop. Today we operate from purpose-built studios and reach over 12,000 weekly listeners across the region.','Station History','about'),
('meta_description','WAVE FM 98.7 — Your community radio station. Live streaming, local news, show schedules, and music requests.','Meta Description','seo'),
('maintenance_mode','0','Maintenance Mode','general'),
('station_logo','','Station Logo','general');

-- ── LISTENER ANALYTICS ───────────────────────────────────────
CREATE TABLE `listener_analytics` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `session_id`   VARCHAR(64)  NOT NULL,
  `ip_address`   VARCHAR(45)  NOT NULL,
  `user_agent`   VARCHAR(500) DEFAULT NULL,
  `country`      VARCHAR(2)   DEFAULT NULL,
  `city`         VARCHAR(100) DEFAULT NULL,
  `timestamp`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_active`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `page`         VARCHAR(255) DEFAULT NULL,
  INDEX `idx_timestamp` (`timestamp`),
  INDEX `idx_session` (`session_id`),
  UNIQUE KEY `unique_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── ACTIVITY LOG ─────────────────────────────────────────────
CREATE TABLE `activity_log` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT UNSIGNED DEFAULT NULL,
  `action`      VARCHAR(100) NOT NULL,
  `entity`      VARCHAR(60)  DEFAULT NULL,
  `entity_id`   INT UNSIGNED DEFAULT NULL,
  `detail`      TEXT         DEFAULT NULL,
  `ip_address`  VARCHAR(45)  NOT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── SUBSCRIBERS ──────────────────────────────────────────────
CREATE TABLE `subscribers` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email`        VARCHAR(120) NOT NULL UNIQUE,
  `name`         VARCHAR(120) DEFAULT NULL,
  `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
  `verified`     TINYINT(1)   NOT NULL DEFAULT 0,
  `verify_token` VARCHAR(64)  DEFAULT NULL,
  `subscribed_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unsubscribed_at` DATETIME  DEFAULT NULL,
  INDEX `idx_email` (`email`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_created` (`subscribed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
