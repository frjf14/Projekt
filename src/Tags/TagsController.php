<?php

namespace Anax\Tags;

/**
 * A controller for comments and admin related events.
 *
 */
class TagsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize()
    {
        $this->tags = new \Anax\Tags\Tag();
        $this->tags->setDI($this->di);
    }

    /**
     * List all tags.
     *
     * @return void
     */
    public function viewAction()
    {
        $sql = 'select tag, count(*) as num from project_tag group by tag;';

        $all = $this->tags->sql($sql);
        $this->views->add('tag/list-all', [
            'tags' => $all,
        ]);
    }

        /**
     * List all tags.
     *
     * @return void
     */
    public function viewfrontAction()
    {
        $sql = 'select tag, count(*) as num from project_tag group by tag order by num desc limit 10;';

        $all = $this->tags->sql($sql);
        $this->views->add('project/tags-front', [
            'tags' => $all,
        ]);
    }

    /**
     * Delete tag.
     *
     * @param $tag to delete.
     *
     * @return void
     */
    public function deleteAction($tag = null)
    {
        if (!isset($tag)) {
            die("Missing tag");
        }

        $res = $this->tags->delete($tag);

        $url = $this->url->create();
        $this->response->redirect($url);
    }

    // /**
    //  * List all questions for the page the questions was made.
    //  *
    //  * @return void
    //  */
    // public function taggedAction($tag = null)
    // {

    //     $all = $this->questions->query()
    //         ->where("tag = ?")
    //         ->execute($tag);

    //     $this->views->add('question/list-all', [
    //         'questions' => $all,
    //     ]);
    // }

    /**
     * Handles the tag string from new questions.
     *
     * @param $string tags to insert.
     *
     * @return $tags as array
     */
    public function handleTagsAction($tagstring = null, $id = null)
    {
        //makes the string lowercase, removes spaces and splits the string into multiple tags as array ","
        $tagstring = strtolower($tagstring);

        $tagstring = preg_replace('/\s+/', '', $tagstring);

        $tags = explode(",", $tagstring);

        foreach($tags as $tag) {
            $tag = urldecode($tag);
            $sql = "insert into project_tag (tag, question_id) values ('$tag', '$id');";

            $this->db->execute($sql);
        }

        return $tags;
    }

    /**
     * Gets tags by question id.
     *
     * @param $id question id.
     *
     * @return tags.
     */
    public function getTagsByQuestionIdAction($id = null) {

        $tags = $this->tags->query('tag')
            ->where("question_id = ?")
            ->execute([$id]);

        return $tags;
    }
}

