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

    public function register($request, $response)
    {

    }

    public function logout($request, $response)
    {

    }

    public function confirm($request, $response)
    {

    }
}
