<?php
/**
 * @author: James Murray <jaimz@vertigolabs.org>
 * @date: 11/4/2017
 * @time: 8:06 AM
 */

namespace VertigoLabs\Ghost\Infrastructure\Worker\Config;


final class WorkerConfiguration implements \JsonSerializable
{
    /**
     * Whether to use the PEAR System Daemon Library (default is false)
     * @var boolean
     */
    private $usePEAR = false;
    /**
     * The name of the app (the daemon/worker name)
     * @var string
     */
    private $appName;
    /**
     * The description of the app (the deamon/worker description)
     * @var string
     */
    private $appDescription;
    /**
     * The name of the original author
     * @var string
     */
    private $authorName;
    /**
     * The email of the original author
     * @var string
     */
    private $authorEmail;
    /**
     * The maximum amount of time, in seconds, the worker will be allowed to run.
     * 0 (zero) means no time limit
     * @var int
     */
    private $sysMaxExecutionTime;
    /**
     * The maximum amount of time, in seconds, the worker will spend parsing request data
     * 0 (zero) means no time limit
     * @var int
     */
    private $sysMaxInputTime;
    /**
     * The maximum amount of memory a worker is allowed to consume.
     * 0 (zero) means no time limit
     * @var string
     */
    private $sysMemoryLimit = '128M';
    /**
     * The user id under which to run the process
     * Defaults to root which is insecure!
     * @var int
     */
    private $appRunAsUID;
    /**
     * The group id under which to run the process
     * Defaults to root which is insecure!
     * @var int
     */
    private $appRunAsGID;
    /**
     * The log filepath
     * @var string
     */
    private $logLocation;

    /**
     * The maximum number of iterations a worker can perform before sleeping
     * @var int
     */
    private $maxIterationCount = 4;

    /**
     * The number of seconds to stall a worker
     * @var int
     */
    private $stallTime = 15;

    /**
     * The number of seconds a worker will sleep
     * @var int
     */
    private $sleepTime = 3600;

    /**
     * @return bool
     */
    public function isUsePEAR()
    {
        return $this->usePEAR;
    }

