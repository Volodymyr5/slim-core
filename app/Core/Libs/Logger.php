<?php

namespace App\Core\Libs;

/**
 * Class Logger
 * @package App\Core\Libs
 */
class Logger
{
    /**
     * @param string $message
     */
    public static function log($message = '')
    {
        $logFilePath = date('Y-m-d') . '-logger.log';
        $logFilePath = dirname(dirname(__DIR__)) . '/var/logs/' . $logFilePath;
        $logDateTime = date('Y-m-d H:i:s');
        if (is_array($message)) {
            $message = var_export($message, true);
            $message = strval($message);
            $message = str_replace("\n", "", $message);
        }

        $message = "{$logDateTime} --- {$message}\n";

        file_put_contents($logFilePath, $message, FILE_APPEND);
    }
}
