<?php
/**
 * Commands kit
 * User: moyo
 * Date: 10/10/2017
 * Time: 2:42 PM
 */

/**
 * @param string $scene
 * @return \Psr\Log\LoggerInterface
 */
function logger(string $scene = 'default') : \Psr\Log\LoggerInterface
{
    return \Carno\Log\Instances::get($scene);
}
