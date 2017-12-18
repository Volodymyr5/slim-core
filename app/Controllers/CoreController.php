<?php

namespace App\Controllers;

/**
 * Class CoreController
 * @package App\Controllers
 */
class CoreController
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __get($property)
    {
        if($this->container->{$property}) {
            return $this->container->{$property};
        }
    }
}