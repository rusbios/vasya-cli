<?php

namespace RB\System\App\DataBase\Model;

use DateTime;

interface ModelInterface
{
    public static function getTableName(): string;

    public function getPrimaryKey(): ?int;

    public function getPrimaryKeyName(): string;

    public function getCreateTs(): ?DateTime;

    public function getUpdateTs(): ?DateTime;

    public function getDeleteTs(): ?DateTime;

    public function toArray(): array;

    public static function createFromArray(array $data): self;
}