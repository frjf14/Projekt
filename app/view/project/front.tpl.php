<hr>

<div class='comments'>

    <div id="questions">
        <hr>
        <?php foreach ($questions as $question) : ?>

            <?php $properties = $question->getProperties(); ?>

            <div id="question">

                <div id="poster">

                    <em>Postad av: <?=$properties['user_id']?></em>
                    <em>Antal svar:  <?=$properties['answers']?></em>
                    <em>Ställde frågan: </br> <?=$properties['created']?> </em>

                    <?php if ($properties['updated'] != null) : ?>

                        <em>Redigerad: <?=$properties['updated']?> </em>

                    <?php endif ?>

                    <em>
                        <a href='<?=$this->url->create("questions/update/{$properties["id"]}")?>'>Redigera</a>
                        <a href='<?=$this->url->create("questions/delete/{$properties["id"]}")?>'>Ta bort</a>
                    </em>

                </div>

                <div id="content">
                    <h3 class="answer-title"><a href='<?=$this->url->create("questions/title/{$properties['title']}")?>'><?=$properties['title']?></a></h3>
                    <p class="wordwrap"><?php $question = (strlen($properties['question']) > 99) ? substr($properties['question'],0,100).'....' : $properties['question'];  echo $question; ?></p>

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

<div id='sidebar'>

    <div id="tags">

    <?php foreach ($tags as $tag) : ?>

        <?php $properties = $tag->getProperties(); ?>

        <a class="linkButton" href='<?=$this->url->create("questions/tagged/{$properties["tag"]}")?>'><?=$properties['tag']?> <em>(<?=$properties['num']?>)</em></a>

    <?php endforeach; ?>

    </div>

</div>




