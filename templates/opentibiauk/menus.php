<?php
defined('MYAAC') or die('Direct access not allowed!');

// Default menu list. Admin → Menus to edit live.
return [
    MENU_CATEGORY_NEWS => [
        ['name' => 'home',      'link' => 'news',       'blank' => '0', 'color' => ''],
        ['name' => 'changelog', 'link' => 'changelog',  'blank' => '0', 'color' => ''],
    ],
    MENU_CATEGORY_ACCOUNT => [
        ['name' => 'login',    'link' => 'account/login',   'blank' => '0', 'color' => ''],
        ['name' => 'register', 'link' => 'account/create',  'blank' => '0', 'color' => ''],
        ['name' => 'manage',   'link' => 'account/manage',  'blank' => '0', 'color' => ''],
    ],
    MENU_CATEGORY_COMMUNITY => [
        ['name' => 'highscores', 'link' => 'highscores', 'blank' => '0', 'color' => ''],
        ['name' => 'who',        'link' => 'online',     'blank' => '0', 'color' => ''],
        ['name' => 'characters', 'link' => 'characters', 'blank' => '0', 'color' => ''],
        ['name' => 'guilds',     'link' => 'guilds',     'blank' => '0', 'color' => ''],
    ],
    MENU_CATEGORY_LIBRARY => [
        ['name' => 'houses',   'link' => 'houses',    'blank' => '0', 'color' => ''],
        ['name' => 'bestiary', 'link' => 'creatures', 'blank' => '0', 'color' => ''],
        ['name' => 'spells',   'link' => 'spells',    'blank' => '0', 'color' => ''],
    ],
];
