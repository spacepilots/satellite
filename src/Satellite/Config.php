<?php

namespace Satellite;

class Config
{
    private static $config = [];

    public static function initialize(array $config)
    {
        $defaultConfig = require(__DIR__ . '/../config.php');
        self::$config = array_replace_recursive($defaultConfig, $config);
    }

    public static function load(string $configFile, $silent = false)
    {
        if (file_exists($configFile)) {
            self::initialize(require($configFile));
            return;
        }
        if ($silent) {
            self::initialize([]);
        } else {
            throw new \InvalidArgumentException("Configuration '${configFile}' does not exist.");
        }
    }

    public static function get($path, $defaultValue = null)
    {
        if (is_string($path)) {
            return array_key_exists($path, self::$config)
                ? self::$config[$path]
                : $defaultValue;
        }
        if (!is_array($path)) {
            throw new \InvalidArgumentException("Invalid config path '${path}'.");
        }

        $ref = &self::$config;
        foreach ($path as $segment) {
            if (is_array($ref) && array_key_exists($segment, $ref)) {
                $ref = &$ref[$segment];
            } else {
                return $defaultValue;
            }
        }
        return $ref;
    }
}
