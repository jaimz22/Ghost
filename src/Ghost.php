<?php
/**
 * @author: James Murray <jaimz@vertigolabs.org>
 * @date: 11/11/2017
 * @time: 10:15 AM
 */

namespace VertigoLabs\Ghost;


use DI\Container;
use League\CLImate\CLImate;
use Psr\Log\LoggerInterface;
use VertigoLabs\Ghost\Infrastructure\Exceptions\Console\MissingArgumentException;
use VertigoLabs\Ghost\Infrastructure\Exceptions\WorkerNotFoundException;
use VertigoLabs\Ghost\Infrastructure\Logger\ConsoleLogger;
use VertigoLabs\Ghost\Infrastructure\Logger\MultiLogger;
use VertigoLabs\Ghost\Infrastructure\Logger\SystemDaemonLogger;
use VertigoLabs\Ghost\Infrastructure\Worker\WorkerInterface;

class Ghost
{
    /**
     * @var \League\CLImate\CLImate
     */
    private $console;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \VertigoLabs\Ghost\Infrastructure\Worker\WorkerInterface
     */
    private $worker;
    /**
     * @var \DateTime
     */
    private $workerStartDate;
    /**
     * @var int
     */
    private $workerIterationCount = 0;
    /**
     * @var array
     */
    private $workerExecutionTimings = [];
    /**
     * @var \DI\Container
     */
    private $container;


    public function __construct(CLImate $console, MultiLogger $logger, Container $container)
    {
        $this->console = $console;
        $this->logger = $logger;
        $this->container = $container;

        $this->logger->attachLogger($container->get(ConsoleLogger::class));
        $this->logger->attachLogger($container->get(SystemDaemonLogger::class));
        $this->initializeConsole();
        $this->buildWorker();
    }

    public function run()
    {
        if (!($this->worker instanceof WorkerInterface)) {
            throw new \RuntimeException('No worker specified');
        }

        \System_Daemon::setOptions($this->worker->getConfiguration()->toDaemonOptions());
        \System_Daemon::setOption('appDir',base_path());

        if (!$this->worker->isInteractive() && !$this->worker->isTest() && !$this->console->arguments->defined('no-daemon')) {
            \System_Daemon::start();
        }

        $this->logger->info(sprintf('Starting worker "%s" with UUID: %s.',$this->worker->getName(), $this->worker->getUUID()));
        $this->workerStartDate = new \DateTime();
        $this->worker->startUp();
        $this->logger->debug(sprintf('Worker "%s" with UUID: %s started successfully',$this->worker->getName(), $this->worker->getUUID()));

        while(!\System_Daemon::isDying() && $this->worker->isValid()) {
            gc_collect_cycles();
            gc_disable();
            $this->workerIterationCount++;
            $iterationStartTime = microtime(true);

            $this->logger->info(sprintf('Worker "%s" with UUID: %s waking up.',$this->worker->getName(), $this->worker->getUUID()));
            $this->worker->wake();
            $this->logger->debug(sprintf('Worker "%s" with UUID: %s woke up successfully',$this->worker->getName(), $this->worker->getUUID()));

            $waitTime = 2;

            $this->logger->debug(sprintf('Worker "%s" with UUID: %s beginning iteration #%d',$this->worker->getName(), $this->worker->getUUID(),$this->workerIterationCount));
            $status = $this->worker->execute();
            switch($status) {
                case WorkerInterface::Signal_Stall:
                    $waitTime = $this->worker->getStallTime();
                    break;
                case WorkerInterface::Signal_Sleep:
                    $waitTime = $this->worker->getSleepTime();
                    break;
                case WorkerInterface::Signal_Continue:
                    $waitTime = 2;
                    break;
                case WorkerInterface::Signal_Die:
                    \System_Daemon::stop();
                    break;
            }

            $iterationWorkTime = microtime(true) - $iterationStartTime;
            $this->workerExecutionTimings[$this->workerIterationCount] = $iterationWorkTime;

            $this->logger->info(sprintf('Worker "%s" with UUID: %s iteration #%d completed in %s seconds.',$this->worker->getName(), $this->worker->getUUID(), $this->workerIterationCount, $iterationWorkTime));

            if ($this->workerIterationCount>=$this->worker->getMaxIterationCount()) {
                $waitTime = $this->worker->getSleepTime();
                $this->logger->debug(sprintf('Worker "%s" with UUID: %s completed %d iterations in %s seconds', $this->worker->getName(), $this->worker->getUUID(), count($this->workerExecutionTimings), array_sum($this->workerExecutionTimings)));
                $this->workerIterationCount = 0;
                $this->workerExecutionTimings = [];
            }

            gc_enable();
            $this->logger->info(sprintf('Worker "%s" with UUID: %s sleeping for %s', $this->worker->getName(), $this->worker->getUUID(), $waitTime));
            $this->worker->sleep();
            $this->logger->debug(sprintf('Worker "%s" with UUID: %s put to sleep successfully', $this->worker->getName(), $this->worker->getUUID()));
            \System_Daemon::iterate($waitTime);
        }

        $this->logger->info(sprintf('Worker "%s" with UUID: %s shutting down.', $this->worker->getName(), $this->worker->getUUID()));
        $this->worker->shutdown();
        $this->logger->debug(sprintf('Worker "%s" with UUID: %s shut down successfully.', $this->worker->getName(), $this->worker->getUUID()));
        \System_Daemon::stop();
    }

    private function displayHelp()
    {
        $this->console->addArt(__DIR__.DIRECTORY_SEPARATOR.'Infrastructure');
        $this->console->lightBlue()->animation('banner')->speed(400)->enterFrom('left');
        $this->console->usage();
    }

    /**
     * @throws \VertigoLabs\Ghost\Infrastructure\Exceptions\WorkerNotFoundException
     */
    protected function buildWorker()
    {
        $worker = $this->console->arguments->get('worker');
        if (!class_exists($worker)) {
            throw new WorkerNotFoundException($worker);
        }
        try{
            $this->worker = $this->container->get($worker);
        }catch (\Exception $e) {
            throw new WorkerNotFoundException($worker, $e->getCode(), $e);
        }
    }

    /**
     * @throws \VertigoLabs\Ghost\Infrastructure\Exceptions\Console\MissingArgumentException
     * @throws \Exception
     */
    protected function initializeConsole()
    {
        $this->console->arguments->add([
            'worker' => [
                'prefix'      => 'w',
                'longPrefix'  => 'worker',
                'description' => 'The name of the worker to run',
                'required'    => true
            ],
            'help'   => [
                'prefix'      => 'h',
                'longPrefix'  => 'help',
                'description' => 'Shows this help message',
                'noValue'     => true
            ],
            'no-daemon'   => [
                'longPrefix'  => 'no-daemon',
                'description' => 'Run the worker one time (do not spawn a daemon process).',
                'noValue'     => true
            ]
        ]);

        if ($this->console->arguments->defined('help') && !$this->console->arguments->defined('worker')) {
            $this->displayHelp();
            exit;
        }

        try{
            $this->console->arguments->parse();
        }catch (\Exception $e){
            throw new MissingArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }
}