

<div class='comments'>

    <div id="tags">

    <?php foreach ($tags as $tag) : ?>

        <?php $properties = $tag->getProperties(); ?>

        <a class="linkButton" href='<?=$this->url->create("questions/tagged/{$properties["tag"]}")?>'><?=$properties['tag']?> <em>(<?=$properties['num']?>)</em></a>

    <?php endforeach; ?>

    </div>

</div>


