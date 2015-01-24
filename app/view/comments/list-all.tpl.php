<hr>

<div class='comments'>

    <?php foreach ($comments as $comment) : ?>

        <?php $properties = $comment->getProperties(); ?>

        <div id="comment">

            <div id="poster">

                <h4><?=$properties['title']?></h4>
                <em>Postad av: <?=$properties['name']?></em>
                <em>Email:  <?=$properties['email']?></em>
                <em>Postad: <?=$properties['posted']?> </em>

                <?php if ($properties['edited'] != null) : ?>

                    <em>Redigerad: <?=$properties['edited']?> </em>

                <?php endif ?>

                <em>
                    <a href='<?=$this->url->create("comments/update/{$properties["id"]}")?>'>Redigera</a>
                    <a href='<?=$this->url->create("comments/delete/{$properties["id"]}/{$properties["page"]}")?>'>Ta bort</a>
                </em>

            </div>

            <div id="content">
                <p><?=$properties['comment']?></p>
            </div>
            <!-- <p><?=dump($comment)?></p> -->
        </div>

    <?php endforeach; ?>

</div>


