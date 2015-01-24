<?php
/**
 * Config-file for navigation bar.
 *
 */
return [

    // Use for styling the menu
    'class' => 'navbar',

    // Here comes the menu strcture
    'items' => [

        'start' => [
            'text'  =>'Start',
            'url'   =>'',
            'title' => 'Start sida'
        ],

        // This is a menu item
        'questions' => [
            'text'  =>'Frågor',
            'url'   =>'questions',
            'title' => 'Sida för frågor'
        ],

        // This is a menu item
        'tags' => [
            'text'  =>'Taggar',
            'url'   =>'tags',
            'title' => 'Sida för taggar'
        ],

        // This is a menu item
        'users'  => [
            'text'  => 'Användare',
            'url'   => 'users',
            'title' => 'Sida för användare'
        ],

        // This is a menu item
        'about'  => [
            'text'  => 'Om',
            'url'   => 'about',
            'title' => 'Info om Sidan',
        ],
    ],

    // Callback tracing the current selected menu item base on scriptname
    'callback' => function($url) {
        if ($url == $this->di->get('request')->getRoute()) {
            return true;
        }
    },

    // Callback to create the urls
    'create_url' => function($url) {
        return $this->di->get('url')->create($url);
    },
];
