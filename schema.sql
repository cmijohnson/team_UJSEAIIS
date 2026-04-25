CREATE DATABASE IF NOT EXISTS `team_showcase` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `team_showcase`;

-- 管理员表
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL,
    INDEX `idx_username` (`username`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 初始化管理员账号 (密码为 password123 的 hash)
INSERT INTO `admins` (`username`, `password_hash`, `email`) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- 文章表
CREATE TABLE IF NOT EXISTS `articles` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `content` TEXT NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `cover_image` VARCHAR(500),
    `author_id` INT NOT NULL,
    `view_count` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`author_id`) REFERENCES `admins`(`id`) ON DELETE CASCADE,
    INDEX `idx_category` (`category`),
    INDEX `idx_created_at` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 图片资源表
CREATE TABLE IF NOT EXISTS `images` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `filename` VARCHAR(255) NOT NULL,
    `url` VARCHAR(500) NOT NULL,
    `type` ENUM('avatar', 'banner', 'content', 'competition', 'project') NOT NULL,
    `size` INT NOT NULL,
    `uploaded_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`uploaded_by`) REFERENCES `admins`(`id`) ON DELETE CASCADE,
    INDEX `idx_type` (`type`),
    INDEX `idx_created_at` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 团队成员表
CREATE TABLE IF NOT EXISTS `members` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `position` VARCHAR(100) NOT NULL,
    `avatar` VARCHAR(500),
    `bio` TEXT,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 比赛成果表
CREATE TABLE IF NOT EXISTS `competitions` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `competition_date` DATE,
    `result` VARCHAR(500),
    `images` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_competition_date` (`competition_date` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 科研项目表
CREATE TABLE IF NOT EXISTS `projects` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `status` ENUM('ongoing', 'completed', 'planned') DEFAULT 'ongoing',
    `achievements` TEXT,
    `images` JSON,
    `start_date` DATE,
    `end_date` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`),
    INDEX `idx_start_date` (`start_date` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 指导老师表
CREATE TABLE IF NOT EXISTS `advisors` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `avatar` VARCHAR(500),
    `profile` TEXT,
    `research_fields` VARCHAR(500),
    `contact` VARCHAR(200),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 发展历程时间轴表
CREATE TABLE IF NOT EXISTS `timeline_events` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `year` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `event_date` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_year` (`year` DESC),
    INDEX `idx_event_date` (`event_date` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 系统配置表 (如团队介绍、网站信息等)
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `setting_key` VARCHAR(100) UNIQUE NOT NULL,
    `setting_value` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 初始化基本配置
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES 
('team_name', '智链细米安全盾队'),
('team_introduction', '我们是一支致力于网络安全和区块链技术研究的专业团队。'),
('contact_email', 'contact@example.com'),
('oss_config', '{}')
ON DUPLICATE KEY UPDATE `id`=`id`;
