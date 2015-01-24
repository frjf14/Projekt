<?php

namespace Anax\Answers;

/**
 * A controller for answers.
 *
 */
class AnswersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->answers = new \Anax\Answers\Answer();
        $this->answers->setDI($this->di);
    }

    /**
     * Delete answer.
     *
     * @param integer $id of answer to delete.
     *
     * @return void
     */
    public function deleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $answer = $this->answers->find($id);

        $answer_user_id = $answer->getProperties()['user_id'];
        $userId = $this->session->get('user_id');
        //checks that the user editing the answer is the op.
        if($userId == $answer_user_id) {

            //questions id needed to update the questions number of answers given.
            $question_id = $this->answers->query('question_id')
                ->where('id = ?')
                ->execute([$id]);

            $qid = $question_id[0]->getProperties();

            $qid = $qid['question_id'];

            $this->updateQuestionAnswerDown($qid);

            $this->answers->delete($id);

            $this->flashmessage->addNotice('Borttagning lyckades!');

            $url = $this->session->get('url');

            $url = $url = $this->url->create($url);

            $this->response->redirect($url);

        } else {

            $this->flashmessage->addError('De där får du inte göra!');

            $url = $tihs->session->get('url');

            $url = $this->url->create($url);

            $this->response->redirect($url);

        }
    }

    /**
     * Update answer.
     *
     * @param int $id of answer to edit.
     *
     * @return void
     */
    public function updateAction($id = null)
    {
        $this->theme->setTitle("Redigera svaret");
        //gets the answer used as values in the form.
        $answer = $this->answers->find($id);

        $answer_user_id = $answer->getProperties()['user_id'];
        $userId = $this->session->get('user_id');
        //checks that the user editing the answer is the op.
        if($userId == $answer_user_id) {

            $properties = $answer->getProperties();

            $form = $this->form->create([], [

                'answer' => [
                    'type'        => 'textarea',
                    'label'       => 'Svaret',
                    'value'       => strip_tags($properties['answer']), //strip_tags because the answers are stored as markdown.
                    'required'    => true,
                    'validation'  => ['not_empty'],
                ],

                'submit' => [
                    'type'      => 'submit',
                    'value'     => 'Redigera frågan',
                    'callback'  => function($form) {

                        $form->saveInSession = true;
                        return true;
                    }
                ],
            ]);

            $status = $form->check();

            if($status === true) {

                $form_data = $this->session->get('form-save');

                $now = date("Y-m-d H:i:s");

                $userId = $this->session->get('user_id');

                $user = $this->session->get('user');

                $this->answers->update([
                    'answer' => $this->textFilter->doFilter($form_data['answer']['value'], 'shortcode, markdown'), //saves the answer as markdown.
                    'updated' => $now,
                 ]);

                    $url = $this->session->get('url');

                    $url = $this->url->create($url);

                    $this->response->redirect($url);

            } else if($status === false) {

                $form->AddOutput("<p>Någonting gick fruktansvärt fel. :(</p>");
            }

            $this->views->add('question/add-question', [
                'form' => $form->getHTML()
            ]);

        } else {

            $this->flashmessage->addError('De där får du inte göra!');

            $url = $this->url->create('questions');

            $this->response->redirect($url);

        }
    }

    /**
     * Update questions number of answers.
     *
     * @param int $id of questions to update.
     *
     * @return void
     */
    private function updateQuestionAnswerDown($id = null)
    {
        $sql = "update project_question set answers = answers-1 where id = $id;";

        $this->db->execute($sql);
    }
}

