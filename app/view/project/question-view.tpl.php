<div class='questions'>

    <div id="questions">

        <h2>Fråga</h2>

        <hr>

        <?php if(sizeof($questions) > 0){$question = $questions[0]; $properties = $question->getProperties(); $url="questions/id/{$properties['id']}"; $this->session->set('url', $url); $this->session->set('question_id', $properties['id']); ?>

            <div id="question">

                <div id="poster">

                    <?php $acronym=$this->di->dispatcher->forward(['controller' => 'users','action' => 'getAcronym','params' => [$properties['user_id']]]); ?>

                    <em>Postad av: <a class="linkButton" href='<?=$this->url->create("users/username/{$acronym}")?>'><?=$acronym?></a></em>
                    <em>Antal svar:  <?=$properties['answers']?></em>
                    <em>Ställde frågan: </br> <?=$properties['created']?> </em>

                    <?php if ($properties['updated'] != null) : ?>

                        <em>Redigerad: <br> <?=$properties['updated']?> </em>

                    <?php endif ?>

                    <?php if($properties['user_id'] == $this->session->get('user_id')) : ?>

                        <em>
                            <a class="linkButton" href='<?=$this->url->create("questions/update/{$properties["id"]}")?>'>Redigera</a>
                            <a class="linkButton" href='<?=$this->url->create("questions/delete/{$properties["id"]}")?>'>Ta bort</a>
                        </em>

                    <?php endif ?>

                </div>

                <div id="content">
                    <h3 class="answer-title"><a href='<?=$this->url->create("questions/id/{$properties['id']}")?>'><?=$properties['title']?></a></h3>

                    <?=$properties['question']?>

                    <div id="tags">
                        <?php $tags = $this->di->dispatcher->forward(['controller' => 'tags','action' => 'getTagsByQuestionId','params' => [$properties['id']]]);?>

                        <?php foreach ($tags as $tag) : ?>

                            <?php $prop = $tag->getProperties(); ?>

                            <a class="linkButton" href='<?=$this->url->create("questions/tagged/{$prop["tag"]}")?>'><?=$prop['tag']?></a>

                            <?php endforeach; ?>
                    </div>
                </div>
                 <a class="comment-add" href='<?=$this->url->create("comments/add/question/{$properties["id"]}")?>'>Kommentera</a>
            </div>
            <hr>

            <?php if($comments) : ?>

                <h4> Kommentarer till frågan </h4>

                <hr>

                <?php foreach ($comments as $comment) : ?>

                    <?php $properties = $comment->getProperties(); ?>

                    <div id="comment">

                        <?=$properties['comment']?> <?php $acronym = $this->di->dispatcher->forward(['controller' => 'users','action' => 'getAcronym','params' => [$properties['user_id']]]);?> <em><a class="linkButton" href='<?=$this->url->create("users/username/{$acronym}")?>'><?=$acronym?></a></em></em><em> - <?=$properties['created']?> </em>

                        <?php if($properties['user_id'] == $this->session->get('user_id')) : ?><em><a class="linkButton" href='<?=$this->url->create("comments/update/{$properties["id"]}")?>'>Redigera</a> <a class="linkButton" href='<?=$this->url->create("comments/delete/{$properties["id"]}")?>'>Ta bort</a></em>

                        <?php endif ?>

                    </div>
                    <hr>
                <?php endforeach; ?>

            <?php endif ?>

            <?php if($answers) : ?>

                <h2>Svar</h2>

                <hr>

                <?php foreach ($answers as $answer) : ?>

                    <?php $properties = $answer->getProperties(); ?>

                    <div id="question">

                        <div id="poster">

                            <?php $acronym=$this->di->dispatcher->forward(['controller' => 'users','action' => 'getAcronym','params' => [$properties['user_id']]]); ?>

                            <em>Postad av: <a class="linkButton" href='<?=$this->url->create("users/username/{$acronym}")?>'><?=$acronym?></a></em>
                            <em>Svarade: </br> <?=$properties['created']?> </em>

                            <?php if ($properties['updated'] != null) : ?>

                                <em>Redigerad: <?=$properties['updated']?> </em>

                            <?php endif ?>

                            <?php if($properties['user_id'] == $this->session->get('user_id')) : ?>

                                <em>
                                    <a class="linkButton" href='<?=$this->url->create("answers/update/{$properties["id"]}")?>'>Redigera</a>
                                    <a class="linkButton" href='<?=$this->url->create("answers/delete/{$properties["id"]}")?>'>Ta bort</a>
                                </em>

                            <?php endif ?>

                        </div>

                        <div id="content">

                            <?=$properties['answer']?>

                        </div>
                        <a class="comment-add" href='<?=$this->url->create("comments/add/answer/{$properties["id"]}")?>'>Kommentera</a>
                    </div>

                    <hr>

                    <?php $acomments=$this->di->dispatcher->forward(['controller' => 'comments','action' => 'postcomments','params' => [$properties['id']]]);?>

                    <?php if($acomments) : ?>

                    <h4> Kommentarer till svar </h4>

                    <hr>

                    <?php foreach ($acomments as $comment) : ?>

                    <?php $properties = $comment->getProperties(); ?>

                    <div id="comment">

                    <?=$properties['comment']?> <?php $acronym = $this->di->dispatcher->forward(['controller' => 'users','action' => 'getAcronym','params' => [$properties['user_id']]]);?> <em><a class="linkButton" href='<?=$this->url->create("users/username/{$acronym}")?>'><?=$acronym?></a></em><em> - <?=$properties['created']?> </em>

                    <?php if($properties['user_id'] == $this->session->get('user_id')) : ?><em><a class="linkButton" href='<?=$this->url->create("comments/update/{$properties["id"]}")?>'>Redigera</a> <a class="linkButton" href='<?=$this->url->create("comments/delete/{$properties["id"]}")?>'>Ta bort</a></em>

                    <?php endif ?>

                    </div>
                    <hr>
                <?php endforeach; ?>

            <?php endif ?>

                <?php endforeach; ?>

            <?php endif ?>


    </div>

    <div id="buttons">

        <button class="linkButton" id="answer-button">Svara</button>
        <!-- <button class="linkButton" id="comment-button">Kommentera</button> -->

    </div>


    <div id="answer-form" class='answer-form'>

        <?=$answerform?>

    </div>

        <?php } else { echo 'Frågan existerar inte eller har tagit bort av användaren.';}?>
</div>




