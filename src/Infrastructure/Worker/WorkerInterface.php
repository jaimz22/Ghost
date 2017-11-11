<?php
/**
 * @author: James Murray <jaimz@vertigolabs.org>
 * @date: 11/4/2017
 * @time: 7:50 AM
 */

namespace VertigoLabs\Ghost\Infrastructure\Worker;

use VertigoLabs\Ghost\Infrastructure\Worker\Config\WorkerConfiguration;

interface WorkerInterface
{
    const Signal_Continue = 'continue';
    const Signal_Die = 'die';
    const Signal_Stall = 'stall';
    const Signal_Sleep = 'sleep';

    public function setWorkerArguments(array $arguments);

    /**
     * @param \VertigoLabs\Ghost\Infrastructure\Worker\Config\WorkerConfiguration $configuration
     *
     * @return void
     */
    public function setConfiguration(WorkerConfiguration $configuration);
    /**
     * @return \VertigoLabs\Ghost\Infrastructure\Worker\Config\WorkerConfiguration
     */
    public function getConfiguration();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getUUID();

    public function startUp();
    public function wake();
    public function execute();
    public function sleep();
    public function shutdown();
    public function isValid();
    public function isInteractive();
    public function isTest();

    public function getStallTime();
    public function getSleepTime();
    public function getMaxIterationCount();
}