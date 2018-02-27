<?php

namespace App\MVC\Controllers\Auth;

use \App\Core\CoreController;
use App\Forms\Validators\IsPasswordTokenValid;
use App\MVC\Entity\UserEntity;
use App\MVC\Models\User;
use Slim\App;

/**
 * Class IndexController
 * @package App\MVC\Controllers\Auth
 */
class IndexController extends CoreController
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

                echo 'send';

                $this->sendMail([
                    'to' => 'test.com',
                    'subject' => 'Hello! Confirm your email.',
                    'body' => 'Hello! Confirm your email.',
                    'from_name' => 'No reply'
                ]);

                echo "Success!";
                exit;
            }
        }
        $users = $u->getAll([
            'email' => 'your.easy.choice@gmail.com',
            'password_token' => 'ade9d36a7b13e64c1c79b289107a6f2c'
        ]);

        var_dump($users);

        return $this->view->render($response, 'index\index\index.twig', [
            'form' => $form
        ]);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     * @throws \Exception
     */
    public function register($request, $response)
    {
        $u = new User();
        $form = $this->getForm('App\Forms\RegisterForm');

        if ($request->isPost()) {
            $data = $request->getParams();
            $form->setData($data);
            $isValid = $form->isValid();
            if ($isValid) {
                $data = $form->getData();

                $ue = new UserEntity();
                $ue->exchangeArray([
                    'email' => $data['email'],
                    'created' => date('Y-m-d H:i:s'),
                    'updated' => date('Y-m-d H:i:s'),
                    'password_token' => md5(date('U') . $data['first_name'] . date('YmdHis')),
                    'token_expiration' => date('Y-m-d H:i:s', strtotime('now + 12 hours')),
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                ]);

                $u->create($ue);

                $mailBody = $this->view->render($response, 'emails\registration_confirm.twig', [
                    'password_token' => $ue->getPasswordToken()
                ]);

                $userFullName = $ue->getFirstName() . ((strlen($ue->getLastName()) > 0) ? ' ' . $ue->getLastName() : '');

                $projectName = (is_string($this->getConfig('projet_name')) ? $this->getConfig('projet_name') : '');

                try {
                    $this->sendMail([
                        'to' => $ue->getEmail(),
                        'subject' => 'Hello ' . $userFullName . '! Confirm your email. ' . $projectName,
                        'body' => strval($mailBody->getBody()),
                        'from_name' => 'No reply'
                    ]);

                    $this->container->flash->addMessage(
                        'alert-success',
                        '<h3>Thank You for Signing Up!</h3>' .
                        '<p>We\'ve sent you an email with a confirmation link, use it to activate your new account.</p>'
                    );

                    return $response->withRedirect($this->router->pathFor('register'));
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
        }

        return $this->view->render($response, 'auth\index\register.twig', [
            'form' => $form
        ]);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function setPassword($request, $response)
    {
        $token = $request->getParam('t', '');

        $isPasswordTokenValid = new IsPasswordTokenValid();

        $form = $this->getForm('App\Forms\SetPasswordForm');

        // set token
        $form->get('token')->setValue($token);

        if ($request->isPost()) {
            $data = $request->getParams();
            $form->setData($data);
            $isValid = $form->isValid();
            if ($isValid) {
                echo 'Form valid';
                exit;
            }
        }

        return $this->view->render($response, 'auth\index\set-password.twig', [
            'form' => $form,
            'is_valid_token' => $isPasswordTokenValid->isValid($token)
        ]);
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function testEmail($request, $response)
    {
        return $this->view->render($response, 'emails\registration_confirm.twig', [
            'password_token' => '899a36fc1e8f8104c8ab1ed867ecfb65'
        ]);
    }
}
