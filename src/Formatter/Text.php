<?php
/**
 * Text lines
 * User: moyo
 * Date: 10/10/2017
 * Time: 3:00 PM
 */

namespace Carno\Log\Formatter;

use Carno\Log\Contracts\Formatter;
use Psr\Log\LogLevel;

class Text implements Formatter
{
    /**
     * @var array
     */
    private $colors = [
        LogLevel::EMERGENCY => '0;31m', // red
        LogLevel::ALERT => '0;31m', // red
        LogLevel::CRITICAL => '0;31m', // red
        LogLevel::ERROR => '0;31m', // red
        LogLevel::WARNING => '1;33m', // yellow
        LogLevel::NOTICE => '0;35m', // purple
        LogLevel::INFO => '0;36m', // cyan
        LogLevel::DEBUG => '0;32m', // green
    ];

    /**
     * @var string
     */
    private $colorCtxKey = '0;33m'; // brown

    /**
     * @var string
     */
    private $colorMessage = '1;37m'; // white

    /**
     * @var string
     */
    private $colorSignEND = "\033[0m";

    /**
     * @var string
     */
    private $colorSignBGN = "\033[";

    /**
     * @param string $scene
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    public function render(string $scene, string $level, string $message, array $context) : string
    {
        return sprintf(
            "[%s] [%s] [%s] : %s ~ %s\n",
            $this->colorSignBGN . $this->colors[$level] . strtoupper($level) . $this->colorSignEND,
            date('Y-m-d H:i:s'),
            strtoupper($scene),
            $this->colorSignBGN . $this->colorMessage . $message . $this->colorSignEND,
            $this->context($context)
        );
    }

    /**
     * @param $context
     * @return string
     */
    private function context($context)
    {
        $print = '[';

        array_walk($context, function ($item, $key) use (&$print) {
            $ctx = $this->colorSignBGN . $this->colorCtxKey . $key . $this->colorSignEND . '=';
            if (is_array($item)) {
                $ctx .= json_encode($item, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } else {
                $ctx .= $item;
            }
            $print .= $ctx . ',';
        });

        return rtrim($print, ',') . ']';
    }
}
