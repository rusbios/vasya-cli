<?php

namespace RB\System\App\DataBase\Migrations;

use RB\System\App\DataBase\Connection\ConnectionInterface;

class CreateMigrationTable202202202131 implements MigrationInterface
{
    public function up(ConnectionInterface $connection): void
    {
        $sql = 'create table if not exists `migration` (
    `level` integer primary key autoincrement,
    `name` varchar(64) unique not null,
    `create_ts` datetime not null default CURRENT_TIMESTAMP
);';
        $connection->query($sql);
    }

    public function down(ConnectionInterface $connection): void
    {
        return;
    }
}