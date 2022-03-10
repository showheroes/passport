<?php

namespace ShowHeroes\SspMapping\Exceptions;

use Exception;

/**
 * Class StrategySyncSspException
 * @package ShowHeroes\SspMapping\Exceptions
 */
class StrategySyncSspException extends Exception implements SentryReportableException
{
    public function shouldBeReportedToSentry()
    {
        return true;
    }
}
