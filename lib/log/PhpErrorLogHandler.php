<?php
namespace Atwood\lib\log;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * Record to the php error log
 */
class PhpErrorLogHandler extends AbstractProcessingHandler
{
    /**
     * @param integer $level The minimum logging level at which this handler will be triggered
     */
    public function __construct($level = Logger::WARNING)
    {
        parent::__construct($level, false);
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
		error_log((string)$record['formatted']);
    }
}