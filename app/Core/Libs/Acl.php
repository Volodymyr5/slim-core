<?php

namespace App\Core\Libs;
use App\Core\Constant;
use App\Core\CoreAcl;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Acl
 * @package App\Core\Libs
 */
class Acl extends CoreAcl
{
    /**
     * Acl constructor.
     * @param ContainerInterface $container
     * @param Request $request
     * @param Response $response
     * @throws \Exception
     */
    public function __construct(ContainerInterface $container, Request $request, Response $response)
    {
        $this->addAllowedRole('USER', Constant::ROLE_USER);
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
        $this->set(self::ROUTE_ALLOW, 'forgot-password', $this->getRoles('GUEST'));
        $this->set(self::ROUTE_ALLOW, 'register', $this->getRoles('GUEST'));
        $this->set(self::ROUTE_ALLOW, 'logout', $this->getRoles());
        $this->set(self::ROUTE__DENY, 'logout', $this->getRoles('GUEST'));
        $this->set(self::ROUTE_ALLOW, 'set-password', $this->getRoles());
        // Admin
        $this->set(self::ROUTE_ALLOW, 'admin', $this->getRoles('ADMIN'));
        $this->set(self::ROUTE_ALLOW, 'admin-users', $this->getRoles('ADMIN'));
        $this->set(self::ROUTE_ALLOW, 'admin-users-edit', $this->getRoles('ADMIN'));
    }
}