<?php

namespace RB\System\App\DataBase\Model;

use DateTime;

interface ModelInterface
{
    const DATE_TIME_FORMAT = 'Y.m.d H:i:s';

    public static function getTableName(): string;

    public function getPrimaryKey(): ?int;

    public function getPrimaryKeyName(): string;

    public function getCreateTs(): ?DateTime;

    public function getUpdateTs(): ?DateTime;

    public function getDeleteTs(): ?DateTime;

    public function toArray(): array;

    public static function createFromArray(array $data): self;
}