<?php

namespace App\Controllers;
use App\Core\Libs\SMTP;

/**
 * Class CoreController
 * @package App\Controllers
 */
class CoreController
{
    /**
     * Container
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * CoreController constructor.
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param $property
     * @return bool
     */
    public function __get($property)
    {
        if($this->container->{$property}) {
            return $this->container->{$property};
        } else {
            return false;
        }
    }

    /**
     * @param $formNameWithNamespace
     * @return \Zend\Form\FormInterface
     */
    protected function getForm($formNameWithNamespace)
    {
        $containerServiceManager = $this->container['serviceManager'];
        $formElementManager = $containerServiceManager->get('FormElementManager');

        return $formElementManager->get($formNameWithNamespace);
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

    /**
     * @return SMTP|bool
     */
    protected function getMailer()
    {
        $config = $this->getConfig();

        if (isset($config['smtp']['connections'])) {
            return new SMTP($config['smtp']);
        } else {
            return false;
        }
    }
}