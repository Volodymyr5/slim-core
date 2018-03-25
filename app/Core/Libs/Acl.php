<?php

namespace App\Core\Libs;
use App\Core\Constant;
use App\Core\CoreAcl;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Acl
 * @package App\Core\Libs
 */
class Acl extends CoreAcl
{
    /**
     * Acl constructor.
     * @param ContainerInterface $container
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @throws \Exception
     */
    public function __construct(ContainerInterface $container, ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->addAllowedRole('USER', Constant::ROLE_USER);
        $this->addAllowedRole('EDITOR', Constant::ROLE_EDITOR);
        $this->addAllowedRole('ADMIN', Constant::ROLE_ADMIN);

        parent::__construct($container, $request, $response);

        $this->rules();
    }

    /**
     * @throws \Exception
     */
    private function rules()
    {
        // Auth
        $this->set(self::ROUTE_ALLOW, 'login', $this->getRoles('GUEST'));
        $this->set(self::ROUTE_ALLOW, 'register', $this->getRoles('GUEST'));
        $this->set(self::ROUTE_ALLOW, 'logout', $this->getRoles());
        $this->set(self::ROUTE__DENY, 'logout', $this->getRoles('GUEST'));
        $this->set(self::ROUTE_ALLOW, 'set-password', $this->getRoles());
        // Index
        $this->set(self::ROUTE_ALLOW, 'admin', $this->getRoles('ADMIN'));
    }
}