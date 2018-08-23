<?php
/**
 * Log formatter
 * User: moyo
 * Date: 10/10/2017
 * Time: 3:01 PM
 */

namespace Carno\Log\Contracts;

interface Formatter
{
    /**
     * @param string $scene
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    public function render(string $scene, string $level, string $message, array $context) : string;
}
