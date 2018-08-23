<?php
/**
 * Replicated log
 * User: moyo
 * Date: 2018/5/2
 * Time: 11:11 AM
 */

namespace Carno\Log\Contracts;

interface Replicated
{
    /**
     * @param string $scene
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function send(string $scene, string $level, string $message, array $context) : void;
}
