<?php

namespace App\MVC\ViewHelpers;

use App\Core\Constant;

/**
 * Class IsRole
 * @package App\MVC\ViewHelpers
 */
class IsRole
{
    /**
     * Container
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $roles;

    /**
     * IsRouteAllowed constructor.
     * @param \Psr\Container\ContainerInterface $container
     * @param string $roles
     */
    public function __construct($container, $roles = '')
    {
        $this->container = $container;

        $roles = is_array($roles) ? $roles : [$roles];
        $this->roles = [];

        $aclRoles = $container->acl->getRoles();

        foreach ($roles as $role) {
            if (isset($aclRoles[$role])) {
                $this->roles[] = $aclRoles[$role];
            }
        }
    }

    /**
     * @return bool
     */
    public function render()
    {
        $identity = $this->container->user->getIdentity();
        $acl = $this->container->acl;

        $currRole = isset($identity['role']) ? $identity['role'] : $acl::GUEST_ROLE;
        foreach ($this->roles as $role) {
            if ($currRole == $role) {
                return true;
            }
        }

        return false;
    }
}
