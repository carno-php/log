<?php
/**
 * Log outputter
 * User: moyo
 * Date: 10/10/2017
 * Time: 3:01 PM
 */

namespace Carno\Log\Contracts;

interface Outputter
{
    /**
     * @param string $data
     */
    public function write(string $data) : void;
}
