-- XAMPPでコミュニティ掲示板用のDB作成スクリプト
CREATE DATABASE IF NOT EXISTS `community_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `community_db`;

CREATE TABLE IF NOT EXISTS `communities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `titles` VARCHAR(255) NOT NULL,
  `descriptions` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `community_id` INT NOT NULL,
  `user_name` VARCHAR(100) NOT NULL,
  `post_content` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (community_id) REFERENCES communities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `oshi_icon` VARCHAR(255), -- 推しキャラのアイコン画像URL
  `oshi_name` VARCHAR(100), -- 推しキャラ名
  `anime_name` VARCHAR(100), -- アニメ名
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `community_members` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `community_id` INT NOT NULL,
  `user_name` VARCHAR(100) NOT NULL,
  FOREIGN KEY (community_id) REFERENCES communities(id) ON DELETE CASCADE,
  FOREIGN KEY (user_name) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- コメントテーブル
CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL,
  `user_name` VARCHAR(100) NOT NULL,
  `comment_text` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_name) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- いいねテーブル
CREATE TABLE IF NOT EXISTS `likes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL,
  `user_name` VARCHAR(100) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_like (post_id, user_name),
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_name) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- フォローテーブル
CREATE TABLE IF NOT EXISTS `follows` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `follower` VARCHAR(100) NOT NULL,
  `followee` VARCHAR(100) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_follow (follower, followee),
  FOREIGN KEY (follower) REFERENCES users(username) ON DELETE CASCADE,
  FOREIGN KEY (followee) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
