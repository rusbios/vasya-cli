<?php

namespace RB\System\App\DataBase\Connection;

use DateTimeInterface;
use RB\System\App\Config;
use RB\System\Exception\DataBaseException;
use SQLite3;

class SqLiteConnection implements ConnectionInterface
{
    protected Config $config;

    private SQLite3 $connect;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->init();
    }

    protected function init(): void
    {
        $path = $this->config->getValue('database.connections.'.self::SQLITE_CONNECTION);
        if (!file_exists($path)) {
            file_put_contents($path, '');
        }
        $this->connect = new SQLite3($path);
    }

    /**
     * @throws DataBaseException
     */
    public function query(string $sql): array
    {
        $res = $this->connect->query($sql);
        if ($res === false) {
            throw new DataBaseException($this->connect->lastErrorMsg());
        }

        while ($row = $res->fetchArray()) {
            $result[] = $row;
        }
        return $result ?? [];
    }

    /**
     * @throws DataBaseException
     */
    public function insert(string $table, array $params = []): int
    {
        $params = $this->escapeKeyValue($params);
        $keys = array_keys($params);
        $values = array_values($params);
        $sql = 'insert into `'.$table.'` ('.join(', ', $keys).') values ('.join(', ', $values).');';

        if ($this->connect->query($sql) === false) {
            throw new DataBaseException($this->connect->lastErrorMsg());
        }
        return $this->connect->lastInsertRowID();
    }

    /**
     * @throws DataBaseException
     */
    public function update(string $table, array $wheres = [], array $params = []): array
    {
        $params = $this->escapeKeyValue($params);
        $sets = [];
        foreach ($params as $key => $value) {
            $sets[] = "$key = $value";
        }
        $sql = 'update `'.$table.'` set '.join(', ', $sets).' '.$this->getSqlWheres($wheres).';';

        $res = $this->connect->query($sql);
        if ($res === false) {
            throw new DataBaseException($this->connect->lastErrorMsg());
        }

        while ($row = $res->fetchArray()) {
            $result[] = $row;
        }
        return $result ?? [];
    }

    /**
     * @throws DataBaseException
     */
    public function delete(string $table, array $wheres = []): array
    {
        $sql = 'delete from `'.$table.'`'.$this->getSqlWheres($wheres).';';

        return $this->query($sql);
    }

    public function isConnect(): bool
    {
        return true;
    }

    public function escape($value)
    {
        switch (gettype($value)) {
            case 'integer' | 'double':
                return $value;
            case 'string':
                return '"'.trim($value).'"';
            case 'NULL':
                return NULL;
            case 'boolean':
                return (int)$value;
            case 'array':
                return $this->escape(json_encode($value));
            case 'object':
                if ($value instanceof DateTimeInterface) {
                    $value = $value->format('H:i:s d.m.Y');
                }
                return $this->escape((string)$value);
        }

        if (mb_strtoupper($value) === 'default') {
            return 'default';
        }

        return $value;
    }

    protected function escapeKeyValue(array $params): array
    {
        $res = [];
        foreach ($params as $key => $value) {
            $res["`$key`"] = $this->escape($value);
        }
        return  $res;
    }

    public function getSqlWheres(array $wheres = []): string
    {
        if (empty($wheres)) {
            return '';
        }

        $items = [];
        foreach ($wheres as $key => $value) {
            $value = $this->escape($value);
            $items[] = ($value === null)
                ? "`$key` is null"
                : "`$key` = ".$value;
        }
        return 'where ' . join(' and ', $items);
    }
}