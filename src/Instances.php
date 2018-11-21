<?php
/**
 * Instances manager
 * User: moyo
 * Date: 22/11/2017
 * Time: 10:56 AM
 */

namespace Carno\Log;

use Carno\Container\DI;
use Psr\Log\LoggerInterface;

class Instances
{
    /**
     * @var Logger[]
     */
    private static $loggers = [];

    /**
     * @var Configure
     */
    private static $configured = null;

    /**
     * @param string $scene
     * @return LoggerInterface
     */
    public static function get(string $scene) : LoggerInterface
    {
        return self::$loggers[$scene] ?? self::$loggers[$scene] = new Logger($scene, self::configure());
    }

    /**
     * @param Configure $configure
     */
    public static function configuration(Configure $configure) : void
    {
        if (self::$configured) {
            self::$configured->unload();
        }

        self::$configured = $configure;

        foreach (self::$loggers as $logger) {
            $logger->reconfigure($configure);
        }
    }

    /**
     * @return Configure
     */
    private static function configure() : Configure
    {
        return self::$configured ?? self::$configured = new Configure(
            DI::has(Environment::class) ? DI::get(Environment::class) : new Environment,
            DI::has(Connections::class) ? DI::get(Connections::class) : null
        );
    }
}
