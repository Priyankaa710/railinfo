-- =========================================================
-- RailInfo Portal — MySQL Schema
-- Import this file via phpMyAdmin (Hostinger hPanel) or:
--   mysql -u USERNAME -p DATABASE_NAME < schema.sql
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------
-- stations : master list used by autocomplete search
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `stations` (
  `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`  VARCHAR(10)  NOT NULL,
  `name`  VARCHAR(120) NOT NULL,
  `city`  VARCHAR(120) DEFAULT NULL,
  `state` VARCHAR(120) DEFAULT NULL,
  `zone`  VARCHAR(20)  DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_stations_code` (`code`),
  KEY `idx_stations_name` (`name`),
  KEY `idx_stations_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- trains : master list of trains
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `trains` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `train_number`     VARCHAR(10)  NOT NULL,
  `train_name`       VARCHAR(150) NOT NULL,
  `train_type`       VARCHAR(20)  DEFAULT 'EXP',
  `source_code`      VARCHAR(10)  NOT NULL,
  `destination_code` VARCHAR(10)  NOT NULL,
  `departure_time`   VARCHAR(8)   NOT NULL,
  `arrival_time`     VARCHAR(8)   NOT NULL,
  `duration`         VARCHAR(10)  DEFAULT NULL,
  `distance_km`      INT UNSIGNED DEFAULT NULL,
  `runs_mon` TINYINT(1) NOT NULL DEFAULT 1,
  `runs_tue` TINYINT(1) NOT NULL DEFAULT 1,
  `runs_wed` TINYINT(1) NOT NULL DEFAULT 1,
  `runs_thu` TINYINT(1) NOT NULL DEFAULT 1,
  `runs_fri` TINYINT(1) NOT NULL DEFAULT 1,
  `runs_sat` TINYINT(1) NOT NULL DEFAULT 1,
  `runs_sun` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_trains_number` (`train_number`),
  KEY `idx_trains_route` (`source_code`, `destination_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- schedules : cached per-date schedule + seat/fare snapshot
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `schedules` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `train_id`         INT UNSIGNED NOT NULL,
  `source_code`      VARCHAR(10)  NOT NULL,
  `destination_code` VARCHAR(10)  NOT NULL,
  `travel_date`      DATE         NOT NULL,
  `departure_time`   VARCHAR(8)   NOT NULL,
  `arrival_time`     VARCHAR(8)   NOT NULL,
  `duration`         VARCHAR(10)  DEFAULT NULL,
  `sl_seats`  INT DEFAULT NULL, `sl_fare`  DECIMAL(8,2) DEFAULT NULL,
  `ac3_seats` INT DEFAULT NULL, `ac3_fare` DECIMAL(8,2) DEFAULT NULL,
  `ac2_seats` INT DEFAULT NULL, `ac2_fare` DECIMAL(8,2) DEFAULT NULL,
  `ac1_seats` INT DEFAULT NULL, `ac1_fare` DECIMAL(8,2) DEFAULT NULL,
  `status`     VARCHAR(20) NOT NULL DEFAULT 'ON_TIME',
  `fetched_at` DATETIME NOT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_schedules_lookup` (`source_code`, `destination_code`, `travel_date`),
  CONSTRAINT `fk_schedules_train` FOREIGN KEY (`train_id`) REFERENCES `trains` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- pnr_cache : cached PNR lookups
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pnr_cache` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pnr_number`       VARCHAR(10)  NOT NULL,
  `train_number`     VARCHAR(10)  DEFAULT NULL,
  `train_name`       VARCHAR(150) DEFAULT NULL,
  `journey_date`     DATE         DEFAULT NULL,
  `source_code`      VARCHAR(10)  DEFAULT NULL,
  `destination_code` VARCHAR(10)  DEFAULT NULL,
  `boarding_point`   VARCHAR(10)  DEFAULT NULL,
  `class`            VARCHAR(10)  DEFAULT NULL,
  `chart_prepared`   TINYINT(1)   NOT NULL DEFAULT 0,
  `passenger_count`  INT UNSIGNED DEFAULT 0,
  `passengers_json`  JSON         DEFAULT NULL,
  `current_status`   VARCHAR(20)  NOT NULL DEFAULT 'UNKNOWN',
  `fetched_at`       DATETIME     NOT NULL,
  `created_at`       DATETIME     DEFAULT NULL,
  `updated_at`       DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pnr_number` (`pnr_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- travel_history : durable copy of per-visitor search history
-- (fast-access copy also kept in the CI4 session)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `travel_history` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id`       VARCHAR(128) NOT NULL,
  `search_type`      ENUM('schedule','pnr') NOT NULL,
  `source_code`      VARCHAR(10)  DEFAULT NULL,
  `destination_code` VARCHAR(10)  DEFAULT NULL,
  `pnr_number`       VARCHAR(10)  DEFAULT NULL,
  `travel_date`      DATE         DEFAULT NULL,
  `searched_at`      DATETIME     NOT NULL,
  `created_at`       DATETIME     DEFAULT NULL,
  `updated_at`       DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_history_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- Seed data — a starter set of stations & trains so the demo
-- works immediately after import. Extend with real data as needed.
-- =========================================================

INSERT IGNORE INTO `stations` (`code`, `name`, `city`, `state`, `zone`) VALUES
('NDLS', 'New Delhi',        'New Delhi', 'Delhi',          'NR'),
('BCT',  'Mumbai Central',   'Mumbai',    'Maharashtra',    'WR'),
('MAS',  'Chennai Central',  'Chennai',   'Tamil Nadu',     'SR'),
('SBC',  'Bengaluru City Jn','Bengaluru', 'Karnataka',      'SWR'),
('HWH',  'Howrah Junction',  'Kolkata',   'West Bengal',    'ER'),
('ADI',  'Ahmedabad Jn',     'Ahmedabad', 'Gujarat',        'WR'),
('PUNE', 'Pune Junction',    'Pune',      'Maharashtra',    'CR'),
('JP',   'Jaipur Junction',  'Jaipur',    'Rajasthan',      'NWR'),
('LKO',  'Lucknow NR',       'Lucknow',   'Uttar Pradesh',  'NR'),
('PNBE', 'Patna Junction',   'Patna',     'Bihar',          'ECR');

INSERT IGNORE INTO `trains`
  (`train_number`, `train_name`, `train_type`, `source_code`, `destination_code`, `departure_time`, `arrival_time`, `duration`, `distance_km`, `created_at`, `updated_at`)
VALUES
('12951', 'Mumbai Rajdhani Express', 'RAJ', 'NDLS', 'BCT', '16:25:00', '08:15:00', '15h 50m', 1384, NOW(), NOW()),
('12301', 'Howrah Rajdhani Express', 'RAJ', 'NDLS', 'HWH', '16:55:00', '10:05:00', '17h 10m', 1447, NOW(), NOW()),
('12621', 'Tamil Nadu Express',      'EXP', 'NDLS', 'MAS', '22:30:00', '07:15:00', '32h 45m', 2194, NOW(), NOW()),
('12007', 'Shatabdi Express',        'SHT', 'NDLS', 'JP',  '06:05:00', '10:40:00', '4h 35m',  308,  NOW(), NOW());

INSERT IGNORE INTO `schedules`
  (`train_id`, `source_code`, `destination_code`, `travel_date`, `departure_time`, `arrival_time`, `duration`,
   `sl_seats`, `sl_fare`, `ac3_seats`, `ac3_fare`, `ac2_seats`, `ac2_fare`, `ac1_seats`, `ac1_fare`, `status`, `fetched_at`)
SELECT id, source_code, destination_code, CURDATE(), departure_time, arrival_time, duration,
       48, 755.00, 22, 1985.00, 6, 2850.00, 2, 4750.00, 'ON_TIME', NOW()
FROM trains;
