<?php

namespace App\Core;

interface EntityInterface
{
    public function toArray();
    public function exchangeArray(array $data);
}