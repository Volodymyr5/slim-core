<?php

namespace App\MVC\ViewHelpers;

/**
 * Class ShowFlashMessagesHelper
 * @package App\MVC\ViewHelpers
 */
class ShowFlashMessagesHelper
{
    /**
     * Container
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * ShowFlashMessagesHelper constructor.
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

        $messagesSet = $this->container->flash->getMessages();

        if (is_array($messagesSet) && count($messagesSet) > 0) {
            foreach ($messagesSet as $status => $messages) {
                if (is_array($messages) && count($messages) > 0) {
                    foreach ($messages as $message) {
                        if (stristr($status, 'alert-')) {
                            echo '<div class="uk-' . $status . ' uk-width-1-1 uk-width-2-3@s uk-width-1-2@m uk-margin-auto" uk-alert>';
                            echo '    <a class="uk-alert-close" uk-close></a> ' . $message;
                            echo '</div>';
                        } else {
                            echo "<script type=\"text/javascript\">UIkit . notification({message: '" . $message . "', status: '" . $status . "'});</script>\n";
                        }
                    }
                }
            }
        }

        $sfmContent = ob_get_contents();
        ob_end_clean();

        return $sfmContent;
    }
}