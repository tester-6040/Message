<?php

namespace App\Core;

abstract class Model
{
    protected function db(): \PDO
    {
        return Database::connection();
    }
}
