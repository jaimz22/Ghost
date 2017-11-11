<?php
namespace VertigoLabs\Ghost\Workers;
use VertigoLabs\Ghost\Infrastructure\Worker\AbstractWorker;
use VertigoLabs\Ghost\Infrastructure\Worker\Config\WorkerConfiguration;
use VertigoLabs\Ghost\Services\TestService;

/**
 * @author: James Murray <jaimz@vertigolabs.org>
 * @date: 11/5/2017
 * @time: 7:03 AM
 */
class TestWorker extends AbstractWorker
{

    /**
     * @var \VertigoLabs\Ghost\Services\TestService
     */
    private $testService;

    public function __construct(TestService $testService)
    {
        $this->testService = $testService;

        $config = new WorkerConfiguration();
        $config->setAppName('Test Worker');
        $this->setConfiguration($config);
        $this->valid = true;
    }

    public function startUp()
    {
        $this->console->green('Starting');
    }

    public function wake()
    {
        $this->console->green('Waking');
    }

    public function execute()
    {
        $this->console->green('Executing');
        $this->console->out($this->testService->speak());
        usleep(500);
    }

    public function sleep()
    {
        $this->console->green('Sleeping');
    }

    public function shutdown()
    {
        $this->console->green('Shutting Down');
    }
}