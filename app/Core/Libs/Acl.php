<?php

namespace App\Core\Libs;
use App\Core\Constant;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Acl
 * @package App\Core\Libs
 */
class Acl
{
    private $container;

    private $request;

    private $response;

    private $route;

    private $routeName;

    private $rules;

    private $allowedRoles;

    const ROLE_GUEST = Constant::ROLE_GUEST;
    const ROLE_USER = Constant::ROLE_USER;
    const ROLE_EDITOR = Constant::ROLE_EDITOR;
    const ROLE_ADMIN = Constant::ROLE_ADMIN;

    const ROUTE_ALLOW = 'allow';
    const ROUTE__DENY = 'deny';

    /**
     * Acl constructor.
     * @param ServerRequestInterface $request
     * @throws \Exception
     */
    public function __construct(ContainerInterface $container, ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->container = $container;

        $this->request = $request;
        $this->response = $response;

        $this->route = $request->getAttribute('route');

        $this->routeName = $this->route ? $this->route->getName() : '';

        $this->rules = [];

        $this->allowedRoles = [
            self::ROLE_GUEST,
            self::ROLE_USER,
            self::ROLE_EDITOR,
            self::ROLE_ADMIN,
        ];

        $this->rules();
    }

    /**
     * @throws \Exception
     */
    private function rules()
    {
        // Auth
        $this->set(self::ROUTE_ALLOW, 'login', self::ROLE_GUEST);
        $this->set(self::ROUTE_ALLOW, 'register', self::ROLE_GUEST);
        $this->set(self::ROUTE_ALLOW, 'logout', [self::ROLE_USER, self::ROLE_EDITOR, self::ROLE_ADMIN]);
        $this->set(self::ROUTE_ALLOW, 'set-password', $this->allowedRoles);
        // Index
        $this->set(self::ROUTE_ALLOW, 'home', $this->allowedRoles);
        $this->set(self::ROUTE_ALLOW, 'admin', self::ROLE_ADMIN);
        $this->set(self::ROUTE_ALLOW, 'test', self::ROLE_ADMIN);
    }

    /**
     * @param null $role
     * @param null $route
     * @return bool
     */
    public function isAllowed($role = null, $route = null)
    {
        $role = $role ? $role : $this->getVisitorRole();
        $route = $route ? $route : $this->routeName;

        $isAllowed = false;
        foreach ($this->rules as $rule) {
            $rule['roles'] = is_array($rule['roles']) ? $rule['roles'] : [$rule['roles']];
            if ($route == $rule['route'] && in_array($role, $rule['roles'])) {
                $isAllowed = $rule['type'];
            }
        }

        return $isAllowed;
    }

    /**
     * @param $allowOrDeny
     * @param $route
     * @param $roles
     * @throws \Exception
     */
    private function set($allowOrDeny, $route, $roles)
    {
        $allowOrDeny = is_string($allowOrDeny) ? mb_strtolower($allowOrDeny, 'UTF-8') : '';
        if (!in_array($allowOrDeny, ['allow', 'deny'])) {
            throw new \Exception('ACL->set() $allowOrDeny should be "allow" or "deny"');
        }

        if (!is_string($route) || empty($route)) {
            throw new \Exception('ACL->set() $route should be String');
        }

        if ((!in_array($roles, $this->allowedRoles) && !is_array($roles)) || empty($roles)) {
            throw new \Exception('ACL->set() $roles should be Number or Array');
        }

        $roles = is_string($roles) ? [$roles] : $roles;

        $this->rules[] = [
            'type' => $allowOrDeny == 'allow',
            'route' => $route,
            'roles' => $roles,
        ];
    }

    /**
     * @return int
     */
    private function getVisitorRole()
    {
        $userIdentity = $this->container->user->getIdentity();

        if (!empty($userIdentity['role']) && in_array($userIdentity['role'], $this->allowedRoles)) {
            return $userIdentity['role'];
        } else {
            return self::ROLE_GUEST;
        }
    }
}