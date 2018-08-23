<?php
/**
 * Log connections manager
 * User: moyo
 * Date: 22/11/2017
 * Time: 10:38 AM
 */

namespace Carno\Log;

use Carno\Log\Contracts\Closeable;
use Carno\Log\Contracts\Outputter;
use Carno\Net\Address;
use Carno\Promise\Promise;
use Carno\Promise\Promised;
use Closure;

class Connections
{
    /**
     * @var Closeable[]
     */
    private $connected = [];

    /**
     * @param Address $address
     * @param Closure $connector
     * @return Outputter
     */
    public function hosting(Address $address, Closure $connector) : Outputter
    {
        return
            $this->connected[$cid = (string)$address] ??
            $this->connected[$cid] = $this->watch($cid, $connector($address))
        ;
    }

    /**
     * @param string $cid
     * @param Closeable $conn
     * @return Closeable|Outputter
     */
    private function watch(string $cid, Closeable $conn) : Closeable
    {
        $conn->closed()->then(function () use ($cid) {
            unset($this->connected[$cid]);
        });

        return $conn;
    }

    /**
     * @param Address $address
     * @return Promised
     */
    public function close(Address $address) : Promised
    {
        return ($c = $this->connected[(string)$address] ?? null) ? $c->close() : Promise::resolved();
    }

    /**
     * @return Promised
     */
    public function release() : Promised
    {
        $pending = [];

        foreach ($this->connected as $closeable) {
            $pending[] = $closeable->close();
        }

        return Promise::all(...$pending);
    }
}
