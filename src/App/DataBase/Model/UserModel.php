<?php

namespace RB\System\App\DataBase\Model;

use RB\System\Helper\BitmaskHelper;

class UserModel extends AbstractModel implements ModelInterface, UserRoleInterface
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $telegramLogin = null;
    private ?int $telegramChatId = null;
    private int $role = 0;
    private ?string $password;
    private bool $isAuth = false;

    public static function getTableName(): string
    {
        return 'user';
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['name'] = $this->getName();
        $data['telegram_login'] = $this->getTelegramLogin();
        $data['telegram_chat_id'] = $this->getTelegramChatId();
        $data['role'] = $this->getRole();
        $data['password'] = $this->getPassword();
        $data['is_auth'] = $this->isAuth();
        return $data;
    }

    public static function createFromArray(array $data): self
    {
        $item = parent::createFromArray($data);
        if (isset($data['id'])) $item->setId($data['id']);
        if (isset($data['name'])) $item->setName($data['name']);
        if (isset($data['telegram_login'])) $item->setTelegramLogin($data['telegram_login']);
        if (isset($data['telegram_chat_id'])) $item->setTelegramChatId($data['telegram_chat_id']);
        if (isset($data['role'])) $item->setRole($data['role']);
        if (isset($data['password'])) $item->setPassword($data['password']);
        if (isset($data['is_auth'])) $item->setIsAuth($data['is_auth']);

        return $item;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getTelegramLogin(): ?string
    {
        return $this->telegramLogin;
    }

    public function setTelegramLogin(string $telegramLogin): self
    {
        $this->telegramLogin = $telegramLogin;
        return $this;
    }

    public function getTelegramChatId(): ?int
    {
        return $this->telegramChatId;
    }

    public function setTelegramChatId(int $telegramChatId): self
    {
        $this->telegramChatId = $telegramChatId;
        return $this;
    }

    public function getRole(): int
    {
        return $this->role;
    }

    public function setRole(int $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function isGuestRole(): bool
    {
        return BitmaskHelper::hasBit(self::BIT_ROLE_GUEST, $this->role);
    }

    public function isAdminRole(): bool
    {
        return BitmaskHelper::hasBit(self::BIT_ROLE_ADMIN, $this->role);
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function isAuth(): bool
    {
        return $this->isAuth;
    }

    public function setIsAuth(bool $isAuth): self
    {
        $this->isAuth = $isAuth;
        return $this;
    }
}