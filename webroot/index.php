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

$di->set('form', '\Mos\HTMLForm\CForm');

$di->set('UsersController', function() use ($di) {
    $controller = new \Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('QuestionsController', function() use ($di) {
    $controller = new Anax\Questions\QuestionsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('AnswersController', function() use ($di) {
    $controller = new Anax\Answers\AnswersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('CommentsController', function() use ($di) {
    $controller = new Anax\Comments\CommentsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('TagsController', function() use ($di) {
    $controller = new Anax\Tags\TagsController();
    $controller->setDI($di);
    return $controller;
});

$di->setShared('flashmessage', function() {
    $flashmessage = new \frjf14\FlashMessage\CFlashMessage();
    return $flashmessage;
});

$app->session();

$app->router->add('', function() use ($app) {

  $app->theme->setTitle("Start");

  $app->views->addString('<h2>Senaste frågorna</h2>', 'main');

  $app->dispatcher->forward([
    'controller' => 'questions',
    'action'     => 'tenlatest',

  ]);

  $app->views->addString('<h2>Populära taggar</h2>', 'sidebar');

  $app->views->region('sidebar');

  $app->dispatcher->forward([
    'controller' => 'tags',
    'action'     => 'viewfront',
  ]);

  $app->views->addString('<h2>Senast aktiva användare</h2>', 'sidebar');

  $app->views->region('sidebar');

  $app->dispatcher->forward([
    'controller' => 'users',
    'action'     => 'tenlatest',
  ]);

});

$app->router->add('questions', function() use ($app) {

  $app->theme->setTitle("Frågor");

  $content = $app->fileContent->get('questionInfo2.md');
  $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

  $app->views->addString('<h2><i class="fa fa-newspaper-o"></i> Alla frågor</h2>', 'featured-1')
               ->addString('<p></p>', 'featured-2')
               ->addString('<p><a class="linkButton float-right" href=' . $app->url->asset("questions/ask") . '><i class="fa fa-question-circle fa-spin"></i> Ställ en fråga</a></p>', 'featured-3')
               ->addString($content, 'sidebar');


  $app->dispatcher->forward([
    'controller' => 'questions',
    'action'     => 'publicview',
  ]);

});

$app->router->add('tags', function() use ($app) {

    $app->theme->setTitle("Taggar");

    $app->dispatcher->forward([
    'controller' => 'tags',
    'action'     => 'view',
    ]);

    $content = $app->fileContent->get('tagsInfo.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    $app->views->addString($content, 'sidebar');

});

$app->router->add('users', function() use ($app) {

  $app->theme->setTitle("Användare");

  $app->dispatcher->forward([
    'controller' => 'users',
    'action'     => 'publiclist',
    //'params'     => array('page' => $i),
  ]);

    $content = $app->fileContent->get('userInfo.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    $app->views->addString($content, 'sidebar');

});

$app->router->add('about', function() use ($app) {

    $app->theme->setTitle("Om");

    $utvecklarInfo = $app->fileContent->get('utvecklarInfo.md');
    $utvecklarInfo = $app->textFilter->doFilter($utvecklarInfo, 'shortcode, markdown');

    $webbplatsInfo = $app->fileContent->get('webbplatsInfo.md');
    $webbplatsInfo = $app->textFilter->doFilter($webbplatsInfo, 'shortcode, markdown');

    $app->views->addString($webbplatsInfo,'main')
               ->addString($utvecklarInfo,'sidebar');

});

$app->router->handle();
$app->theme->render();
