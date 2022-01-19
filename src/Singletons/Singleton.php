<?php

namespace System\Singletons;

use Exception;

/**
 * Singleton is a creational design pattern,
 * which ensures that only one object of its kind exists
 * and provides a single point of access to it for any other code.
 */
abstract class Singleton
{
    /**
     * @var $this ?self Holds the Singleton instance
     */
    private static ?self $instance;

    /**
     * Returns the singleton instance
     *
     * @return self singleton instance
     */
    public static function getInstance(): self
    {
        if (!isset(self::$instance))
        {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Protected constructor, so it can't be called from the outside
     */
    protected function __construct()
    {
    }

    /**
     * Prevent cloning
     *
     * @throws Exception
     */
    protected function __clone()
    {
        throw new Exception("Cannot clone a singleton.");
    }

    /**
     * Prevent serializing
     *
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a singleton.");
    }

}