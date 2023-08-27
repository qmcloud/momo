CREATE TABLE messages
(
    id           INT auto_increment NOT NULL,
    channel_id   varchar(100) NOT NULL,
    send_user_id INT          NOT NULL,
    message      text         NOT NULL,
    create_time  timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    update_time  timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`) USING BTREE,
    KEY          `channel_id` (`channel_id`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;