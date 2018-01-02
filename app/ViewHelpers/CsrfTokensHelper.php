<?php

namespace App\ViewHelpers;

/**
 * Class CsrfTokensHelper
 * @package App\ViewHelpers
 */
class CsrfTokensHelper
{
    /**
     * Container
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * CsrfTokensHelper constructor.
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
        ob_start();

        echo '<input type="hidden" name="' . $this->container->csrf->getTokenNameKey() . '" value="' . $this->container->csrf->getTokenName() . '">';
        echo '<input type="hidden" name="' . $this->container->csrf->getTokenValueKey() . '" value="' . $this->container->csrf->getTokenValue() . '">';

        $csrfContent = ob_get_contents();
        ob_end_clean();

        return $csrfContent;
    }
}