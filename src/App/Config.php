<?php

namespace RB\System\App;

class Config
{
    private const DIR = BASE_PATH . '/config/';

    private static ?self $obj;

    private array $date;

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

    /**
     * @param array $path
     * @param array $values
     * @param $default
     * @return array|bool|int|string|null
     */
    private function getVal(array $path, array &$values, $default = null)
    {
        $key = array_shift($path);
        $val = $values[$key] ?? $default;
        if ($path) {
            $val = $this->getVal($path, $values[$key]);
        }
        return $val;
    }

    /**
     * @param string $name
     * @param null $default
     * @return string|int|bool|array|null
     * @example app.name
     */
    public function getValue(string $name, $default = null)
    {
        return $this->getVal(explode('.', $name), $this->date, $default);
    }
}