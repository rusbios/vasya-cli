<?php

namespace RB\System\Service\DataBase\Migrations;

use RB\System\Service\DataBase\Connection\ConnectionInterface;

interface MigrationInterface
{
    public function up(ConnectionInterface $connection): void;

    public function down(ConnectionInterface $connection): void;
}