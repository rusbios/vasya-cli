<?php

namespace RB\System\App\DataBase\Migrations;

use RB\System\App\DataBase\Connection\ConnectionInterface;

class CreateUserTable202202202309 implements MigrationInterface
{
    public function up(ConnectionInterface $connection): void
    {
        $sql = 'create table `user` (
    `id` integer primary key autoincrement,
    `name` varchar null,
    `telegram_login` varchar null,
    `telegram_chat_id` integer null,
    `role` integer not null default 0,
    `password` varchar(255) not null,
    `is_auth` integer(1) not null default 0,
    `create_ts` datetime not null default CURRENT_TIMESTAMP,
    `update_ts` datetime null,
    `delete_ts` datetime null,
    
    unique (`telegram_login`),
    unique (`telegram_chat_id`)
);';
        $connection->query($sql);
    }

    public function down(ConnectionInterface $connection): void
    {
        $connection->query('drop table `user`;');
    }
}