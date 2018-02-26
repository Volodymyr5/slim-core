<?php

namespace App\MVC\ViewHelpers;

/**
 * Class GetConfigHelper
 * @package App\MVC\ViewHelpers
 */
class GetConfigHelper
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
    protected $configName;

    /**
     * GetConfigHelper constructor.
     * @param \Psr\Container\ContainerInterface $container
     * @param bool $configName
     */
    public function __construct($container, $configName = false)
    {
        $this->container = $container;
        $this->configName = $configName;
    }

    /**
     * @return array|mixed
     */
    public function render()
    {
        $allConfig = isset($this->container->settings['custom']) ? $this->container->settings['custom'] : [];

        if ($this->configName && isset($allConfig[$this->configName])) {
            return $allConfig[$this->configName];
        } else {
            return $allConfig;
        }
    }
}
