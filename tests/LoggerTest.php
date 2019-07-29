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
    private const DAT = [
        'k1' => 'v1',
        'k2' => 111,
        'k3' => true,
        'k4' => null,
        'k5' => [1, 2, 3],
        'k6' => ['kk1' => 'vv1']
    ];

    public function testFunc()
    {
        conf()->set('log.addr', '???');
        logger('test')->info('test1', self::DAT);

        conf()->set('test.log.format', 'json');
        logger('test')->info('test2', self::DAT);

        conf()->set('log.level', 'debug');
        logger()->debug('test3', self::DAT);

        $this->assertTrue(logger() instanceof Logger);
    }
}
