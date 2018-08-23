<?php
/**
 * JSON style
 * User: moyo
 * Date: 21/11/2017
 * Time: 4:20 PM
 */

namespace Carno\Log\Formatter;

use Carno\Log\Contracts\Formatter;
use Carno\Log\Environment;

class JSON implements Formatter
{
    /**
     * @var Environment
     */
    private $env = null;

    /**
     * JSON constructor.
     * @param Environment $env
     */
    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    /**
     * @param string $scene
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    public function render(string $scene, string $level, string $message, array $context) : string
    {
        return json_encode(
            array_merge(
                [
                    '$app' => $this->env->app(),
                    '$host' => $this->env->host(),
                    '$scene' => $scene,
                    '$level' => $level,
                    '$message' => $message
                ],
                $context
            ),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ) . "\n";
    }
}
