<?php
/**
 * @author: James Murray <jaimz@vertigolabs.org>
 * @date: 11/4/2017
 * @time: 7:53 AM
 */

namespace VertigoLabs\Ghost\Infrastructure\Logger;

use Psr\Log\LoggerInterface;

class MultiLogger implements LoggerInterface
{
    /**
     * A collection of loggers
     *
     * @var \SplObjectStorage|\Psr\Log\LoggerInterface[]
     */
    private $loggers;

    public function __construct()
    {
        $this->loggers = new \SplObjectStorage();
    }


    public function attachLogger(LoggerInterface $logger)
    {
        $this->loggers->attach($logger);
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function emergency($message, array $context=[])
    {
        foreach($this->loggers as $logger) {
            $logger->emergency($message,$context);
        }
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function alert($message, array $context=[])
    {
        foreach($this->loggers as $logger) {
            $logger->alert($message,$context);
        }
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function critical($message, array $context=[])
    {
        foreach($this->loggers as $logger) {
            $logger->critical($message,$context);
        }
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function error($message, array $context=[])
    {
        foreach($this->loggers as $logger) {
            $logger->error($message,$context);
        }
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function warning($message, array $context=[])
    {
        foreach($this->loggers as $logger) {
            $logger->warning($message,$context);
        }
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function notice($message, array $context=[])
    {
        foreach($this->loggers as $logger) {
            $logger->notice($message,$context);
        }
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function info($message, array $context=[])
    {
        foreach($this->loggers as $logger) {
            $logger->info($message,$context);
        }
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function debug($message, array $context=[])
    {
        foreach($this->loggers as $logger) {
            $logger->debug($message,$context);
        }
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        foreach($this->loggers as $logger) {
            $logger->log($level, $message,$context);
        }
    }
}