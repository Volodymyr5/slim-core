<?php

namespace App\MVC\ViewHelpers;

use App\Core\Constant;

/**
 * Class IsXhr
 * @package App\MVC\ViewHelpers
 */
class IsXhr
{
    /**
     * Container
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * IsXhr constructor.
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @return bool
     */
    public function render()
    {
        return $this->container->get('request')->isXhr();
    }
}
