<?php

namespace App\MVC\ViewHelpers;

/**
 * Class GetSiteUrlHelper
 * @package App\MVC\ViewHelpers
 */
class GetSiteUrlHelper
{
    /**
     * Container
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * GetSiteUrlHelper constructor.
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function render()
    {
        $path = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . trim($_SERVER['HTTP_HOST'], '/');

        return $path;
    }
}
