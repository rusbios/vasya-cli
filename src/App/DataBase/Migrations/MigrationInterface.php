<?php

namespace RB\System\App\DataBase\Migrations;

use RB\System\App\DataBase\Connection\ConnectionInterface;

interface MigrationInterface
{
    public function up(ConnectionInterface $connection): void;

    public function down(ConnectionInterface $connection): void;
}