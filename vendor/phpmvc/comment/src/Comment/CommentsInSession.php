<?php

namespace Phpmvc\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentsInSession implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
     * Add a new comment.
     *
     * @param array $comment with all details.
     *
     * @return void
     */
    public function add($comment)
    {
        $comments = $this->session->get('comments', []);
        $id = uniqid();
        $comment['id'] = $id;
        $comments[] = $comment;
        $this->session->set('comments', $comments);
    }



    /**
     * Find and return all comments.
     *
     * @return array with filtered comments.
     */
    public function findAll($page = null)
    {
        $comments = $this->session->get('comments', []);
        $filteredList = array();

        if($page != null & $comments != null){

        foreach($comments as $comment) {

            if($comment['page'] == $page){

                $filteredList[] = $comment;
            }
        }
            return $filteredList;
        } else {
            return $comments;
        }
    }



    /**
     * Delete all comments.
     *
     * @return void
     */
    public function deleteAll()
    {
        $this->session->set('comments', []);
    }

    public function delete($id)
    {
        $comments = $this->session->get('comments', []);

        $i = 0;

        foreach($comments as $comment){

            if($comment['id'] == $id){
                break;
            }

            $i++;
        }

        unset($comments[$i]);
        $comments = array_values($comments);
        $this->session->set('comments', $comments);
    }

    public function edit($comment, $id)
    {
        $comments = $this->session->get('comments', []);

        $i = 0;

        foreach($comments as $com) {

            if($com['id'] == $id){

                break;
            }
            $i++;
        }

        $comments[$i] = $comment;

        $this->session->set('comments', $comments);
    }
}
