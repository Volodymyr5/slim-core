<?php

namespace App\MVC\ViewHelpers;

/**
 * Class IsRouteAllowed
 * @package App\MVC\ViewHelpers
 */
class IsRouteAllowed
{
    /**
     * Container
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $routeName;

    /**
     * IsRouteAllowed constructor.
     * @param \Psr\Container\ContainerInterface $container
     * @param string $routeName
     */
    public function __construct($container, $routeName = '')
    {
        $this->container = $container;
        $this->routeName = $routeName;
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->container->acl->isAllowed($this->routeName);
    }
}
