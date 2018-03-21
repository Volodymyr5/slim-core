<?php

namespace App\Core\Libs;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Acl
 * @package App\Core\Libs
 */
class Acl
{
    private $container;

    private $routeName;

    private $rules;

    private $allowedRoles;

    const ROLE_GUEST = 1;
    const ROLE_USER = 2;
    const ROLE_EDITOR = 3;
    const ROLE_ADMIN = 4;

    /**
     * Acl constructor.
     * @param ServerRequestInterface $request
     * @throws \Exception
     */
    public function __construct(ContainerInterface $container, ServerRequestInterface $request)
    {
        $this->container = $container;

        $route = $request->getAttribute('route');
        $this->routeName = $route ? $route->getName() : '';

        $this->rules = [];

        $this->allowedRoles = [
            self::ROLE_GUEST,
            self::ROLE_USER,
            self::ROLE_EDITOR,
            self::ROLE_ADMIN,
        ];

        $this->rules();
    }

    private function rules()
    {
        // Auth
        $this->set('allow', 'login', self::ROLE_GUEST);
        $this->set('allow', 'register', self::ROLE_GUEST);
        $this->set('allow', 'logout', [self::ROLE_USER, self::ROLE_EDITOR, self::ROLE_ADMIN]);
        $this->set('allow', 'set-password', [self::ROLE_GUEST, self::ROLE_USER, self::ROLE_EDITOR, self::ROLE_ADMIN]);
        // Index
        $this->set('allow', 'home', [self::ROLE_GUEST, self::ROLE_USER, self::ROLE_EDITOR, self::ROLE_ADMIN]);
        $this->set('allow', 'admin', self::ROLE_ADMIN);
    }

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

    public function isAllowed()
    {
        var_dump($this->rules);
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