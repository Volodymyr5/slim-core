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
        $form = $this->getForm('App\Forms\UserForm');

        if ($request->isPost()) {
            $data = $request->getParams();
            $form->setData($data);
            $isValid = $form->isValid();
            if ($isValid) {
                echo "Success!";
                exit;
            }
        }
        $users = User::count();

        var_dump($users);

        return $this->view->render($response, 'index\index\index.twig', [
            'form' => $form
        ]);
    }

    public function test($request, $response, $args)
    {
        return $this->view->render($response, 'index\index\test.twig', [
            'args' => $args
        ]);
    }
}
