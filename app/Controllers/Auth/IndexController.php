<?php

namespace App\Controllers\Auth;

use \App\Controllers\CoreController;
use App\Models\User;

/**
 * Class IndexController
 * @package App\Controllers\Auth
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

        /*$mailer = $this->getMailer();
        if ($mailer) {
            $mailer->to('your.easy.choice@gmail.com');
            $mailer->from('bornfree@ukr.net', 'Vladimir K1.'); // email is required, name is optional
            $mailer->subject('Hello Vova!');
            $mailer->body('This is a <b>HTML</b> email.');
            $result = $mailer->send();

            var_dump($result);
        }*/

        return $this->view->render($response, 'index\index\index.twig', [
            'form' => $form
        ]);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function register($request, $response)
    {
        $form = $this->getForm('App\Forms\RegisterForm');

        if ($request->isPost()) {
            $data = $request->getParams();
            $form->setData($data);
            $isValid = $form->isValid();
            if ($isValid) {
                echo "Success!";
            }
        }

        return $this->view->render($response, 'auth\index\register.twig', [
            'form' => $form
        ]);
    }
}
