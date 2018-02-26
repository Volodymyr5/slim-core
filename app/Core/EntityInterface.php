<?php

namespace App\Core;

/**
 * Interface EntityInterface
 * @package App\Core
 */
interface EntityInterface
{
    /**
     * @return array
     */
    public function toArray();

    /**
     * @param array $data
     */
    public function exchangeArray(array $data);
}