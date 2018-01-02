<?php

namespace App\Controllers\Index;

use \App\Controllers\CoreController;
use App\Models\User;

/**
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends CoreController
{
    public function index($request, $response)
    {
        $users = User::count();

        var_dump($users);

        return $this->view->render($response, 'index\index\index.twig');
    }

    public function test($request, $response, $args)
    {
        return $this->view->render($response, 'index\index\test.twig', [
            'args' => $args
        ]);
    }
}
