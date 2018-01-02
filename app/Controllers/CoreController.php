<?php

namespace App\Controllers;

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
    public function getForm($formNameWithNamespace)
    {
        $containerServiceManager = $this->container['serviceManager'];
        $formElementManager = $containerServiceManager->get('FormElementManager');

        return $formElementManager->get($formNameWithNamespace);
    }
}