<?php

namespace App\MVC\Controllers\Index;

use App\Core\Constant;
use \App\Core\CoreController;
use App\Forms\EditUserForm;
use App\MVC\Entity\UserEntity;
use App\MVC\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdminController
 * @package App\MVC\Controllers\Index
 */
class AdminController extends CoreController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function index (Request $request, Response $response)
    {
        return $this->view->render($response, 'index\admin\index.twig', []);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function users (Request $request, Response $response)
    {
        $u = new User($this->container);
        $users = $u->getAll();

        $roles = $this->container->acl->getRoles();
        $roles = array_flip($roles);

        foreach ($users as &$user) {
            $user['role'] = isset($roles[$user['role']]) ? $roles[$user['role']] : '-';
        }

        return $this->view->render($response, 'index\admin\users.twig', [
            'users' => $users
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function usersEdit (Request $request, Response $response)
    {
        $id = $request->getAttribute('id', null);

        $u = new User($this->container);

        $form = $this->getForm('App\Forms\EditUserForm');

        $user = null;

        if ($id && is_numeric($id) && $id > 0) {
            $user = $u->getByField('id', $id);

            if (!empty($user['id'])) {
                $roles = $this->container->acl->getRoles();
                $roles = array_merge([''], $roles);
                $roles = array_flip($roles);
                unset($roles[Constant::ROLE_GUEST]);
                $roles[''] = '';

                $form->get('role')->setValueOptions($roles);

                $form->setData($user);

                if ($request->isPost()) {
                    $data = $request->getParams();

                    $form->setData($data);
                    $isValid = $form->isValid();

                    if ($isValid) {
                        $data = $form->getData();
                        $ue = new UserEntity();
                        $ue->exchangeArray([
                            'id' => $data['id'],
                            'email' => $data['email'],
                            'first_name' => $data['first_name'],
                            'last_name' => $data['last_name'],
                            'role' => $data['role'],
                            'updated' => date('Y-m-d H:i:s'),
                        ]);

                        $u->modify($ue, true);

                        $this->container->flash->addMessage(
                            'alert-success',
                            '<h3>User successfully changed!</h3>'
                        );

                        return $response->withJson(['success' => true]);
                    }
                }
            }else {
                $user = null;
            }
        }

        return $this->view->render($response, 'index\admin\users-edit.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
