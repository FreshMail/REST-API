<?php declare(strict_types=1);

namespace FreshMail\ApiV2\Factory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class MonologFactory
{
    /**
     * @return \Monolog\Logger
     */
    public static function createInstance(): LoggerInterface
    {
        $syslog = new SyslogHandler('freshmail-api-v2-library', LOG_USER, Logger::WARNING);
        $syslog->setFormatter(new LineFormatter("%level_name%: %message%"));

        $monolog = new Logger('freshmail-api-v2-library');
        $monolog->pushHandler($syslog);

        return $monolog;
    }
}