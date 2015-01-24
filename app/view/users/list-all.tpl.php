
<table border="1" style="width:100%">

    <tr>
        <th>Gravatar</th>
        <th>Akronym</th>
        <th>Email</th>
        <th>Ställda frågor</th>

<!--        <th>Skapad</th>
        <th>Aktiv</th>
        <th>Uppdaterad</th>
        <th>Uppdatera</th>
        <th>Aktivera</th>
        <th>Radera</th> -->
    </tr>

<?php foreach ($users as $user) : ?>

    <?php $value = $user->getProperties(); ?>

    <tr style="text-align:center">
        <td><?php $gravatar = md5(strtolower(trim($value['email']))); echo "<img src='http://www.gravatar.com/avatar/" . $gravatar . "?s=60' alt='gravatar'>"; ?></td>
        <td><a class="linkButton" href='<?=$this->url->create("users/username/{$value['acronym']}")?>'><?=$value['acronym']?></a></td>
        <td><em>Namn:</em> <?=($value['name'])?></br>
        <em>Email:</em> <?=$value['email']?></td>
        <td><em>Ställda frågor:</em> <?=$value['questions']?></td>
<!--        <td><?=$value['created']?></td>
        <td><?=$value['active']?></td>
         <td><?=$value['updated']?></td>
        <td><a href='<?=$this->url->create("users/update/{$value["id"]}")?>'>Uppdatera</a></td>

        <?php if ($value['active'] == null) : ?>

            <td><a href='<?=$this->url->create("users/activate/{$value["id"]}")?>'>Aktivera</a></td>

        <?php endif ?>

        <?php if ($value['active'] != null) : ?>

            <td><a href='<?=$this->url->create("users/inactivate/{$value["id"]}")?>'>Avaktivera</a></td>

        <?php endif ?>

        <?php if ($value['deleted'] == null) : ?>

            <td><a href='<?=$this->url->create("users/softdelete/{$value["id"]}")?>'>Radera</a></td>

        <?php endif ?>

        <?php if ($value['deleted'] != null) : ?>

            <td><a href='<?=$this->url->create("users/recoverdelete/{$value["id"]}")?>'>Återställ</a></td>

        <?php endif ?> -->
    </tr>

<?php endforeach; ?>

</table>



