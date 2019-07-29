<?php
/**
 * -> Stdout
 * User: moyo
 * Date: 10/10/2017
 * Time: 2:58 PM
 */

namespace Carno\Log\Outputter;

use Carno\Log\Contracts\Outputter;

class Stdout implements Outputter
{
    /**
     * @param string $data
     */
    public function write(string $data) : void
    {
        PHP_SAPI === 'cli' && print $data;
    }
}
