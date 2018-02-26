<?php

namespace App\MVC\ViewHelpers;

/**
 * Class AssetsHelper
 * @package App\MVC\ViewHelpers
 */
class AssetsHelper
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
    protected $path;

    /**
     * AssetsHelper constructor.
     * @param \Psr\Container\ContainerInterface $container
     * @param string $path
     */
    public function __construct($container, $path = '')
    {
        $this->container = $container;
        $this->path = trim($path, '/');
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->container->request->getUri()->getBasePath() . '/assets/' . $this->path;
    }
}
