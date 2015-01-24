<div class='comments'>

    <div id="questions">
        <hr>
        <?php foreach ($questions as $question) : ?>

            <?php $properties = $question->getProperties(); ?>

            <div id="question">

                <div id="poster">

                    <?php $acronym=$this->di->dispatcher->forward(['controller' => 'users','action' => 'getAcronym','params' => [$properties['user_id']]]); ?>

                    <em>Postad av: <a class="linkButton" href='<?=$this->url->create("users/username/{$acronym}")?>'><?=$acronym?></a></em>
                    <em>Antal svar:  <?=$properties['answers']?></em>
                    <em>Ställde frågan: </br> <?=$properties['created']?> </em>

                    <?php if ($properties['updated'] != null) : ?>

                        <em>Redigerad: </br> <?=$properties['updated']?> </em>

                    <?php endif ?>

<!--                     <?php if($properties['user_id'] == $this->session->get('user_id')) : ?>

                        <em>
                            <a class="linkButton" href='<?=$this->url->create("questions/update/{$properties["id"]}")?>'>Redigera</a>
                            <a class="linkButton" href='<?=$this->url->create("questions/delete/{$properties["id"]}")?>'>Ta bort</a>
                        </em>

                    <?php endif ?>
 -->
                </div>

                <div id="content">
                    <h3 class="answer-title"><a href='<?=$this->url->create("questions/id/{$properties['id']}")?>'><?=$properties['title']?></a></h3>

                    <?php $question = (strlen($properties['question']) > 99) ? substr($properties['question'],0,100).'....' : $properties['question'];  echo $question; ?>

                <!-- <p><?=dump($comment)?></p> -->
                    <div id="tags">
                        <?php $tags = $this->di->dispatcher->forward(['controller' => 'tags','action' => 'getTagsByQuestionId','params' => [$properties['id']]]);?>

                        <?php foreach ($tags as $tag) : ?>

                            <?php $prop = $tag->getProperties(); ?>

                            <!-- <h4><?=$prop['tag']?></h4> -->

                            <a class="linkButton" href='<?=$this->url->create("questions/tagged/{$prop["tag"]}")?>'><?=$prop['tag']?></a>

                            <?php endforeach; ?>
                    </div>
                </div>

            </div>
            <hr>
        <?php endforeach; ?>

    </div>

</div>


