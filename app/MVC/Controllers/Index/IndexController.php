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
        $at = !empty($_COOKIE['at']) ? $this->container->user->readToken($_COOKIE['at']) : null;
        $rt = !empty($_COOKIE['rt']) ? $this->container->user->readToken($_COOKIE['rt']) : null;

        $at['time'] = !empty($at['te']) ? date('H:i:s d.m.Y', $at['te']) : null;
        $rt['time'] = !empty($rt['te']) ? date('H:i:s d.m.Y', $rt['te']) : null;

        $u = $this->getModel('User');

        $users = $u->getAll();

        return $this->view->render($response, 'index\index\index.twig', [
            'users' => $users
        ]);
    }
}
