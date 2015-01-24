[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/frjf14/cflashmessage/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/frjf14/cflashmessage/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/frjf14/cflashmessage/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/frjf14/cflashmessage/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/frjf14/cflashmessage/badges/build.png?b=master)](https://scrutinizer-ci.com/g/frjf14/cflashmessage/build-status/master)

Flash messages - Anax MVC
=========================

cflashmessage handles error, success, notice and warning messages, which can be used to give the user feedback.

License
-------

This software is free software and carries a MIT license.

How to use flash messages
-------------------------

Adding it as shared service in the frontcontroller.

`$di->setShared('flashmessage', function() {
    $flashmessage = new \frjf14\FlashMessage\CFlashMessage();
    return $flashmessage;
});`

From the package Copy the css file: frjf14/cflashmessage/src/css/flashmessage.css to the webroot css folder: webroot/css/ then add the css to the frontcontroller `$app->theme->addStylesheet('css/flashmessage.css');` or just link directly to the CSS file within the package `$app->theme->addStylesheet('../vendor/frjf14/cflashmessage/src/css/flashmessage.css');`

Now you can add flash messages as an example error message: `$app->flashmessage->addError('An error message.');` and to display the message use `$app->flashmessage->getFlashMessages();` to retrive the message(s) as html.
