CREATE TABLE group_users
(
    id          INT auto_increment NOT NULL,
    group_id    INT       NOt NULL,
    user_id     INT       NOt NULL,
    channel_id  char(32)  NOT NULL,
    is_manager  tinyint(1) NOT NULL DEFAULT 0,
    create_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    update_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`) USING BTREE,
    KEY         `group_id` (`group_id`),
    KEY         `user_id` (`user_id`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;