<?php

namespace App\MVC\Controllers\Auth;

use \App\Core\CoreController;

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
     */
    public function index($request, $response)
    {
        $u = $this->getModel('User');

        $form = $this->getForm('App\Forms\LoginForm');

        if ($request->isPost()) {
            $data = $request->getParams();
            $form->setData($data);
            $isValid = $form->isValid();
            if ($isValid) {
                $formData = $form->getData();
                $currUser = $u->getByEmail($formData['email']);
                $currUser = $currUser->asArray();
                if (!empty($currUser['id'])) {
                    $this->container->user->login($currUser['id']);

                    $this->container->flash->addMessage(
                        'alert-success',
                        '<h3>You successfully logged in!</h3>'
                    );

                    return $response->withRedirect($this->router->pathFor('home'));
                }
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
        $this->container->user->logout();

        $this->container->flash->addMessage(
            'alert-success',
            '<h3>You successfully logged out!</h3>'
        );

        return $response->withRedirect($this->router->pathFor('home'));
    }
}
