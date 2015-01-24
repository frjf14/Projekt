<?php

require __DIR__.'/config_with_app.php';

$app->theme->configure(ANAX_APP_PATH . 'config/theme_project.php');

$app->navbar->configure(ANAX_APP_PATH . 'config/navbar_project.php');

$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);

$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/database_mysql.php');
    $db->connect();
    return $db;
});

$di->setShared('flashmessage', function() {
    $flashmessage = new \frjf14\FlashMessage\CFlashMessage();
    return $flashmessage;
});

$app->session();

$app->router->add('', function() use ($app) {

    $app->theme->setTitle("Setup/Reset");

    $user = $app->session->get('user');

    $sql = 'select name from project_user limit 1';

    $val = $app->db->execute($sql);

    $content = $app->fileContent->get('setupInfo.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    $app->views->addString($content, 'main');

    if($val == true && $user == 'admin' || $val == false) {

      $button = "<form method='post'><input type='submit' name='setup' value='Setup/Reset'/></form>";

      $app->views->addString($button, 'sidebar');

      if (isset($_POST['setup'])) {

        $sql = "drop table if exists project_voted;
        drop table if exists project_tag;
        drop table if exists project_answer;
        drop table if exists project_comment;
        drop table if exists project_question;
        drop table if exists project_user;

        create table project_user
        (
          id int auto_increment,
          acronym varchar(12) unique not null,
          email varchar(80),
          name varchar(80),
          questions int default 0,
          password char(255),
          created datetime,
          updated datetime,
          deleted datetime,
          active datetime,
          primary key(id)

        ) engine innodb character set utf8;

        create table project_question
        (
          id int auto_increment,
          user_id int,
          title varchar(80),
          question varchar(255),
          answers int default 0,
          created datetime,
          updated datetime,
          deleted datetime,
          primary key (id),
          foreign key (user_id) references project_user (id)

        ) engine innodb character set utf8;

        create table project_answer
        (
          id int auto_increment,
          user_id int,
          question_id int,
          answer varchar(255),
          created datetime,
          updated datetime,
          deleted datetime,
          primary key(id),
          foreign key (question_id) references project_question (id) ON DELETE CASCADE,
          foreign key (user_id) references project_user (id)

        ) engine innodb character set utf8;

        create table project_comment
        (
          id int auto_increment,
          user_id int,
          question_id int,
          answer_id int,
          comment varchar(255),
          created datetime,
          updated datetime,
          deleted datetime,
          primary key(id),
          foreign key (user_id) references project_user (id),
          foreign key (question_id) references project_question (id) ON DELETE CASCADE,
          foreign key (answer_id) references project_answer (id) ON DELETE CASCADE

        ) engine innodb character set utf8;

        create table project_tag
        (
          tag varchar(80),
          question_id int,
          primary key (tag, question_id),
          foreign key (question_id) references project_question (id) ON DELETE CASCADE

        ) engine innodb character set utf8;";

        $app->db->execute($sql);

        $app->session->set('user', null);
        $app->session->set('user_id', null);

        $app->flashmessage->addNotice('Databasen har Skapats/återställts.');

        $url = $app->url->create('');

        $app->response->redirect($url);
      }
    }
});

$app->router->handle();
$app->theme->render();
