<?php

namespace App\Core;
use Psr\Container\ContainerInterface;

/**
 * Class CoreModel
 * @package App\Core
 */
class CoreModel {
    protected $container;
    protected $db;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container['db'];
    }

    /**
     * @param \BaseQuery $query
     * @return array
     */
    protected function extract(\BaseQuery $query, $extractOneRow = false)
    {
        $result = [];
        foreach ($query as $row) {
            $result[] = $row;
        }

        if ($extractOneRow) {
            $result = !empty($result[0]) ? $result[0] : [];
        }

        return $result;
    }
}