<?php

namespace ShowHeroes\Passport\Tests\Fixtures;

trait QueryStringBuilder
{

    private function buildQueryString(array $parameters): string
    {
        if (empty($parameters)) {
            return '';
        }

        return '?' . http_build_query($parameters);
    }
}
