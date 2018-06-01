<?php
namespace Product\Service;


class Validator
{
    public static function isValidDate(string $date) : bool
    {
        return preg_match('/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]T[0-2][0-9]:[0-5][0-9]$/', $date);
    }
}