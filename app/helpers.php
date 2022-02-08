<?php

if (! function_exists('escape_like')) {
    /*
     * Escapes string for use in MySQL LIKE condition.
     */
    function escape_like(string $string, bool $wrap = true): string
    {
        $search = ['%', '_'];
        $replace = ['\%', '\_'];
        $result = str_replace($search, $replace, $string);
        if ($wrap) {
            $result = '%'.$result.'%';
        }
        return $result;
    }
}
