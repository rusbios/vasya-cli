<?php

namespace RB\System\Service\DataBase\Migrations;

use RB\System\Service\DataBase\Connection\ConnectionInterface;

class CreateUserTable202202202309 implements MigrationInterface
{
    public function up(ConnectionInterface $connection): void
    {
        $sql = 'create table `user` (
    `id` integer primary key autoincrement,
    `name` varchar null,
    `telegram_login` varchar null,
    `create_ts` datetime not null default CURRENT_TIMESTAMP,
    `update_ts` datetime null,
    `delete_ts` datetime null,
    
    unique (`telegram_login`)
);';
        $connection->query($sql);
    }

    public function down(ConnectionInterface $connection): void
    {
        $connection->query('drop table `user`;');
    }
}