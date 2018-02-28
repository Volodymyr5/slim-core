<?php

namespace App\MVC\Controllers\Auth;

use \App\Core\CoreController;
use App\MVC\Models\User;

/**
 * Class LoginController
 * @package App\MVC\Controllers\Auth
 */
class LoginController extends CoreController
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     * @throws \Exception
     */
    public function index($request, $response)
    {
        $u = new User();

        $form = $this->getForm('App\Forms\UserForm');

        if ($request->isPost()) {
            $data = $request->getParams();
            $form->setData($data);
            $isValid = $form->isValid();
            if ($isValid) {

                echo 'login';

                exit;
            }
        }

        return $this->view->render($response, 'auth\login\index.twig', [
            'form' => $form
        ]);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function logout ($request, $response)
    {
        $this->container->flash->addMessage(
            'alert-success',
            '<h3>You successfully logged out!</h3>'
        );

        return $response->withRedirect($this->router->pathFor('home'));
    }
}
