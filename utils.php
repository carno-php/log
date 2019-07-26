<?php
/**
 * Commands kit
 * User: moyo
 * Date: 10/10/2017
 * Time: 2:42 PM
 */

use Carno\Log\Instances;
use Psr\Log\LoggerInterface;

/**
 * @param string $scene
 * @return LoggerInterface
 */
function logger(string $scene = 'default') : LoggerInterface
{
    return Instances::get($scene);
}
