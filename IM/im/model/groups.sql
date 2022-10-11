CREATE TABLE groups
(
    id          INT auto_increment NOT NULL,
    user_id     INT          NOt NULL,
    title       varchar(100) NOt NULL,
    description varchar(255) NOt NULL,
    channel_id  char(32)     NOT NULL,
    create_time timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    update_time timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`) USING BTREE,
    KEY         `title` (`title`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;