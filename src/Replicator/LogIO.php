<?php
/**
 * Replicating to log.io
 * User: moyo
 * Date: 2018/5/2
 * Time: 11:59 AM
 */

namespace Carno\Log\Replicator;

use Carno\Log\Connections;
use Carno\Log\Contracts\Outputter;
use Carno\Log\Contracts\Replicated;
use Carno\Log\Environment;
use Carno\Log\Outputter\TCP;
use Carno\Net\Address;

class LogIO implements Replicated
{
    /**
     * @var Environment
     */
    private $env = null;

    /**
     * @var Connections
     */
    private $cmg = null;

    /**
     * @var Address
     */
    private $recv = null;

    /**
     * @var Outputter
     */
    private $pipe = null;

    /**
     * @var array
     */
    private $assets = [];

    /**
     * LogIO constructor.
     * @param Environment $env
     * @param Connections $cmg
     * @param Address $recv
     */
    public function __construct(Environment $env, Connections $cmg, Address $recv)
    {
        $this->env = $env;
        $this->cmg = $cmg;
        $this->recv = $recv;

        $this->pipe = $cmg->hosting($recv, static function (Address $recv) {
            return new TCP($recv);
        });
    }

    /**
     */
    public function __destruct()
    {
        $this->cmg->close($this->recv);
    }

    /**
     * @param string $scene
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function send(string $scene, string $level, string $message, array $context) : void
    {
        $buf = '';

        $node = $this->env->app().(($t = $this->env->tagged()) ? sprintf(':%s', $t) : '');
        $stream = $scene;

        if (!isset($this->assets[$node][$stream])) {
            $this->assets[$node][$stream] = true;
            $buf .= sprintf("+node|%s|%s\r\n", $node, $stream);
        }

        $buf .= sprintf(
            "+log|%s|%s|%s|%s\r\n",
            $stream,
            $node,
            $level,
            sprintf('%s > %s', $message, json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
        );

        $this->pipe->write($buf);
    }
}
