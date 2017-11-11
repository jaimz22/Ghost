#!/usr/bin/php -dxdebug.remote_autostart=Off
<?php
/**
 * @author: James Murray <jaimz@vertigolabs.org>
 * @date: 11/4/2017
 * @time: 7:27 AM
 *
 * To debug over CLI run the following:
 * export PHP_IDE_CONFIG="serverName=ghost"
 * export XDEBUG_CONFIG="remote_enable=1 remote_mode=req remote_port=9000 remote_host=192.168.1.120 remote_connect_back=0"
 */

require_once 'bootstrap.php';


try{
    $ghost = $container->get(\VertigoLabs\Ghost\Ghost::class);
    $ghost->run();

}catch (\Exception $exception) {
    $console->error($exception->getMessage());
}
