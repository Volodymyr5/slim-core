<?php

namespace App\Controllers\Index;

use \App\Controllers\CoreController;
/**
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends CoreController
{
    public function index($request, $response)
    {
        return $this->view->render($response, 'index\index\index.twig');
    }

    public function test($request, $response, $args)
    {
        return $this->view->render($response, 'index\index\test.twig', [
            'args' => $args
        ]);
    }
}
