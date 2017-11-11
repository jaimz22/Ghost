<?php
/**
 * @author: James Murray <jaimz@vertigolabs.org>
 * @date: 11/11/2017
 * @time: 7:19 AM
 */

namespace VertigoLabs\Ghost\Infrastructure\Worker;

use League\CLImate\CLImate;
use Ramsey\Uuid\Uuid;
use VertigoLabs\Ghost\Infrastructure\Logger\WorkerLogger;
use VertigoLabs\Ghost\Infrastructure\Worker\Config\WorkerConfiguration;

abstract class AbstractWorker implements WorkerInterface
{
    /**
     * @var \League\CLImate\CLImate
     */
    protected $console;

    /**
     * @var \VertigoLabs\Ghost\Infrastructure\Logger\WorkerLogger
     */
    protected $logger;

    /**
     * @var Config\WorkerConfiguration
     */
    private $configuration;

    /**
     * A unique identifier assigned to this instance of the worker
     * @var string
     */
    private $UUID;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var bool
     */
    protected $valid;

    /**
     * @var bool
     */
    protected $interactive;

    /**
     * @var bool
     */
    protected $test;

    final public function init(CLImate $console, WorkerLogger $logger)
    {
        $this->console = $console;
        $this->setDefaultArguments();

        $logger->setWorker($this);
        $this->logger = $logger;

        if ($this->console->arguments->defined('help')) {
            $this->displayHelp();
            exit;
        }
        if ($this->console->arguments->defined('test')) {
            $this->test = true;
        }
        if ($this->console->arguments->defined('interactive')) {
            $this->test = true;
        }
    }

    private function setDefaultArguments()
    {
        $this->console->arguments->add([
            'interactive' => [
                'prefix' => 'i',
                'longPrefix' => 'interactive',
                'description' => 'Runs the worker in interactive mode',
                'noValue'=>true
            ],
            'test' => [
                'longPrefix' => 'test',
                'description' => 'Runs the worker in test mode',
                'noValue'=>true
            ]
        ]);
    }

    public function setWorkerArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function setConfiguration(WorkerConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function getName()
    {
        return $this->configuration->getAppName();
    }

    public function getUUID()
    {
        if (null === $this->UUID) {
            $this->UUID = Uuid::uuid4()->toString();
        }

        return $this->UUID;
    }

    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @return bool
     */
    public function isInteractive()
    {
        return $this->interactive;
    }

    /**
     * @return bool
     */
    public function isTest()
    {
        return $this->test;
    }

    public function getStallTime()
    {
        return $this->configuration->getStallTime();
    }

    public function getSleepTime()
    {
        return $this->configuration->getSleepTime();
    }

    public function getMaxIterationCount()
    {
        return $this->configuration->getMaxIterationCount();
    }

    private function displayHelp()
    {
        $this->console->addArt(dirname(__DIR__));
        $this->console->lightBlue()->animation('banner')->speed(400)->enterFrom('left');
        $this->console->usage();
    }
}