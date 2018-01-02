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

    public function test($request, $response, $args)
    {
        return $this->view->render($response, 'index\index\test.twig', [
            'args' => $args
        ]);
    }
}
