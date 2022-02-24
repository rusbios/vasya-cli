<?php

namespace RB\System\App\DataBase\Connection;

interface ConnectionInterface
{
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    const SQL_CONNECTION = 'sql';
    const SQLITE_CONNECTION = 'sqlite';

    public function query(string $sql);

    public function insert(string $table, array $params): int;

    public function update(string $table, array $wheres, array $params): array;

    public function delete(string $table, array $wheres): array;

    public function isConnect(): bool;

    public function escape($value);

    public function getSqlWheres(array $wheres = []): string;
}