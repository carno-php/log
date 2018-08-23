<?php
/**
 * Logger scened
 * User: moyo
 * Date: 10/10/2017
 * Time: 2:43 PM
 */

namespace Carno\Log;

use Carno\Log\Contracts\Formatter;
use Carno\Log\Contracts\Outputter;
use Carno\Log\Contracts\Replicated;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Logger extends AbstractLogger
{
    /**
     * @var string
     */
    private $scene = null;

    /**
     * @var array
     */
    private $levels = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG,
    ];

    /**
     * @var array
     */
    private $allows = [];

    /**
     * @var Formatter
     */
    private $formatter = null;

    /**
     * @var Outputter
     */
    private $outputter = null;

    /**
     * @var Replicated
     */
    private $replicator = null;

    /**
     * Logger constructor.
     * @param string $scene
     * @param Configure $configure
     */
    public function __construct(string $scene, Configure $configure)
    {
        $this->scene = $scene;

        $configure->syncLevel($scene, function (string $level) {
            $this->allows = array_slice($this->levels, 0, array_search($level, $this->levels, true) + 1);
        });

        $configure->syncFormatter($scene, function (Formatter $formatter) {
            $this->formatter = $formatter;
        });

        $configure->syncOutputter($scene, function (Outputter $outputter) {
            $this->outputter = $outputter;
        });

        $configure->syncReplicator($scene, function (Replicated $replicated = null) {
            $this->replicator = $replicated;
        });
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = []) : void
    {
        if (in_array($level, $this->allows) && $this->outputter && $this->formatter) {
            $this->outputter->write($this->formatter->render($this->scene, $level, $message, $context));
            $this->replicator && $this->replicator->send($this->scene, $level, $message, $context);
        }
    }
}
