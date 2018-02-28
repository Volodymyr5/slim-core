<?php

namespace App\MVC\Controllers\Index;

use \App\Core\CoreController;
use App\MVC\Models\User;

/**
 * Class IndexController
 * @package App\MVC\Controllers\Index
 */
class IndexController extends CoreController
{
    /**
     * @param $request
     * @param $response
     * @return
     */
    public function index($request, $response)
    {
        $u = new User();

        $users = $u->getAll();

        return $this->view->render($response, 'index\index\index.twig', [
            'users' => $users
        ]);
    }
}
