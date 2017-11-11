<?php
/**
 * @author: James Murray <jaimz@vertigolabs.org>
 * @date: 11/11/2017
 * @time: 7:52 AM
 */

namespace VertigoLabs\Ghost\Infrastructure\Logger;

use VertigoLabs\Ghost\Infrastructure\Worker\WorkerInterface;

class WorkerLogger extends MultiLogger
{

    /**
     * The worker that is logging
     *
     * @var \VertigoLabs\Ghost\Infrastructure\Worker\WorkerInterface
     */
    private $worker;

    public function __construct(SystemDaemonLogger $systemDaemonLogger, ConsoleLogger $consoleLogger)
    {
        parent::__construct();
        $this->attachLogger($systemDaemonLogger);
        $this->attachLogger($consoleLogger);
    }

    /**
     * @param \VertigoLabs\Ghost\Infrastructure\Worker\WorkerInterface $worker
     */
    public function setWorker(WorkerInterface $worker)
    {
        $this->worker = $worker;
    }

    public function emergency($message, array $context=[])
    {
        $this->addWorkerNameToContext($context);
        parent::emergency($message, $context);
    }

    public function alert($message, array $context=[])
    {
        $this->addWorkerNameToContext($context);
        parent::alert($message, $context);
    }

    public function critical($message, array $context=[])
    {
        $this->addWorkerNameToContext($context);
        parent::critical($message, $context);
    }

    public function error($message, array $context=[])
    {
        $this->addWorkerNameToContext($context);
        parent::error($message, $context);
    }

    public function warning($message, array $context=[])
    {
        $this->addWorkerNameToContext($context);
        parent::warning($message, $context);
    }

    public function notice($message, array $context=[])
    {
        $this->addWorkerNameToContext($context);
        parent::notice($message, $context);
    }

    public function info($message, array $context=[])
    {
        $this->addWorkerNameToContext($context);
        parent::info($message, $context);
    }

    public function debug($message, array $context=[])
    {
        $this->addWorkerNameToContext($context);
        parent::debug($message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        $this->addWorkerNameToContext($context);
        parent::log($level, $message,$context);
    }

    private function addWorkerNameToContext(&$context)
    {
        if(!isset($context['worker'])){
            $context['worker'] = [
                'name'=>$this->worker->getName(),
                'UUID'=>$this->worker->getUUID()
                ];
        }
    }
}