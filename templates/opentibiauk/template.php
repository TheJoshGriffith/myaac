<?php
defined('MYAAC') or die('Direct access not allowed!');

// Resolve the active flat list of top-level nav links from the configured
// MyAAC menus. We render them as plain `<a>` in the top bar (no
// categories/dropdowns; terminal aesthetic).
$menus = get_template_menus();
$flat_nav = [];
foreach ($menus as $category => $links) {
    foreach ($links as $link) {
        $flat_nav[] = $link;
    }
}

$playersOnline = (int)($status['players'] ?? 0);
$serverOnline  = !empty($status['online']);
$serverName    = $config['lua']['serverName'] ?? 'OpenTibia';
?><!DOCTYPE html>
<html lang="en" data-palette="phosphor">
<head>
    <?php echo template_place_holder('head_start'); ?>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($title); ?> — <?php echo htmlspecialchars($serverName); ?></title>
    <link rel="stylesheet" href="<?php echo $template_path; ?>/style.css" type="text/css" />
    <script>
        // Palette restore — runs before paint to avoid flash.
        (function () {
            try {
                var p = localStorage.getItem('opentibia.palette');
                if (p && /^[a-z]+$/.test(p)) {
                    document.documentElement.setAttribute('data-palette', p);
                }
            } catch (e) {}
        })();
    </script>
    <?php echo template_place_holder('head_end'); ?>
</head>
<body>
    <?php echo template_place_holder('body_start'); ?>

    <div class="shell">
        <!-- top bar -->
        <header class="topbar">
            <div class="topbar-inner">
                <a href="<?php echo getLink('news'); ?>" class="brand">
                    <span class="dot"></span><?php echo htmlspecialchars($serverName); ?><span class="brand-host"></span>
                </a>
                <nav class="topnav">
                    <?php foreach ($flat_nav as $link): ?>
                        <a href="<?php echo $link['link_full']; ?>" <?php echo $link['target_blank']; ?>>
                            <?php echo htmlspecialchars($link['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <span class="statuspill" title="server status">
                    <?php if ($serverOnline): ?>
                        <span class="blip"></span><?php echo $playersOnline; ?> online
                    <?php else: ?>
                        offline
                    <?php endif; ?>
                </span>
                <?php if (!$logged): ?>
                    <a class="btn sm" href="<?php echo getLink('account/manage'); ?>">login</a>
                    <a class="btn sm primary" href="<?php echo getLink('account/create'); ?>">$ create account</a>
                <?php else: ?>
                    <a class="btn sm" href="<?php echo getLink('account/manage'); ?>"><?php echo htmlspecialchars($account_logged->getName()); ?></a>
                    <a class="btn sm ghost" href="<?php echo getLink('account/logout'); ?>">logout</a>
                <?php endif; ?>
            </div>
        </header>

        <!-- page content (rendered by MyAAC's page system) -->
        <?php
        // Landing = the news index with no specific article/archive view.
        // MyAAC uses PATH_INFO routing, so $_GET['subtopic'] is empty on
        // every page — use the router's PAGE constant instead (it resolves
        // to 'news' on the homepage).
        $currentPage = defined('PAGE') ? PAGE : '';
        $isLanding = ($currentPage === 'news' || $currentPage === '')
            && !isset($_GET['id']) && !isset($_GET['archive']);

        if ($isLanding && is_file(__DIR__ . '/landing.php')) {
            // landing.php emits its own <section> chrome (hero + grid + features)
            // and consumes $content (the news-item list) inline.
            include __DIR__ . '/landing.php';
        } else {
            ?>
            <main class="page">
                <div class="container">
                    <div class="pagehead">
                        <div class="crumb"><a href="<?php echo getLink('news'); ?>">~</a> / <?php echo htmlspecialchars(strtolower($title)); ?></div>
                        <h1><?php echo htmlspecialchars(strtolower($title)); ?></h1>
                    </div>
                    <?php echo $content; ?>
                </div>
            </main>
            <?php
        }
        ?>

        <!-- footer -->
        <footer class="foot">
            <div class="container">
                <div>
                    <span class="cmt">// <?php echo htmlspecialchars($serverName); ?> — terminal frontend, <?php echo date('Y'); ?></span>
                </div>
                <div class="row gap-16">
                    <span class="dim">palette:</span>
                    <select id="palette-switch" class="select" style="width: auto; padding: 4px 8px; font-size: var(--fs-xs);">
                        <option value="phosphor">phosphor</option>
                        <option value="amber">amber</option>
                        <option value="paper">paper</option>
                        <option value="ice">ice</option>
                    </select>
                    <span class="dim">·</span>
                    <a href="<?php echo getLink('forum'); ?>">forum</a>
                    <a href="<?php echo getLink('faq'); ?>">faq</a>
                    <a href="https://github.com/TheJoshGriffith/myaac" target="_blank" rel="noopener">source</a>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // palette switcher
        (function () {
            var sw = document.getElementById('palette-switch');
            if (!sw) return;
            var saved = localStorage.getItem('opentibia.palette') || 'phosphor';
            sw.value = saved;
            sw.addEventListener('change', function () {
                var v = sw.value;
                document.documentElement.setAttribute('data-palette', v);
                try { localStorage.setItem('opentibia.palette', v); } catch (e) {}
            });
        })();

        // mark active top nav link by URL
        (function () {
            var here = window.location.pathname + window.location.search;
            document.querySelectorAll('.topnav a').forEach(function (a) {
                var href = a.getAttribute('href') || '';
                if (href && here.indexOf(href.replace(/^https?:\/\/[^\/]+/, '')) !== -1) {
                    a.classList.add('active');
                }
            });
        })();
    </script>
</body>
</html>
