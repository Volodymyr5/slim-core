<?php

namespace App\Core;
use App\Core\Constant;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class CoreAcl
 * @package App\Core\Libs
 */
class CoreAcl
{
    private $container;
    private $request;
    private $response;
    private $route;
    private $routeName;
    private $rules;
    private $allowedRoles;

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
        $this->allowedRoles = !empty($this->allowedRoles) ? $this->allowedRoles : [];

        $this->addAllowedRole('GUEST', 1);

        $this->rules();
    }

    /**
     * @param null $route
     * @param null $role
     * @return bool
     */
    public function isAllowed($route = null, $role = null)
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
     * @throws \Exception
     */
    private function rules()
    {
        $this->set(self::ROUTE_ALLOW, 'home', $this->getRoles());
    }

    /**
     * @param $allowOrDeny
     * @param $route
     * @param $roles
     * @throws \Exception
     */
    protected function set($allowOrDeny, $route, $roles)
    {
        $allowOrDeny = is_string($allowOrDeny) ? mb_strtolower($allowOrDeny, 'UTF-8') : '';
        if (!in_array($allowOrDeny, ['allow', 'deny'])) {
            throw new \Exception('ACL->set() $allowOrDeny should be "allow" or "deny"');
        }

        if (!is_string($route) || empty($route)) {
            throw new \Exception('ACL->set() $route should be String');
        }

        if ((!in_array($roles, $this->allowedRoles) && !is_array($roles)) || empty($roles)) {
            throw new \Exception('ACL->set() $roles should be Number or Array of Numbers');
        }

        $roles = is_string($roles) ? [$roles] : $roles;

        $this->rules[] = [
            'type' => $allowOrDeny == 'allow',
            'route' => $route,
            'roles' => $roles,
        ];
    }

    /**
     * @param $roleName
     * @param $roleId
     * @throws \Exception
     */
    protected function addAllowedRole($roleName, $roleId)
    {
        if (!is_string($roleName) || empty($roleName)) {
            throw new \Exception('ACL->addAllowedRole() $roleName should be String');
        }

        if (!is_numeric($roleId) || $roleId <= 0) {
            throw new \Exception('ACL->addAllowedRole() $roleId should be Number, greater than 0');
        }

        $this->allowedRoles[$roleName] = $roleId;
    }

    /**
     * @param null $roleName
     * @return array
     * @throws \Exception
     */
    protected function getRoles($roleName = null)
    {
        if ($roleName != null && (!is_string($roleName) || empty($roleName))) {
            throw new \Exception('ACL->getRoles() $roleName should be String');
        }

        if (isset($this->allowedRoles[$roleName])) {
            return $this->allowedRoles[$roleName];
        } else {
            return $this->allowedRoles;
        }
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
            return $this->getRoles('GUEST');
        }
    }
}