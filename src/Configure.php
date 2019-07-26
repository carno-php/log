<?php
/**
 * Log configure
 * User: moyo
 * Date: 21/11/2017
 * Time: 6:18 PM
 */

namespace Carno\Log;

use Carno\Config\Config;
use Carno\Config\Features\Overrider;
use Carno\Log\Contracts\Formatter;
use Carno\Log\Contracts\Outputter;
use Carno\Log\Contracts\Replicated;
use Carno\Log\Formatter\JSON;
use Carno\Log\Formatter\Text;
use Carno\Log\Outputter\Stdout;
use Carno\Log\Outputter\TCP;
use Carno\Log\Replicator\LogIO;
use Carno\Net\Address;
use Psr\Log\LogLevel;
use Closure;

class Configure
{
    /**
     * @var Config
     */
    private $cfg = null;

    /**
     * @var Environment
     */
    private $env = null;

    /**
     * @var Connections
     */
    private $cmg = null;

    /**
     * @var Overrider[]
     */
    private $watched = [];

    /**
     * Configure constructor.
     * @param Config $cfg
     * @param Environment $env
     * @param Connections $cmg
     */
    public function __construct(Config $cfg, Environment $env, Connections $cmg = null)
    {
        $this->cfg = $cfg;
        $this->env = $env;
        $this->cmg = $cmg;
    }

    /**
     * @param string $scene
     * @param Closure $sync
     */
    public function syncLevel(string $scene, Closure $sync) : void
    {
        $sync(debug() ? LogLevel::DEBUG : LogLevel::INFO);

        $this->watched[] = $this->cfg->overrides(static function (string $level) use ($sync) {
            debug() || $sync($level);
        }, 'log.level', $scene . '.log.level');
    }

    /**
     * @param string $scene
     * @param Closure $sync
     */
    public function syncFormatter(string $scene, Closure $sync) : void
    {
        $sync($this->getFormatter('text'));

        $this->watched[] = $this->cfg->overrides(function (string $type) use ($sync) {
            debug() || $sync($this->getFormatter($type));
        }, 'log.format', $scene . '.log.format');
    }

    /**
     * @param string $scene
     * @param Closure $sync
     */
    public function syncOutputter(string $scene, Closure $sync) : void
    {
        $sync($this->getOutputter('stdout://'));

        $this->watched[] = $this->cfg->overrides(function (string $dsn) use ($sync) {
            debug() || $sync($this->getOutputter($dsn));
        }, 'log.addr', $scene . '.log.addr');
    }

    /**
     * @param string $scene
     * @param Closure $sync
     */
    public function syncReplicator(string $scene, Closure $sync) : void
    {
        $this->watched[] = $this->cfg->overrides(function (string $dsn = null) use ($sync) {
            debug() || $sync($this->getReplicator($dsn));
        }, 'log.replica', $scene . '.log.replica');
    }

    /**
     */
    public function unload() : void
    {
        foreach ($this->watched as $watcher) {
            $watcher->unwatch();
        }
    }

    /**
     * @param string $type
     * @return Formatter
     */
    private function getFormatter(string $type) : Formatter
    {
        switch ($type) {
            case 'json':
                return new JSON($this->env);
            default:
                return new Text();
        }
    }

    /**
     * @param string $dsn
     * @return Outputter
     */
    private function getOutputter(string $dsn) : Outputter
    {
        $parsed = parse_url($dsn);

        switch ($parsed['scheme'] ?? 'default') {
            case 'tcp':
                if ($this->cmg) {
                    return $this->cmg->hosting(
                        new Address($parsed['host'], $parsed['port'] ?? 80),
                        static function (Address $address) {
                            return new TCP($address);
                        }
                    );
                }
        }

        return new Stdout();
    }

    /**
     * @param string $dsn
     * @return Replicated
     */
    private function getReplicator(string $dsn = null) : ?Replicated
    {
        $parsed = parse_url($dsn);

        switch ($parsed['scheme'] ?? 'default') {
            case 'logio':
                if ($this->cmg) {
                    return new LogIO($this->env, $this->cmg, new Address($parsed['host'], $parsed['port'] ?? 28777));
                }
        }

        return null;
    }
}
