<?php
/**
 * Closeable pipe (e.g. TCP/HTTP)
 * User: moyo
 * Date: 22/11/2017
 * Time: 10:39 AM
 */

namespace Carno\Log\Contracts;

use Carno\Promise\Promised;

interface Closeable
{
    /**
     * @return Promised
     */
    public function close() : Promised;

    /**
     * @return Promised
     */
    public function closed() : Promised;
}
