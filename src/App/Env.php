<?php

namespace RB\System\App;

class Env
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getEnv(string $name, $default = null)
    {
        return $_ENV[$name] ?? $default;
    }

    public function getConfig(string $name, $default = null)
    {
        return $this->config->getValue($name, $default);
    }

    public function getVersion(): string
    {
        return $this->getConfig('app.version', '1.0.0');
    }

    public function getName(): string
    {
        return $this->getConfig('app.name', '');
    }

    public function isDev(): bool
    {
        return (bool)$this->getConfig('app.env', false);
    }

    public function isProd(): bool
    {
        return (bool)$this->getConfig('app.env', true);
    }

    public function isDebug(): bool
    {
        return (bool)$this->getConfig('app.debug', false);
    }
}