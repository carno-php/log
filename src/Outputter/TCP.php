<?php
/**
 * -> TCP
 * User: moyo
 * Date: 21/11/2017
 * Time: 3:58 PM
 */

namespace Carno\Log\Outputter;

use Carno\Log\Contracts\Closeable;
use Carno\Log\Contracts\Outputter;
use Carno\Net\Address;
use Carno\Net\Contracts\TCP as Pipe;
use Carno\Net\Events;
use Carno\Promise\Promise;
use Carno\Promise\Promised;
use Carno\Socket\Socket;
use Carno\Timer\Timer;

class TCP implements Outputter, Closeable
{
    /**
     * reconnect later if error occurred
     */
    private const RETRY_WAIT = 1500;

    /**
     * max buf size
     */
    private const BUF_OVERFLOW = 20000;

    /**
     * @var Address
     */
    private $endpoint = null;

    /**
     * @var Events
     */
    private $events = null;

    /**
     * @var Pipe
     */
    private $client = null;

    /**
     * @var bool
     */
    private $connected = false;

    /**
     * @var bool
     */
    private $closing = false;

    /**
     * @var Promised
     */
    private $closed = null;

    /**
     * @var array
     */
    private $buffer = [];

    /**
     * TCP constructor.
     * @param Address $endpoint
     */
    public function __construct(Address $endpoint)
    {
        $this->endpoint = $endpoint;

        $this->events = (new Events)
            ->attach(Events\Socket::CONNECTED, function () {
                $this->connected();
            })
            ->attach(Events\Socket::CLOSED, function () {
                $this->connecting();
            })
            ->attach(Events\Socket::ERROR, function () {
                Timer::after(self::RETRY_WAIT, function () {
                    $this->connecting();
                });
            })
        ;

        $this->connecting();
    }

    /**
     * reconnect to server if conn closed or error
     */
    private function connecting() : void
    {
        $this->connected = false;
        $this->closing
            ? $this->closed()->resolve()
            : $this->client = Socket::connect($this->endpoint, $this->events)
        ;
    }

    /**
     * conn established
     */
    private function connected() : void
    {
        $this->connected = true;
        while ($this->buffer) {
            $this->client->send(array_shift($this->buffer));
        }
    }

    /**
     * @param string $log
     */
    public function write(string $log) : void
    {
        if ($this->connected) {
            $this->client->send($log);
            return;
        }

        if (count($this->buffer) >= self::BUF_OVERFLOW) {
            return;
        }

        $this->buffer[] = $log;
    }

    /**
     * @return Promised
     */
    public function close() : Promised
    {
        if ($this->connected) {
            $this->closing = true;
            $this->client->close();
            return $this->closed();
        } else {
            return Promise::resolved();
        }
    }

    /**
     * @return Promised
     */
    public function closed() : Promised
    {
        return $this->closed ?? $this->closed = Promise::deferred();
    }
}
