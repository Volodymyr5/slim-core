<?php

namespace App\Core;
use Psr\Container\ContainerInterface;

/**
 * Class CoreModel
 * @package App\Core
 */
class CoreModel {
    protected $container;

    protected $accessTokenExpire;

    protected $refreshTokenExpire;

    /**
     * CoreController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param bool $configName
     * @return array|mixed
     */
    protected function getConfig($configName = false)
    {
        $allConfig = isset($this->container->settings['custom']) ? $this->container->settings['custom'] : [];

        if ($configName && isset($allConfig[$configName])) {
            return $allConfig[$configName];
        } else {
            return $allConfig;
        }
    }
}