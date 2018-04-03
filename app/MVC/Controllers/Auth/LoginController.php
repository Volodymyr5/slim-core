<?php

namespace App\MVC\Controllers\Auth;

use \App\Core\CoreController;
use App\MVC\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LoginController
 * @package App\MVC\Controllers\Auth
 */
class LoginController extends CoreController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function index(Request $request, Response $response)
    {
        $u = new User($this->container);

        $form = $this->getForm('App\Forms\LoginForm');

        if ($request->isPost()) {
            $data = $request->getParams();
            $form->setData($data);
            $isValid = $form->isValid();
            if ($isValid) {
                $formData = $form->getData();
                $currUser = $u->getByField('email', $formData['email']);

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
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function logout (Request $request, Response $response)
    {
        $this->container->user->logout();

        $this->container->flash->addMessage(
            'alert-success',
            '<h3>You successfully logged out!</h3>'
        );

        return $response->withRedirect($this->router->pathFor('home'));
    }
}
