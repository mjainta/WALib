<?php
/**
 * Holds Config.
 *
 * @package WALib
 */
namespace WALib\Application;

/**
 * A class which holds the config for the application.
 *
 * Used to store variables which can be used in the whole application.
 *
 * @package WALib
 */
class AppConfig
{
    /**
     * The config array.
     *
     * @var mixed[]
     */
    protected static $_config = array();

    /**
     * Returns a config value or null if none exists.
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        if(self::has($key))
        {
            return self::$_config[$key];
        }
        else
        {
            return null;
        }
    }

    /**
     * Sets a config variable.
     *
     * If the key is already in use, it will be overridden.
     *
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        self::$_config[$key] = $value;
    }

    /**
     * Returns whether the key exists in the config array.
     *
     * @param string $key
     * @return boolean True if the key exists, false otherwise.
     */
    public static function has($key)
    {
        return array_key_exists($key, self::$_config);
    }

    /**
     * Adds an array to the config.
     *
     * Keys in use will be overridden by standard.
     *
     * @param mixed[] $params
     * @param boolean $overwrite
     */
    public static function add($params, $overwrite = true)
    {
        foreach($params as $configKey => $configValue)
        {
            /*
             * Only overwrite previous value if overwrite is allowed
             * or if overwrite is prohibited but the configKey is not already in use.
             */
            if($overwrite
                || (!$overwrite && !self::has($configKey)))
            {
                self::set($configKey, $configValue);
            }
        }
    }
}