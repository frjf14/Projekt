<?php

namespace Anax\Comments;

/**
 * A controller for comments and admin related events.
 *
 */
class CommentsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->comments = new \Anax\Comments\Comment();
        $this->comments->setDI($this->di);
    }

    /**
     * List all comments.
     *
     * @return void
     */
    public function viewAction()
    {
        $all = $this->comments->findAll();
        $this->views->add('comments/list-all', [
            'comments' => $all,
        ]);
    }

    /**
     * Add comment.
     *
     * @return void
     */
    public function addAction($type, $id)
    {
        $user = $this->session->get('user');

        if(isset($user)){

            $this->theme->setTitle("Posta kommentar");

            $form = $this->form->create([], [

                'comment' => [
                    'type'        => 'textarea',
                    'label'       => 'Kommentar',
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],

                'submit' => [
                    'type'      => 'submit',
                    'value'     => 'Posta kommentaren',
                    'callback'  => function($form) {

                        $form->saveInSession = true;
                        return true;
                    }
                ],
            ]);

            $status = $form->check();

            if($status === true) {

                $now = date("Y-m-d H:i:s");

                $user = $this->session->get('user_id');

                if($type == 'question') {

                    $this->comments->save([
                        'comment' => $this->textFilter->doFilter($form->Value('comment'), 'shortcode, markdown'), //saved as markdown
                        'user_id' => $user,
                        'created' => $now,
                        'question_id'   => $id,
                    ]);

                 } elseif($type == 'answer') {

                    $this->comments->save([
                        'comment' => $this->textFilter->doFilter($form->Value('comment'), 'shortcode, markdown'), //saved as markdown
                        'user_id' => $user,
                        'created' => $now,
                        'answer_id'   => $id,
                    ]);
                 }

                unset($_SESSION['form-save']);

                $question_id = $this->session->get('question_id');

                $url = $this->url->create("questions/id/$question_id");

                $this->response->redirect($url);

            } else if($status === false) {

                $form->AddOutput("<p>Någonting gick fruktansvärt fel. :(</p>");
            }

            $this->views->add('project/add-comment', [
                'form' => $form->getHTML()
            ]);

        } else {

            $this->flashmessage->addNotice('Du måste vara inloggad för att kommentera.');

            $url = $url = $this->url->create('users/login');

            $this->response->redirect($url);
        }
    }

    /**
     * Delete comment.
     *
     * @param integer $id of comment to delete and the comment page for the redirect.
     *
     * @return void
     */
    public function deleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $user_id = $this->session->get('user_id');
        $comment = $this->comments->find($id);
        $comment_user_id = $comment->getProperties()['user_id'];

        if($user_id == $comment_user_id){

            $res = $this->comments->delete($id);

            $question_id = $this->session->get('question_id');
            //redirects back to the question where the comment was deleted.
            $url = $this->session->get('url');

            $url = $this->url->create($url);

            $this->response->redirect($url);
        }
    }

    /**
     * List all comments for the post with id.
     *
     * @param int $id of post to comment.
     *
     * @return comments.
     */
    public function postcommentsAction($id)
    {

        $all = $this->comments->query()
            ->where("answer_id = ?")
            ->execute([$id]);

        return $all;
    }

    /**
     * Update comment.
     *
     * @param int $id of comment to edit.
     *
     * @return void
     */
    public function updateAction($id)
    {
        $this->theme->setTitle("Redigera kommentaren");

        $comment = $this->comments->find($id);

        $properties = $comment->getProperties();

        $form = $this->form->create([], [

            'comment' => [
                'type'        => 'textarea',
                'label'       => 'Kommentar',
                'value'       => strip_tags($properties['comment']), //removes tags because its saved as markdown.
                'required'    => true,
                'validation'  => ['not_empty'],
            ],

            'submit' => [
                'type'      => 'submit',
                'value'     => 'Redigera kommentaren',
                'callback'  => function($form) {

                    //$now = date(DATE_RFC2822);
                    $now = date("Y-m-d H:i:s");

                    $this->comments->update([
                        'comment' => $this->textFilter->doFilter($form->Value('comment'), 'shortcode, markdown'), //saved as markdown.
                        'updated' => $now,
                    ]);

                    return true;
                }
            ],
        ]);

        $status = $form->check();

        if($status === true) {

            $url = $this->session->get('url');

            $url = $this->url->create($url);

            $this->response->redirect($url);

        } else if($status === false) {

            $form->AddOutput("<p>Någonting gick fruktansvärt fel. :(</p>");
        }

        $this->views->add('comments/add-comment', [

            'form' => $form->getHTML()
        ]);

    }
}

