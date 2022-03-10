<?php

namespace ShowHeroes\SspMapping\Exceptions;

interface SentryReportableException
{
    public function shouldBeReportedToSentry();
}
