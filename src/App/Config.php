<?php

namespace RB\System\App;

class Config
{
    private const DIR = BASE_PATH . '/config/';

    private static ?self $obj;

    private array $date;

    private ?Env $env = null;

    private function __construct()
    {
        foreach (scandir(self::DIR) as $configName) {
            if (!empty(trim($configName, '.'))) {
                $name = str_replace('.php', '', $configName);
                $this->date[$name] = require self::DIR . $configName;
            }
        }
    }

    public static function create(): self
    {
        if (empty(self::$obj)) {
            self::$obj = new self();
        }

        return self::$obj;
    }

    private function getVal(array $path, array &$values, $default = null)
    {
        $key = array_shift($path);
        $val = $values[$key] ?? $default;
        if ($path) {
            $val = $this->getVal($path, $values[$key]);
        }
        return $val;
    }

    public function getValue(string $name, $default = null)
    {
        return $this->getVal(explode('.', $name), $this->date, $default);
    }

    public function getEnv(): Env
    {
        if (!$this->env) {
            $this->env = new Env(self::create());
        }

        return $this->env;
    }
}
