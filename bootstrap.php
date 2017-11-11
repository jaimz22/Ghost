<?php
/**
 * @author: James Murray <jaimz@vertigolabs.org>
 * @date: 11/4/2017
 * @time: 7:28 AM
 */

date_default_timezone_set('America/New_York');

/**
 * @param string $path
 *
 * @return string
 */
function base_path($path = '')
{
    return __DIR__ . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

include_once 'vendor/autoload.php';

/** Build the dependency injection */
$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions([
    \VertigoLabs\Ghost\Ghost::class => \DI\object()
                        ->constructor(DI\get(\League\CLImate\CLImate::class), \DI\get(\VertigoLabs\Ghost\Infrastructure\Logger\MultiLogger::class)),

    'VertigoLabs\\Ghost\\Workers\\*' => DI\object()
                        ->method('init',DI\get(\League\CLImate\CLImate::class), DI\get(\VertigoLabs\Ghost\Infrastructure\Logger\WorkerLogger::class)),

    \VertigoLabs\Ghost\Infrastructure\Logger\ConsoleLogger::class => \DI\object()
                        ->property('console',DI\get(\League\CLImate\CLImate::class))
]);
$container = $containerBuilder->build();
$console = $container->get(\League\CLImate\CLImate::class);