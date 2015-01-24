<?php

namespace Phpmvc\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;



    /**
     * View all comments.
     *
     * @return void
     */
    public function viewAction($page)
    {
        $comments = new \Phpmvc\Comment\CommentsInSession();
        $comments->setDI($this->di);

        $all = $comments->findAll($page);

        $this->views->add('comment/comments', [
            'comments' => $all,
        ]);
    }



    /**
     * Add a comment.
     *
     * @return void
     */
    public function addAction()
    {
        $isPosted = $this->request->getPost('doCreate');

        if (!$isPosted) {
            $this->response->redirect($this->request->getPost('redirect'));
        }

        $comment = [
            'page'      => $this->request->getPost('page'),
            'content'   => $this->request->getPost('content'),
            'name'      => $this->request->getPost('name'),
            'web'       => $this->request->getPost('web'),
            'mail'      => $this->request->getPost('mail'),
            'timestamp' => time(),
            'ip'        => $this->request->getServer('REMOTE_ADDR'),
        ];

        $comments = new \Phpmvc\Comment\CommentsInSession();
        $comments->setDI($this->di);

        $comments->add($comment);

        $this->response->redirect($this->request->getPost('redirect'));
    }



    /**
     * Remove all comments.
     *
     * @return void
     */
    public function removeAllAction()
    {
        $isPosted = $this->request->getPost('doRemoveAll');

        if (!$isPosted) {
            $this->response->redirect($this->request->getPost('redirect'));
        }

        $comments = new \Phpmvc\Comment\CommentsInSession();
        $comments->setDI($this->di);

        $comments->deleteAll();

        $this->response->redirect($this->request->getPost('redirect'));
    }

    public function removeAction($id)
    {
        // $isPosted = $this->request->getPost('doRemove'); //doRemove måste lägga till i formulär i tpl.

        // if (!$isPosted) {
        //     $this->response->redirect($this->request->getPost('redirect'));
        // }

        $comments = new \Phpmvc\Comment\CommentsInSession();
        $comments->setDI($this->di);

        $comments->delete($id);

        $this->response->redirect($this->request->getPost('redirect'));
    }

    public function editFormAction($id)
    {
        $comments = new \Phpmvc\Comment\CommentsInSession();
        $comments->setDI($this->di);

        $all = $comments->findAll();

        $i = 0;

        foreach($all as $comment){

            if($comment['id'] == $id){
                break;
            }
            $i++;
        }

        $this->views->add('comment/editComment', [
            'comment' => $all[$i],
        ]);
    }

    public function editAction($id)
    {
        $isPosted = $this->request->getPost('doEdit');

        if (!$isPosted) {
            $this->response->redirect($this->request->getPost('redirect'));
        }

        $comment = [
            'page'      => $this->request->getPost('page'),
            'content'   => $this->request->getPost('content'),
            'name'      => $this->request->getPost('name'),
            'web'       => $this->request->getPost('web'),
            'mail'      => $this->request->getPost('mail'),
            'timestamp' => $this->request->getPost('timestamp'),
            'ip'        => $this->request->getServer('REMOTE_ADDR'),
            'id'        => $id,
            'edited'    => time(),
        ];

        $comments = new \Phpmvc\Comment\CommentsInSession();
        $comments->setDI($this->di);

        $comments->edit($comment, $id);

        $this->response->redirect($this->request->getPost('redirect'));
    }
}
