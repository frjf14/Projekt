<?php

namespace Anax\Questions;

/**
 * A controller for questions.
 *
 */
class QuestionsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->questions = new \Anax\Questions\Question();
        $this->questions->setDI($this->di);

        $this->users = new \Anax\Users\User();
        $this->users->setDI($this->di);

        $this->answers = new \Anax\Answers\Answer();
        $this->answers->setDI($this->di);

        $this->comments = new \Anax\Comments\Comment();
        $this->comments->setDI($this->di);

        $this->tags = new \Anax\Tags\Tag();
        $this->tags->setDI($this->di);
    }

    /**
     * List all questions.
     *
     * @return void
     */
    public function viewAction()
    {
        $all = $this->questions->findAll();
        $this->views->add('question/list-all', [
            'questions' => $all,
        ]);
    }

    /**
     * List all questions non deleted questions.
     *
     * @return void
     */
    public function publicviewAction()
    {
        $all = $this->questions->query()
            ->where('deleted is null')
            ->execute();

        $this->views->add('question/list-all', [
            'questions' => $all,
        ]);
    }


    /**
     * Add new question.
     *
     *
     * @return void
     */
    public function askAction()
    {
        $this->theme->setTitle("Ställ en fråga");

        $user = $this->session->get('user');

        if(isset($user)){

            $form = $this->form->create([], [

                'title' => [
                    'type'        => 'text',
                    'label'       => 'Titel',
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],

                'question' => [
                    'type'        => 'textarea',
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],

                'tags' => [
                    'type'        => 'text',
                    'label'       => 'Tagg(ar) använd " , " för att separera flera taggar',
                    'required'    => true,
                    'multiple'    => true,
                    'validation'  => ['not_empty'],
                ],

                'submit' => [
                    'type'      => 'submit',
                    'value'     => 'Ställ frågan',
                    'callback'  => function($form) {

                        $form->saveInSession = true;
                        return true;
                    }
                ],
            ]);

            $status = $form->check();

            if($status === true) {

                $form_data = $this->session->get('form-save');

                $stringtags  = $form_data['tags']['value'];

                $now = date("Y-m-d H:i:s");

                $userId = $this->session->get('user_id');

                $user = $this->session->get('user');

                $this->questions->save([
                    'title' => $form_data['title']['value'],
                    'question' => $this->textFilter->doFilter($form_data['question']['value'], 'shortcode, markdown'),
                    'user_id' => $userId,
                    'created' => $now,
                ]);

                $id = $this->questions->getLastInsertID();

                $tags = $this->di->dispatcher->forward([
                    'controller' => 'tags',
                    'action' => 'handleTags',
                    'params' => [$stringtags, $id]
                ]);

                $this->updateUserAsked($userId);

                unset($_SESSION['form-save']);

                $this->flashmessage->addNotice('Din fråga är nu ställd.');

                $url = $url = $this->url->create('questions');

                $this->response->redirect($url);

            } else if($status === false) {

                $form->AddOutput("<p>Någonting gick fruktansvärt fel. :(</p>");
            }

            $questionInfo = $this->fileContent->get('questionInfo.md');
            $questionInfo = $this->textFilter->doFilter($questionInfo, 'shortcode, markdown');

            $this->views->add('question/add-question', [
                'form' => $form->getHTML()
            ])

            ->addString($questionInfo, 'sidebar');

        } else {

            $this->flashmessage->addNotice('Du måste vara inloggad för att ställa frågor.');

            $url = $url = $this->url->create('users/login');

            $this->response->redirect($url);
        }
    }

    /**
     * Delete question.
     *
     * @param integer $id of question to delete and the question page for the redirect.
     *
     * @return void
     */
    public function deleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $this->deleteTag($id);

        $res = $this->questions->delete($id);

        if($res) {

            $this->flashmessage->addNotice('Borttagning lyckades!');

            $url = $this->session->get('url');

            $url = $url = $this->url->create($url);

            $this->response->redirect($url);
        }
    }

    /**
     * Delete (soft) question.
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

        $now = date("Y-m-d H:i:s");

        $question = $this->questions->find($id);

        $question->deleted = $now;
        $question->save();

        $url = $this->url->create('questions');
        $this->response->redirect($url);
    }

    /**
     * List all questions for the page the questions was made.
     *
     * @return void
     */
    public function taggedAction($tag = null)
    {
        $this->theme->setTitle("Taggad frågor");

        $tag = urldecode($tag);

        $questions = $this->questions->query()
            ->join('tag', 'project_question.id = project_tag.question_id')
            ->where("project_tag.tag = ?")
            ->execute([$tag]);

        $this->views->add('question/list-all', [
            'questions' => $questions,
        ]);
    }

    /**
     * List question.
     *
     * @return void
     */
    public function idAction($id = null)
    {
        $this->theme->setTitle("Frågan");

        $question = $this->questions->query()
            ->where("id = ?")
            ->andWhere('deleted is null')
            ->execute([$id]);

        $comments = $this->comments->query()
            ->where('question_id = ?')
            ->andWhere('deleted is null')
            ->execute([$id]);

        $answers = $this->answers->query()
            ->where('question_id = ?')
            ->andWhere('deleted is null')
            ->execute([$id]);

        $answerForm = $this->answer($id);

        $this->views->add('project/question-view', [
            'questions' => $question,
            'comments' => $comments,
            'answers' => $answers,
            'answerform' => $answerForm,
        ]);
    }

    /**
     * Update question.
     *
     * @param int $id of question to edit.
     *
     * @return void
     */
    public function updateAction($id = null)
    {
        $this->theme->setTitle("Redigera frågan");

        $question = $this->questions->find($id);
        //Check that user is the op of the question.
        $user_id = $this->session->get('user_id');
        $question_user_id = $question->getProperties()['user_id'];

        if($question_user_id == $user_id){

            $properties = $question->getProperties();

            $tags = $this->tags->query('tag')
                        ->where('question_id = ?')
                        ->execute([$id]);

            $tagstring = "";

            foreach($tags as $tag){
                if($tagstring != "") {
                    $tagstring .= ", ";
                }

                $tagstring .= $tag->getProperties()['tag'];
            }

            $form = $this->form->create([], [

                'title' => [
                    'type'        => 'text',
                    'value'       => $properties['title'],
                    'label'       => 'Titel',
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],

                'question' => [
                    'type'        => 'textarea',
                    'value'       => strip_tags($properties['question']),
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],

                'tags' => [
                    'type'        => 'textarea',
                    'label'       => 'Tagg(ar)',
                    'value'       => $tagstring,
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],

                'submit' => [
                    'type'      => 'submit',
                    'value'     => 'Uppdatera frågan',
                    'callback'  => function($form) {

                        $form->saveInSession = true;
                        return true;
                    }
                ],
            ]);

            $status = $form->check();

            if($status === true) {

                foreach($tags as $tag){
                    if($tagstring != "") {
                        $tagstring .= ", ";
                    }

                    $tag= $tag->getProperties()['tag'];

                    $sql = "delete from project_tag where tag = '$tag' and question_id = '$id'";

                    $this->questions->sql($sql);
                }

                $form_data = $this->session->get('form-save');

                $stringtags  = $form_data['tags']['value'];

                $now = date("Y-m-d H:i:s");

                $userId = $this->session->get('user_id');

                $user = $this->session->get('user');

                $this->questions->update([
                    'id' => $id,
                    'title' => $form_data['title']['value'],
                    'question' => $this->textFilter->doFilter($form_data['question']['value'], 'shortcode, markdown'),
                    'user_id' => $userId,
                    'updated' => $now,
                ]);

                unset($_SESSION['form-save']);

                $tags = $this->di->dispatcher->forward([
                    'controller' => 'tags',
                    'action' => 'handleTags',
                    'params' => [$stringtags, $id]
                ]);

                $url = $this->url->create("questions/id/$id");

                $this->response->redirect($url);


            } else if($status === false) {

                $form->AddOutput("<p>Någonting gick fruktansvärt fel. :(</p>");
            }

            $this->views->add('question/add-question', [
                'title' => "Redigera frågan " . $properties['title'],
                'form' => $form->getHTML()
            ]);

        } else {

            $this->flashmessage->addNotice('Du kan inte uppdatera den här frågan.');

            $url = $url = $this->url->create("questions/id/$id");

            $this->response->redirect($url);
        }

    }

    /**
     * Gets questions for specific user by id.
     *
     * @param int $id of user.
     *
     * @return array questions.
     */
    public function getquestionsbyuserAction($id = null) {

        $questions = $this->questions->query()
            ->where('user_id = ?')
            ->execute([$id]);
        return $questions;
    }

    /**
     * Gets the ten latest questions order by the time of creation.
     *
     * @return void
     */
    public function tenlatestAction() {

        $sql = 'select * from project_question where deleted is null order by created desc limit 10;';
        $questions = $this->questions->sql($sql);
        $this->views->add('question/list-all', [
            'questions' => $questions,
        ]);
    }

    /**
     * Answer question.
     *
     * @param int $id of question to answer.
     *
     * @return form html
     */
    private function answer($question_id = null)
    {

        $form = $this->form->create([], [

            'answer' => [
                'type'        => 'textarea',
                'label'       => 'Svar',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],

            'submit' => [
                'type'      => 'submit',
                'value'     => 'Posta ett svar',
                'callback'  => function($form) {

                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        $status = $form->check();

        if($status === true) {

            $user = $this->session->get('user');

            if(isset($user)){

                $form_data = $this->session->get('form-save');

                $now = date("Y-m-d H:i:s");

                $userId = $this->session->get('user_id');

                $user = $this->session->get('user');

                $this->answers->save([
                    'answer' => $this->textFilter->doFilter($form_data['answer']['value'], 'shortcode, markdown'),
                    'user_id' => $userId,
                    'question_id' => $question_id,
                    'created' => $now,
                ]);

                $this->updateQuestionAnswerUp($question_id);

                unset($_SESSION['form-save']);

                $url = $this->session->get('url');

                $url = $url = $this->url->create($url);

                $this->response->redirect($url);

        } else {

            unset($_SESSION['form-save']);

            $this->flashmessage->addNotice('Du måste vara inloggad för att svara på frågan.');

            $url = $url = $this->url->create("questions/id/$question_id");

            $this->response->redirect($url);
        }

        } else if($status === false) {

            $form->AddOutput("<p>Någonting gick fruktansvärt fel. :(</p>");
        }

        return $form->getHTML();
    }


    /**
     * Delete tag by question id.
     *
     * @param int $id of question in tag.
     *
     * @return void
     */
    private function deleteTag($id)
    {
        $sql = "delete from project_tag where question_id = $id;";

        $this->db->execute($sql);
    }

    /**
     * Updates user profile number of questions asked.
     *
     * @param int $id of the user to update.
     *
     * @return void
     */
    private function updateUserAsked($id = null)
    {
        $sql = "update project_user set questions = questions +1 where id = $id;";

        $this->db->execute($sql);
    }

    /**
     * Updates number of answers of a question by increasing the number by 1.
     *
     * @param int $id of the question to update.
     *
     * @return void
     */
    private function updateQuestionAnswerUp($id = null)
    {
        $sql = "update project_question set answers = answers +1 where id = $id;";

        $this->db->execute($sql);
    }
}

