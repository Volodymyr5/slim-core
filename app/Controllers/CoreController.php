<?php

namespace App\Controllers;
use App\Libs\SMTP;

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
     * @return mixed
     */
    public function __get($property)
    {
        if($this->container->{$property}) {
            return $this->container->{$property};
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
     * @return array
     */
    protected function getConfig()
    {
        return isset($this->container->settings['custom']) ? $this->container->settings['custom'] : [];
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