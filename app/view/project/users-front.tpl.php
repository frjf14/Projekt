<div id="users">

<?php foreach ($users as $user) : ?>

<table style="width:45%">

    <?php $value = $user->getProperties(); ?>

    <tr style="text-align:center" height="60px" >
        <td width="60px" height="60px"><?php $gravatar = md5(strtolower(trim($value['email']))); echo "<img src='http://www.gravatar.com/avatar/" . $gravatar . "?s=50' alt='gravatar'>"; ?></td>
        <td width="60px" height="60px"><a class="linkButton" href='<?=$this->url->create("users/username/{$value['acronym']}")?>'><?=$value['acronym']?></a></td>
    </tr>

</table>

<?php endforeach; ?>

</div>


