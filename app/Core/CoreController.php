<?php

namespace App\Core;
use App\Core\Libs\JwtAuth;
use App\Core\Libs\SMTP;
use Psr\Container\ContainerInterface;

/**
 * Class CoreController
 * @package App\Core
 */
class CoreController
{
    protected $container;

    protected $auth;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->auth = new JwtAuth($container);
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
     * @param array $params
     * @return bool
     * @throws \Exception
     */
    protected function sendMail($params = [])
    {
        $params['to'] = !empty($params['to']) ? $params['to'] : [];
        $params['to'] = (is_string($params['to'])) ? [$params['to']] : $params['to'];
        $params['copy'] = !empty($params['copy']) ? $params['copy'] : [];
        $params['copy'] = (is_string($params['copy'])) ? [$params['copy']] : $params['copy'];
        $params['hidden_copy'] = !empty($params['hidden_copy']) ? $params['hidden_copy'] : [];
        $params['hidden_copy'] = (is_string($params['hidden_copy'])) ? [$params['hidden_copy']] : $params['hidden_copy'];
        $params['from_name'] = !empty($params['from_name']) ? $params['from_name'] : null;
        $params['subject'] = !empty($params['subject']) ? $params['subject'] : null;
        $params['body'] = !empty($params['body']) ? $params['body'] : null;
        $params['attachments'] = !empty($params['attachments']) ? $params['attachments'] : [];
        $params['attachments'] = (is_string($params['attachments'])) ? [$params['attachments']] : $params['attachments'];

        \App\Core\Libs\Logger::log($params);
        return true;

        if ($params['to'] && $params['subject'] && $params['body']) {
            $mailer = $this->getMailer($params['from_name']);
            if ($mailer) {
                // to
                foreach ($params['to'] as $recipient) {
                    $mailer->to($recipient);
                }
                // copy
                foreach ($params['copy'] as $recipient) {
                    $mailer->cc($recipient);
                }
                // hidden_copy
                foreach ($params['hidden_copy'] as $recipient) {
                    $mailer->bcc($recipient);
                }
                // subject
                $mailer->subject($params['subject']);
                // body
                $mailer->body($params['body']);
                // attachments
                foreach ($params['attachments'] as $attachment) {
                    $mailer->attach($attachment);
                }

                ob_start();
                $result = $mailer->send();
                ob_clean();
            } else {
                if ($params['copy']) {
                    $params['to'] = array_merge($params['to'], $params['copy']);
                }
                $params['to'] = implode(', ', $params['to']);

                @mail($params['to'], $params['subject'], $params['body']);

                foreach ($params['hidden_copy'] as $recipient) {
                    @mail($recipient, $params['subject'], $params['body']);
                }
            }

            return true;
        } else {
            throw new \Exception('CoreController->sendMail: to, subject and body parameters is required!');
        }
    }

    /**
     * @param null $fromName
     * @return SMTP|bool
     */
    protected function getMailer($fromName = null)
    {
        $config = $this->getConfig();

        if (!empty($config['smtp']['connections']['primary']['user'])) {
            $mailer = new SMTP($config['smtp']);
            $mailer->from($config['smtp']['connections']['primary']['user'], $fromName);

            return $mailer;
        } else {
            return false;
        }
    }
}