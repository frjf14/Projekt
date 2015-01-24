<?php if($user) : ?>

    <?php $value = $user->getProperties(); $url="users/username/{$value['acronym']}"; $this->session->set('url', $url); $user_id = $this->session->get('user_id');?>

    <?php if($value['deleted'] == null || $value['id'] == $user_id) : ;?>

        <h1>Användare</h1>

        <table border="1" style="width:100%">

            <tr>
                <th>Gravatar</th>
                <th>Akronym</th>
                <th>Email</th>
                <th>Namn</th>
                <th>Registrerad</th>
                <th>Senast inloggad</th>
                <?php $user_id = $this->session->get('user_id'); if($value['id'] == $user_id) : ?>
                    <th>Uppdatera</th>
                    <th>Radera</th>
                <?php endif ?>
            </tr>


            <tr style="text-align:center">

                <td><?php $gravatar = md5(strtolower(trim($value['email']))); echo "<img src='http://www.gravatar.com/avatar/" . $gravatar . "?s=70' alt='gravatar'>"; ?></td>
                <td><?=$value['acronym']?></td>
                <td><?=$value['email']?></td>
                <td><?=$value['name']?></td>
                <td><?=$value['created']?></td>
                <td><?=$value['active']?></td>
                <?php $user_id = $this->session->get('user_id'); if($value['id'] == $user_id) : ?>
                    <td><a href='<?=$this->url->create("users/update/{$value["id"]}")?>'>Redigera</a></td>

                    <?php if ($value['deleted'] == null) : ?>

                        <td><a href='<?=$this->url->create("users/softdelete/{$value["id"]}")?>'>Radera</a></td>

                    <?php endif ?>

                    <?php if ($value['deleted'] != null) : ?>

                        <td><a href='<?=$this->url->create("users/recoverdelete/{$value["id"]}")?>'>Återställ</a></td>

                    <?php endif ?>
                <?php endif ?>
            </tr>

        </table>

        <div class='questions'>

            <div id="questions">

                <?php if($questions) : ?>

                    <div id="post-header"> <h2>Ställda frågor</h2> </div>

                    <hr>

                    <?php foreach ($questions as $question) : ?>



                        <?php $properties = $question->getProperties(); ?>

                        <div id="question">

                            <div id="poster">

                                <em>Antal svar:  <?=$properties['answers']?></em>
                                <em>Ställde frågan: </br> <?=$properties['created']?> </em>

                                <?php if ($properties['updated'] != null) : ?>

                                    <em>Redigerad: <?=$properties['updated']?> </em>

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
                                <?php $question = (strlen($properties['question']) > 99) ? substr($properties['question'],0,100).'....' : $properties['question'];  echo $question; ?>

                                <div id="tags">

                                    <?php $tags = $this->di->dispatcher->forward(['controller' => 'tags','action' => 'getTagsByQuestionId','params' => [$properties['id']]]);?>

                                    <?php foreach ($tags as $tag) : ?>

                                        <?php $prop = $tag->getProperties(); ?>

                                        <a class="linkButton" href='<?=$this->url->create("questions/tagged/{$prop["tag"]}")?>'><?=$prop['tag']?></a>

                                    <?php endforeach; ?>

                                </div>

                            </div>

                        </div>

                        <hr>

                    <?php endforeach; ?>

                <?php endif ?>

            </div>

            <?php if($answers) : ?>

                        <h2>Postade svar</h2>

                        <hr>

                        <?php foreach ($answers as $answer) : ?>

                            <?php $properties = $answer->getProperties(); ?>

                            <div id="question">

                                <div id="poster">

                                    <em>Postad av: <?=$this->di->dispatcher->forward(['controller' => 'users','action' => 'getAcronym','params' => [$properties['user_id']]]);?> </em>
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

                            </div>

                            <hr>

                        <?php endforeach; ?>

            <?php endif ?>

            <?php if($comments) : ?>

                <div id="post-header"> <h2>Skrivna Kommentarer</h2> </div>

                <hr>

                <?php foreach ($comments as $comment) : ?>

                        <?php $properties = $comment->getProperties(); ?>

                        <div id="comment">

                           <?=$properties['comment']?> <em><?=$this->di->dispatcher->forward(['controller' => 'users','action' => 'getAcronym','params' => [$properties['user_id']]]);?></em><em> - <?=$properties['created']?> </em>

                            <?php if($properties['user_id'] == $this->session->get('user_id')) : ?>

                                <em><a class="linkButton" href='<?=$this->url->create("comments/update/{$properties["id"]}")?>'>Redigera</a> <a class="linkButton" href='<?=$this->url->create("comments/delete/{$properties["id"]}")?>'>Ta bort</a></em>

                            <?php endif ?>

                        </div>

                        <hr>

                <?php endforeach; ?>

            <?php endif ?>

        </div>

    <?php endif ?>

    <?php if($value['deleted'] != null && $value['id'] != $user_id) { echo '<h1>Användaren finns inte längre kvar :(</h1>'; } elseif($value['deleted'] != null) { echo '<h2>Din användare är borttagen vilket gör att andra inte längre kan se din profil. Du kan återställa den genom att klicka på återställ.</h2>'; } ?>

<?php endif ?>