    /**
     * @param bool $usePEAR
     *
     * @return WorkerConfiguration
     */
    public function setUsePEAR($usePEAR)
    {
        $this->usePEAR = $usePEAR;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function getSanitizedAppName()
    {
        $appName = $this->appName;

        // replace non letter or digits by -
        $appName = preg_replace('~[^\\pL\d]+~u', '-', $appName);
        // trim
        $appName = trim($appName, '-');
        // transliterate
        if (function_exists('iconv')){
            $appName = iconv('utf-8', 'us-ascii//TRANSLIT', $appName);
        }
        // lowercase
        $appName = strtolower($appName);
        // remove unwanted characters
        $appName = preg_replace('~[^-\w]+~', '', $appName);
        if (empty($appName)) {
            throw new \RuntimeException(sprintf('Unsafe app name specified "%s"',$this->appName));
        }
        return $appName;
    }

    /**
     * @param string $appName
     *
     * @return WorkerConfiguration
     * @throws \RuntimeException
     */
    public function setAppName($appName)
    {
        $this->appName = strtolower($appName);
        // run this here to check to make sure the app name is safe
        $this->getSanitizedAppName();
        return $this;
    }

    /**
     * @return string
     */
    public function getAppDescription()
    {
        return $this->appDescription;
    }

    /**
     * @param string $appDescription
     *
     * @return WorkerConfiguration
     */
    public function setAppDescription($appDescription)
    {
        $this->appDescription = $appDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param string $authorName
     *
     * @return WorkerConfiguration
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * @param string $authorEmail
     *
     * @return WorkerConfiguration
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
        return $this;
    }

    /**
     * @return int
     */
    public function getSysMaxExecutionTime()
    {
        return $this->sysMaxExecutionTime;
    }

    /**
     * @param int $sysMaxExecutionTime
     *
     * @return WorkerConfiguration
     */
    public function setSysMaxExecutionTime($sysMaxExecutionTime)
    {
        $this->sysMaxExecutionTime = $sysMaxExecutionTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getSysMaxInputTime()
    {
        return $this->sysMaxInputTime;
    }

    /**
     * @param int $sysMaxInputTime
     *
     * @return WorkerConfiguration
     */
    public function setSysMaxInputTime($sysMaxInputTime)
    {
        $this->sysMaxInputTime = $sysMaxInputTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getSysMemoryLimit()
    {
        return $this->sysMemoryLimit;
    }

    /**
     * @param string $sysMemoryLimit
     *
     * @return WorkerConfiguration
     */
    public function setSysMemoryLimit($sysMemoryLimit)
    {
        $this->sysMemoryLimit = $sysMemoryLimit;
        return $this;
    }

    /**
     * @return int
     */
    public function getAppRunAsUID()
    {
        return $this->appRunAsUID;
    }

    /**
     * @param int $appRunAsUID
     *
     * @return WorkerConfiguration
     */
    public function setAppRunAsUID($appRunAsUID)
    {
        $this->appRunAsUID = $appRunAsUID;
        return $this;
    }

    /**
     * @return int
     */
    public function getAppRunAsGID()
    {
        return $this->appRunAsGID;
    }

    /**
     * @param int $appRunAsGID
     *
     * @return WorkerConfiguration
     */
    public function setAppRunAsGID($appRunAsGID)
    {
        $this->appRunAsGID = $appRunAsGID;
        return $this;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function getLogLocation()
    {
        if (null === $this->logLocation) {
            $this->logLocation = '/var/log/'.$this->getSanitizedAppName().'.log';
        }
        return $this->logLocation;
    }

    /**
     * @param string $logLocation
     */
    public function setLogLocation($logLocation)
    {
        $this->logLocation = $logLocation;
    }

    /**
     * @return int
     */
    public function getMaxIterationCount()
    {
        return $this->maxIterationCount;
    }

    /**
     * @param int $maxIterationCount
     */
    public function setMaxIterationCount($maxIterationCount)
    {
        $this->maxIterationCount = $maxIterationCount;
    }

    /**
     * @return int
     */
    public function getStallTime()
    {
        return $this->stallTime;
    }

    /**
     * @param int $stallTime
     */
    public function setStallTime($stallTime)
    {
        $this->stallTime = $stallTime;
    }

    /**
     * @return int
     */
    public function getSleepTime()
    {
        return $this->sleepTime;
    }

    /**
     * @param int $sleepTime
     */
    public function setSleepTime($sleepTime)
    {
        $this->sleepTime = $sleepTime;
    }

    public function toArray()
    {
        return [
            'usePEAR'=>$this->usePEAR,
            'appName'=>$this->getAppName(),
            'appDescription'=>$this->getAppDescription(),
            'authorName'=>$this->getAuthorName(),
            'authorEmail'=>$this->getAuthorEmail(),
            'sysMaxExecutionTime'=>$this->getSysMaxExecutionTime(),
            'sysMaxInputTime'=>$this->getSysMaxInputTime(),
            'sysMemoryLimit'=>$this->getSysMemoryLimit(),
            'appRunAsGID'=>$this->getAppRunAsGID(),
            'appRunAsUID'=>$this->getAppRunAsUID(),
            'logLocation'=>$this->getLogLocation(),
            'maxIterationCount'=>$this->getMaxIterationCount(),
            'stallTime'=>$this->getStallTime(),
            'sleepTime'=>$this->getSleepTime()
        ];
    }

    public function toDaemonOptions()
    {
        return [
            'usePEAR'=>$this->usePEAR,
            'appName'=>$this->getAppName(),
            'appDescription'=>$this->getAppDescription(),
            'authorName'=>$this->getAuthorName(),
            'authorEmail'=>$this->getAuthorEmail(),
            'sysMaxExecutionTime'=>$this->getSysMaxExecutionTime(),
            'sysMaxInputTime'=>$this->getSysMaxInputTime(),
            'sysMemoryLimit'=>$this->getSysMemoryLimit(),
            'appRunAsGID'=>$this->getAppRunAsGID(),
            'appRunAsUID'=>$this->getAppRunAsUID(),
            'logLocation'=>$this->getLogLocation(),
        ];
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}