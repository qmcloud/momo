CREATE TABLE notices
(
    id          INT auto_increment NOT NULL,
    type        INT          NOT NULL,
    pub_user_id INT          NOT NULL,
    sub_user_id INT          NOT NULL,
    link_id     INT          NOT NULL,
    content     varchar(100) NOt NULL,
    Note        varchar(100) NOt NULL DEFAULT '',
    is_agree    varchar(100) NOt NULL,
    create_time timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    update_time timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status      tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`) USING BTREE,
    KEY         `pub_user_id` (`pub_user_id`),
    KEY         `sub_user_id` (`sub_user_id`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;