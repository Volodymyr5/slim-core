<?php

namespace App\MVC\Controllers\Auth;

use \App\Core\CoreController;
use App\Core\Libs\Logger;
use App\MVC\Entity\UserEntity;
use App\MVC\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ForgotPasswordController
 * @package App\MVC\Controllers\Auth
 */
class ForgotPasswordController extends CoreController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @throws \Exception
     */
    public function index(Request $request, Response $response)
    {
        $u = new User($this->container);
        $form = $this->getForm('App\Forms\ForgotPasswordForm');

        if ($request->isPost()) {
            $data = $request->getParams();
            $form->setData($data);
            $isValid = $form->isValid();
            if ($isValid) {
                $user = $u->getByField('email', $data['email']);

                if (!empty($user['id'])) {
                    $ue = new UserEntity();
                    $ue->exchangeArray([
                        'id' => $user['id'],
                        'password_token' => md5(date('U') . $user['password'] . date('YmdHis')),
                        'token_expiration' => date('U', strtotime('now + 12 hours')),
                    ]);

                    $u->modify($ue);

                    $user['first_name'] = $user['first_name'] ? ' ' . $user['first_name'] : '';
                    $user['last_name'] = $user['last_name'] ? ' ' . $user['last_name'] : '';
                    $userFullName = $user['first_name'] . $user['last_name'];

                    $projectName = (is_string($this->getConfig('projet_name')) ? $this->getConfig('projet_name') : '');

                    try {
                        $this->sendMail([
                            'to' => $data['email'],
                            'subject' => 'Hello' . $userFullName . '! We received Password reset request. ' . $projectName,
                            'body' => $this->getEmailBody('emails\forgot_password.twig', [
                                'password_token' => $ue->getPasswordToken()
                            ]),
                            'from_name' => 'No reply'
                        ]);

                        $this->container->flash->addMessage(
                            'alert-success',
                            '<h3>We\'ve sent you an email with a confirmation link.</h3>' .
                            '<p>Use it to restore your password.</p>'
                        );

                        return $response->withRedirect($this->router->pathFor('forgot-password'));
                    } catch (\Exception $e) {
                        Logger::log($e->getMessage());
                    }
                }
            }
        }

        return $this->view->render($response, 'auth\forgot-password\index.twig', [
            'form' => $form
        ]);
    }
}
