<?php
/**
 * Environment info
 * User: moyo
 * Date: 27/12/2017
 * Time: 4:12 PM
 */

namespace Carno\Log;

class Environment
{
    /**
     * @var string
     */
    private $app = null;

    /**
     * @var string
     */
    private $host = null;

    /**
     * @var string
     */
    private $tagged = null;

    /**
     * Environment constructor.
     * @param string $app
     * @param string $tagged
     */
    public function __construct(string $app = 'app', string $tagged = '')
    {
        $this->app = $app;
        $this->host = gethostname();
        $this->tagged = $tagged;
    }

    /**
     * @return string
     */
    public function app() : string
    {
        return $this->app;
    }

    /**
     * @return string
     */
    public function host() : string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function tagged() : string
    {
        return $this->tagged;
    }
}
