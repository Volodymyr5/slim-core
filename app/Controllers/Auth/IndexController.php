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
                $data = $form->getData();
                $user = \Model::factory('\App\Models\User')->create();
                $user->email = $data['email'];
                $user->password = password_hash($data['email'] . $data['password'], PASSWORD_DEFAULT);
                $user->created = date('Y-m-d H:i:s');
                $user->updated = date('Y-m-d H:i:s');
                $user->save();

                $userMeta = $user->userMeta()->create();
                $userMeta->user_id = $user->id;
                $userMeta->first_name = $data['first_name'];
                $userMeta->last_name = $data['last_name'];
                $userMeta->register_confirm_token = password_hash($data['email'] . date('U'), PASSWORD_DEFAULT);
                $userMeta->save();

                echo "Success!";
            }
        }

        return $this->view->render($response, 'auth\index\register.twig', [
            'form' => $form
        ]);
    }
}
