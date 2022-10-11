CREATE TABLE `send_queues`
(
    `id`           int(11) NOT NULL AUTO_INCREMENT,
    `user_id`      int(11) NOT NULL,
    `message`      text NOT NULL,
    `send_user_id` int(11) NOT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    KEY            `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;