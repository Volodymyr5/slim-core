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
        return $this->view->render($response, 'index\index.twig');
    }

    public function test($request, $response, $args)
    {
        var_dump($args);

        //return $this->view->render($response, 'index\index.twig');
    }
}
