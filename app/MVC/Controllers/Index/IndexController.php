<?php

namespace App\MVC\Controllers\Index;

use \App\Core\CoreController;

/**
 * Class IndexController
 * @package App\MVC\Controllers\Index
 */
class IndexController extends CoreController
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function index($request, $response)
    {
        $u = $this->getModel('User');

        $users = $u->getAll();

        return $this->view->render($response, 'index\index\index.twig', [
            'users' => $users
        ]);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function admin($request, $response)
    {
        return $this->view->render($response, 'index\index\index.twig', [
            'users' => 'admin'
        ]);
    }
}
