<?php

namespace App\MVC\Controllers\Auth;

use \App\Core\CoreController;
use App\Forms\Validators\IsPasswordTokenValid;
use App\MVC\Entity\UserEntity;

/**
 * Class SetPasswordController
 * @package App\MVC\Controllers\Auth
 */
class SetPasswordController extends CoreController
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function index($request, $response)
    {
        $u = $this->getModel('User');

        $token = $request->getParam('t', '');

        $isPasswordTokenValid = new IsPasswordTokenValid();
        $isPasswordTokenValid->setOptions([
            'container' => $this->container
        ]);

        $form = $this->getForm('App\Forms\SetPasswordForm');

        // set token
        $form->get('token')->setValue($token);

        if ($request->isPost()) {
            $data = $request->getParams();
            $form->setData($data);
            $isValid = $form->isValid();
            if ($isValid) {
                $token = !empty($data['token']) ? $data['token'] : null;
                $user = $u->getAll([
                    'password_token' => $token
                ]);

                if (
                    !empty($user[0]['id']) &&
                    !empty($user[0]['password_token']) &&
                    !empty($data['password'])
                ) {
                    $ue = new UserEntity();
                    $ue->exchangeArray([
                        'id' => $user[0]['id'],
                        'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                        'password_token' => '',
                        'token_expiration' => 0,
                    ]);

                    try {
                        $u->modify($ue);
                    } catch (\Exception $e) {
                        $this->container->flash->addMessage(
                            'alert-warning',
                            '<h3>Something go wrong, try again!</h3>'
                        );
                        return $response->withRedirect($this->router->pathFor('register'));
                    }

                    $this->container->flash->addMessage(
                        'alert-success',
                        '<h3>Your password successfully changed!</h3>'
                    );

                    return $response->withRedirect($this->router->pathFor('login'));
                } else {
                    return $response->withRedirect($this->router->pathFor('set-password'));
                }
            }
        }

        return $this->view->render($response, 'auth\set-password\index.twig', [
            'form' => $form,
            'is_valid_token' => $isPasswordTokenValid->isValid($token)
        ]);
    }
}
