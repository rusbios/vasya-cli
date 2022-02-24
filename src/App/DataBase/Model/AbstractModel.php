<?php

namespace RB\System\App\DataBase\Model;

use DateTime;
use RB\System\App\DataBase\Connection\ConnectionInterface;

abstract class AbstractModel implements ModelInterface
{
    private ?DateTime $createTs = null;
    private ?DateTime $updateTs = null;
    private ?DateTime $deleteTs = null;

    public function toArray(): array
    {
        return [
            $this->getPrimaryKeyName() => $this->getPrimaryKey(),
            'create_ts' => $this->getCreateTs() ? $this->getCreateTs()->format(ConnectionInterface::DATE_TIME_FORMAT) : null,
            'update_ts' => $this->getUpdateTs() ? $this->getUpdateTs()->format(ConnectionInterface::DATE_TIME_FORMAT) : null,
            'delete_ts' => $this->getDeleteTs() ? $this->getDeleteTs()->format(ConnectionInterface::DATE_TIME_FORMAT) : null,
        ];
    }

    public static function createFromArray(array $data): ModelInterface
    {
        $item = new static();
        if (!empty($data['create_ts'])) {
            if ($data['create_ts'] instanceof DateTime) {
                $item->setCreateTs($data['create_ts']);
            } else {
                $item->setCreateTs(new DateTime($data['create_ts']));
            }
        }
        if (!empty($data['update_ts'])) {
            if ($data['update_ts'] instanceof DateTime) {
                $item->setUpdateTs($data['update_ts']);
            } else {
                $item->setUpdateTs(new DateTime($data['update_ts']));
            }
        }
        if (!empty($data['delete_ts'])) {
            if ($data['delete_ts'] instanceof DateTime) {
                $item->setDeleteTs($data['delete_ts']);
            } else {
                $item->setDeleteTs(new DateTime($data['delete_ts']));
            }
        }

        return $item;
    }

    public function getPrimaryKey(): ?int
    {
        return $this->getId();
    }

    public function getPrimaryKeyName(): string
    {
        return 'id';
    }

    public function getCreateTs(): ?DateTime
    {
        return $this->createTs;
    }

    public function setCreateTs(DateTime $createTs): self
    {
        $this->createTs = $createTs;
        return $this;
    }

    public function getUpdateTs(): ?DateTime
    {
        return $this->updateTs;
    }

    public function setUpdateTs(DateTime $updateTs): self
    {
        $this->updateTs = $updateTs;
        return $this;
    }

    public function getDeleteTs(): ?DateTime
    {
        return $this->deleteTs;
    }

    public function setDeleteTs(DateTime $deleteTs): self
    {
        $this->deleteTs = $deleteTs;
        return $this;
    }
}