<?php
/**
 * Logger Test
 * User: moyo
 * Date: Jul 29, 2019
 * Time: 10:39
 */

namespace Carno\Log\Tests;

use Carno\Log\Logger;
use PHPUnit\Framework\TestCase;
use function Carno\Config\conf;

class LoggerTest extends TestCase
{
    public function testFunc()
    {
        conf()->set('log.addr', '???');
        logger('test')->info('test1');

        conf()->set('test.log.format', 'json');
        logger('test')->info('test2');

        conf()->set('log.level', 'debug');
        logger()->debug('test3');

        $this->assertTrue(logger() instanceof Logger);
    }
}
