<?php

namespace RB\System\Service\TelegramBots;

use RB\System\App\DataBase\AllRepository;
use RB\System\App\DataBase\Model\UserModel;
use RB\System\Service\TelegramBots\Models\TelegramMessageDTO;

trait AuthUserTrait
{
    protected AllRepository $repository;

    protected function getUser(TelegramMessageDTO $message): ?UserModel
    {
        $res = $this->repository->fetch($this->user::getTableName(), [
            'telegram_login' => $message->getFrom()['username'],
            'telegram_chat_id' => $message->getChat()['id'],
        ]);
        foreach ($res as $model) {
            return $model;
        }

        return null;
    }
}