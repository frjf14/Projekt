<?php

namespace Anax\Users;

/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->users = new \Anax\Users\User();
        $this->users->setDI($this->di);

        $this->comments = new \Anax\Comments\Comment();
        $this->comments->setDI($this->di);

        $this->answers = new \Anax\Answers\Answer();
        $this->answers->setDI($this->di);

        $this->questions = new \Anax\Questions\Question();
        $this->questions->setDI($this->di);
    }

    /**
     * List all users.
     *
     * @return void
     */
    public function listAction()
    {

        $all = $this->users->findAll();

        $this->theme->setTitle("Alla användare");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Alla användare",
        ]);
    }

    /**
     * List all non deleted users.
     *
     * @return void
     */
    public function publiclistAction()
    {

        $all = $this->users->query()
            ->where('deleted is null')
            ->execute();

        $this->theme->setTitle("Alla användare");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Alla användare",
        ]);
    }

    /**
     * List user with id.
     *
     * @param int $id of user to display
     *
     * @return void
     */
    public function idAction($id = null)
    {

        $user = $this->users->find($id);

        $this->theme->setTitle("Användare med id");
        $this->views->add('users/view', [
            'user' => $user,
            'questions' => null,
            'comments' => null,
            'answers' => null,
        ]);


    }

    /**
     * List user with acronym.
     *
     * @param string $acronym of user to display
     *
     * @return void
     */
    public function usernameAction($acronym = null)
    {

        $user = $this->users->findByAcronym($acronym);

        $questions = $this->users->query()
            ->join('question', 'project_question.user_id = project_user.id')
            ->where('acronym = ?')
            ->execute([$acronym]);

        $comments = $this->users->query()
            ->join('comment', 'project_user.id = project_comment.user_id')
            ->where('acronym = ?')
            ->execute([$acronym]);

        $answers = $this->users->query()
            ->join('answer', 'project_user.id = project_answer.user_id')
            ->where('acronym = ?')
            ->execute([$acronym]);

        $this->theme->setTitle($acronym);
        $this->views->add('users/view', [
            'user' => $user,
            'questions' => $questions,
            'comments' => $comments,
            'answers' => $answers,
        ]);
    }

    /**
     * Register new user.
     *
     * @param string $acronym of user to add.
     *
     * @return void
     */
    public function registerAction($acronym = null)
    {
        $this->theme->setTitle("Registrera");

        $form = $this->form->create([], [

            'acronym' => [
                    'type'        => 'text',
                    'label'       => 'Användarnamn',
                    'required'    => true,
                    'validation'  => ['not_empty'],
            ],

            'email' => [
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['email_adress'],
            ],

            'name' => [
                'type'        => 'text',
                'label'       => 'Namn',
                'required'    => false,
                'validation'  => [],
             ],

            'password' => [
                'type'        => 'password',
                'label'       => 'Lösenord',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],

            'submit' => [
                'type'      => 'submit',
                'value'     => 'Registrera',
                'callback'  => function($form) {

                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        $status = $form->check();

        if($status === true) {

            $form_data = $this->session->get('form-save');

            $acronym  = $form_data['acronym']['value'];

            $checkAcronym = $this->checkAcronym($acronym);

            if($checkAcronym) {

            $now = date("Y-m-d H:i:s");

            $this->users->save([
                    'acronym' => $form->Value('acronym'),
                    'email' => $form->Value('email'),
                    'name' => $form->Value('name'),
                    'password' => password_hash($form->Value('password'), PASSWORD_DEFAULT),
                    'created' => $now,
            ]);

            unset($_SESSION['form-save']);

            $url = $this->url->create('users/username/' . $form->Value('acronym'));

            $this->response->redirect($url);

            } else {

                $this->flashmessage->addError('Användarnamnet är redan registrerat, välj ett annat användarnamn.');

                $url = $this->url->create('users/register');

                $this->response->redirect($url);
            }

        } else if($status === false) {

            $form->AddOutput("<p>Någonting gick fruktansvärt fel. :(</p>");
        }

        $this->views->add('users/add-user', [
            'title' => "Registrera dig",
            'form' => $form->getHTML()
        ]);

    }

    /**
     * Check if acronym exists.
     *
     * @param string $acronym to check.
     *
     * @return boolean true if name doesn't exist or false if it exists.
     */
    private function checkAcronym($acronym)
    {
        $acronym = $this->users->query('acronym')
            ->where('acronym = ?')
            ->execute([$acronym]);

        if($acronym != null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Delete user.
     *
     * @param integer $id of user to delete.
     *
     * @return void
     */
    public function deleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $this->users->delete($id);

        $url = $this->url->create('users');
        $this->response->redirect($url);
    }

    /**
     * Delete (soft) user.
     *
     * @param integer $id of user to delete.
     *
     * @return void
     */
    public function softDeleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        //$now = date(DATE_RFC2822);
        $now = date("Y-m-d H:i:s");

        $user = $this->users->find($id);

        $user->deleted = $now;
        $user->save();

        $url = $url = $this->url->create("users/id/$id");

        $this->response->redirect($url);

    }
    /**
     * Recover deleted user.
     *
     * @param $integer id of user to recover.
     *
     * @return void
     */
    public function recoverDeleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $user = $this->users->find($id);

        $user->deleted = null;
        $user->save();

        $url = $this->url->create("users/id/$id");
        $this->response->redirect($url);
    }

    /**
     * List all active and not deleted users.
     *
     * @return void
     */
    public function activeAction()
    {
        $all = $this->users->query()
            ->where('active IS NOT NULL')
            ->andWhere('deleted is NULL')
            ->execute();

        $this->theme->setTitle("Users that are active");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Användare som är aktiva",
        ]);
    }

    /**
     * List all inactive users.
     *
     * @return void
     */
    public function inactiveAction()
    {
        $all = $this->users->query()
            ->where('active IS NULL')
            ->execute();

        $this->theme->setTitle("Users that are active");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Användare som är inaktiva",
        ]);
    }

    /**
     * List all Deleted users.
     *
     * @return void
     */
    public function deletedAction()
    {
        $all = $this->users->query()
            ->where('deleted IS NOT NULL')
            ->execute();

        $this->theme->setTitle("Borttagna användare");
        $this->views->add('users/deleted', [
            'users' => $all,
            'title' => "Användare som är Raderade",
        ]);
    }

    /**
     * Update user.
     *
     * @param string $acronym of user to add.
     *
     * @return void
     */
    public function updateAction($id = null)
    {
        $user_id = $this->session->get('user_id');

        if($user_id == $id) {

        $this->theme->setTitle("Uppdatera användare");

        $user = $this->users->find($id);

        $properties = $user->getProperties();

        $form = $this->form->create([], [

            'email' => [
                'type'        => 'text',
                'value'       => $properties['email'],
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
            ],

            'name' => [
                'type'        => 'text',
                'value'       => $properties['name'],
                'label'       => 'Namn',
                'required'    => true,
                'validation'  => ['not_empty'],
             ],

            'submit' => [
                'type'      => 'submit',
                'value'     => 'Uppdatera',
                'callback'  => function($form) {

                    //$now = date(DATE_RFC2822);
                    $now = date("Y-m-d H:i:s");

                    $this->users->update([
                        'email' => $form->Value('email'),
                        'name' => $form->Value('name'),
                        'updated' => $now,
                    ]);

                    return true;
                }
            ],
        ]);

        $status = $form->check();

        if($status === true) {

            $url = $this->url->create('users/id/' . $this->users->id);

            $this->response->redirect($url);

        } else if($status === false) {

            $form->AddOutput("<p>Någonting gick fruktansvärt fel. :(</p>");
        }

        $this->views->add('users/add-user', [
            'title' => "Uppdatera användare " . $properties['acronym'],
            'form' => $form->getHTML()
        ]);

        } else {

            $this->flashmessage->addError('Du kan inte redigera andras användare!.');

            $url = $this->url->create('users');

            $this->response->redirect($url);

        }
    }

    /**
     * Activate user.
     *
     * @param integer $id of user to activate.
     *
     * @return void
     */
    public function activateAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        //$now = date(DATE_RFC2822);
        $now = date("Y-m-d H:i:s");

        $user = $this->users->find($id);

        $user->active = $now;
        $user->save();

        $url = $this->url->create('users/id/' . $id);
        $this->response->redirect($url);
    }

    /**
     * Inactivate user.
     *
     * @param integer $id of user to inactivate.
     *
     * @return void
     */
    public function inactivateAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $user = $this->users->find($id);

        $user->active = null;
        $user->save();

        $url = $this->url->create('users/id/' . $id);
        $this->response->redirect($url);
    }

    /**
     * Login user.
     *
     *
     * @return void
     */
    public function loginAction()
    {
        //Checks if user already logged in.
        if($this->session->get('user')) {
            //flash message is created and site i redirected to front page.
            $this->flashmessage->addNotice('Du är redan inloggad.');
            $url = $this->url->create();
            $this->response->redirect($url);


        } else {

            $this->theme->setTitle("Logga in");

            $form = $this->form->create([], [

                'acronym' => [
                    'type'        => 'text',
                    'label'       => 'Användarnamn',
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],

                'password' => [
                    'type'        => 'password',
                    'label'       => 'Lösenord',
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],

                'submit' => [
                    'type'      => 'submit',
                    'value'     => 'Logga in',
                    'callback'  => function($form) {

                        $form->saveInSession = true;
                        return true;
                    }
                ],
            ]);

            $status = $form->check();

            if($status === true) {

                $form_data = $this->session->get('form-save');

                $acronym  = $form_data['acronym']['value'];
                $password = $form_data['password']['value'];

                $user = $this->users->findByAcronym($acronym);

                unset($_SESSION['form-save']);

                if($user) {

                    if(password_verify($password, $user->password)) {

                        $this->session->set('user', $user->acronym);
                        $this->session->set('user_id', $user->id);

                        $now = date("Y-m-d H:i:s");

                        $this->users->update([
                            'id' => $user->id,
                            'active' => $now,
                        ]);


                        $this->flashmessage->addNotice('Du är nu inloggad.');

                        $url = $this->url->create();

                        $this->response->redirect($url);

                    } else {

                        $this->flashmessage->addError('Fel lösenord eller användarnamn.');

                        $url = $this->url->create('users/login');

                        $this->response->redirect($url);
                    }
                } else {

                    $this->flashmessage->addError('Fel lösenord eller användarnamn.');

                    $url = $this->url->create('users/login');

                    $this->response->redirect($url);
                }

            } else if($status === false) {

                $form->AddOutput("<p>Någonting gick fruktansvärt fel. :(</p>");
            }

            $loginInfo = $this->fileContent->get('loginInfo.md');
            $loginInfo = $this->textFilter->doFilter($loginInfo, 'shortcode, markdown');

            $this->views->addString($form->getHTML(), 'main')
                        ->addString($loginInfo, 'sidebar');
        }
    }

    /**
     * Logs out the user.
     *
     * @return void
     */
    public function logoutAction()
    {
        $this->session->set('user', null);
        $this->session->set('user_id', null);

        $this->flashmessage->addNotice('Du har loggat ut.');

        $url = $this->url->create();

        $this->response->redirect($url);
    }

    /**
     * Selects the 10 latest logged in users.
     *
     * @return void
     */
    public function tenlatestAction() {

        $sql = 'select * from project_user where deleted is null order by active desc limit 10;';
        $users = $this->users->sql($sql);
        $this->views->add('project/users-front', [
            'users' => $users,
        ]);
    }

    /**
     * gets the acronym of a user by id.
     *
     * @param int $id of hte user.
     *
     * @return string $acronym
     */
    public function getAcronymAction($id) {

        $acronym = $this->users->query('acronym')
            ->where('id = ?')
            ->execute([$id]);

        $acronymobj = $acronym[0];

        $acronymarray = $acronymobj->getProperties();

        $acronym = $acronymarray['acronym'];

        return $acronym;
    }
}

