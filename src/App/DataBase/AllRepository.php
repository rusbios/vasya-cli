<?php

namespace RB\System\App\DataBase;

use DateTime;
use RB\System\App\DataBase\Connection\ConnectionInterface;
use RB\System\App\DataBase\Model\{ModelInterface, UserModel};
use RB\System\Exception\DataBaseException;

class AllRepository
{
    private array $map = [
        UserModel::class,
    ];

    private ConnectionInterface $conn;

    public function __construct(ConnectionInterface $conn)
    {
        $this->conn = $conn;
    }

    public function save(ModelInterface $model): ModelInterface
    {
        $data = array_filter($model->toArray());

        if ($model->getPrimaryKey()) {
            $data['update_ts'] = new DateTime();
            // TODO calc diff
            $data = $this->conn->update($model->getTableName(), [
                $model->getPrimaryKeyName() => $model->getPrimaryKey(),
            ], $data);
        } else {
            $data['create_ts'] = new DateTime();
            $data[$model->getPrimaryKeyName()] = $this->conn->insert($model->getTableName(), $data);
        }

        return $model::createFromArray($data);
    }

    /**
     * @return ModelInterface[]
     * @throws DataBaseException
     *
     */
    public function fetch(string $table, array $filter = []): array
    {
        /** @var ModelInterface $item */
        foreach ($this->map as $item) {
            if ($item::getTableName() == $table) {
                $class = $item;
                break;
            }
        }

        if (empty($class)) {
            throw new DataBaseException('not empty map table');
        }

        $sql = 'select * from '. $table . ' ' . $this->conn->getSqlWheres($filter);
        $res = $this->conn->query($sql);

        foreach ($res ?? [] as $item) {
            $result[] = $class::createFromArray($item);
        }

        return $result ?? [];
    }
}