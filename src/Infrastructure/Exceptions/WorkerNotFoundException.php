<?php
/**
 * @author: James Murray <jaimz@vertigolabs.org>
 * @date: 11/11/2017
 * @time: 10:27 AM
 */

namespace VertigoLabs\Ghost\Infrastructure\Exceptions;

use RuntimeException;


class WorkerNotFoundException extends RuntimeException
{
    public function __construct($worker)
    {
        parent::__construct(sprintf('The worker "%s" can not be found',$worker));
    }
}