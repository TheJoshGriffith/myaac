<?php
defined('MYAAC') or die('Direct access not allowed!');

// Theme: OpenTibia.UK
// Terminal/monospace aesthetic, JetBrains Mono, four palettes.
// Hero on landing is configurable: ascii (default), manifesto, live, map.

$config['theme_hero_variant'] = 'ascii'; // ascii | manifesto | live | map

// Menus are surfaced as plain links in the top bar. Adminable via the
// MyAAC admin → Menus page; this is just the default list at fresh install.
$config['menu_categories'] = [
    MENU_CATEGORY_NEWS      => ['id' => 'news',      'name' => 'news'],
    MENU_CATEGORY_ACCOUNT   => ['id' => 'account',   'name' => 'account'],
    MENU_CATEGORY_COMMUNITY => ['id' => 'community', 'name' => 'community'],
    MENU_CATEGORY_LIBRARY   => ['id' => 'library',   'name' => 'library'],
    MENU_CATEGORY_SHOP      => ['id' => 'shop',      'name' => 'shop'],
];

$config['menus'] = require __DIR__ . '/menus.php';
