CREATE TABLE user_users
(
    id          INT auto_increment NOT NULL,
    user_id     INT          NOT NULL,
    has_user_id INT          NOT NULL,
    channel_id  varchar(100) NOT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    KEY         `user_id` (`user_id`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;