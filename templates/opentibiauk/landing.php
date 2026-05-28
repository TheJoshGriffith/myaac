<?php
defined('MYAAC') or die('Direct access not allowed!');

// Landing hero + ladder/upcoming sidebar + feature cards.
// Included by template.php when on the homepage. $content holds the
// news-item list (rendered by news.php). Globals available: $status,
// $config, $db, $content.

$lua = $config['lua'] ?? [];
$serverName = $lua['serverName'] ?? 'OpenTibia';
$online   = (int)($status['players'] ?? 0);
$peak     = (int)($status['playersPeak'] ?? $status['players'] ?? 0);
$uptime   = $status['uptimeReadable'] ?? '—';
$isOnline = !empty($status['online']);

// Canary rate keys (fall back to '—' if unset)
$rateExp   = $lua['rateExp']   ?? ($lua['experienceStages'] ?? null);
$rateSkill = $lua['rateSkill'] ?? null;
$rateLoot  = $lua['rateLoot']  ?? null;
$worldType = $lua['worldType'] ?? 'open';
$protocol  = '15.11';

// Top-5 ladder by level/experience.
$ladder = [];
try {
    $res = $db->query(
        'SELECT `name`, `level`, `vocation` FROM `players` ' .
        'WHERE `group_id` < 4 AND `deletion` = 0 ' .
        'ORDER BY `experience` DESC, `level` DESC LIMIT 5'
    );
    foreach ($res as $row) { $ladder[] = $row; }
} catch (Throwable $e) { /* leave empty */ }

// Vocation → short label + .voc class
function ot_voc_class($v) {
    $v = (int)$v;
    // OTServBR vocation ids: 1 sorc,2 druid,3 pala,4 knight (+promoted +monk)
    $map = [1=>'s',2=>'d',3=>'p',4=>'k',5=>'s',6=>'d',7=>'p',8=>'k',9=>'k'];
    return $map[$v] ?? 's';
}
function ot_voc_short($v) {
    $v = (int)$v;
    $map = [0=>'none',1=>'sorc',2=>'druid',3=>'pala',4=>'knight',5=>'ms',6=>'ed',7=>'rp',8=>'ek',9=>'monk'];
    return $map[$v] ?? '—';
}

$ASCII = <<<'ART'
 ▄██████   ██▀▀█▀▀██     ██   ██  ██  ▀██
██    ██      ██         ██   ██  ██ ▄█▀
██    ██      ██   ████  ██   ██  █████▄
██    ██      ██         ██   ██  ██  ▀██▄
 ▀█████▀      ██    ██    ▀████▀  ██    ▀█
ART;
?>
<section class="hero">
    <div class="container">
        <pre class="ascii lg" aria-hidden="true"><?php echo htmlspecialchars($ASCII); ?></pre>
        <div class="hero-grid mt-24">
            <div>
                <h1>a mid-rate <br/>open-tibia server<br/>that doesn't <span class="blink">scold you</span></h1>
                <p class="lede">
                    <span class="cmt">the pitch, dry-readme edition</span><br/>
                    Botting is allowed. Free open world. Do what you want.
                    PvP is limited most days and encouraged on Saturdays.
                    Rates are mid-rate — quick through the early levels, easing
                    to 1× up top. A level 1000 should still take effort, not a
                    long weekend.
                </p>
                <div class="row gap-8 wrap">
                    <?php if (!$logged): ?>
                        <a class="btn primary" href="<?php echo getLink('account/create'); ?>">$ create account</a>
                    <?php else: ?>
                        <a class="btn primary" href="<?php echo getLink('account/manage'); ?>">$ my account</a>
                    <?php endif; ?>
                    <a class="btn" href="<?php echo getLink('online'); ?>">see who's online</a>
                    <a class="btn ghost" href="<?php echo getLink('news'); ?>">read /news</a>
                </div>
            </div>
            <div class="col gap-16">
                <div class="panel">
                    <div class="panel-head">
                        <h3>server status</h3>
                        <span class="panel-meta accent"><?php echo $isOnline ? '● online · ' . htmlspecialchars($uptime) : '○ offline'; ?></span>
                    </div>
                    <div class="grid-2" style="gap:14px">
                        <div class="col gap-4"><div class="upper mute">players</div><div class="fs-lg tabular"><?php echo $online; ?></div></div>
                        <div class="col gap-4"><div class="upper mute">peak today</div><div class="fs-lg tabular"><?php echo $peak; ?></div></div>
                        <div class="col gap-4"><div class="upper mute">protocol</div><div class="fs-lg tabular"><?php echo htmlspecialchars($protocol); ?></div></div>
                        <div class="col gap-4"><div class="upper mute">world type</div><div class="fs-lg"><?php echo htmlspecialchars($worldType); ?></div></div>
                        <?php // Rates are staged (see deploy stages.lua); config.lua exposes only
                              // the flat fallback (1×), so show the real curve instead. ?>
                        <div class="col gap-4"><div class="upper mute">exp rate</div><div class="fs-lg tabular">20×→1×</div></div>
                        <div class="col gap-4"><div class="upper mute">skill rate</div><div class="fs-lg tabular">15×→2×</div></div>
                        <div class="col gap-4"><div class="upper mute">loot rate</div><div class="fs-lg tabular"><?php echo $rateLoot !== null ? htmlspecialchars($rateLoot) . '×' : '—'; ?></div></div>
                        <div class="col gap-4"><div class="upper mute">pvp</div><div class="fs-lg">sat. only</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="page" style="padding-top:32px">
    <div class="container">
        <div class="grid-12">
            <div style="grid-column: span 8">
                <div class="row between mb-16">
                    <h2 class="upper" style="margin:0;font-size:var(--fs-sm);color:var(--fg)"># latest from the changelog</h2>
                    <a href="<?php echo getLink('news'); ?>" class="fs-xs">all news →</a>
                </div>
                <?php echo $content; /* news-item list */ ?>
            </div>
            <div style="grid-column: span 4">
                <div class="panel mb-16">
                    <div class="panel-head"><h3>top of the ladder</h3><a href="<?php echo getLink('highscores'); ?>" class="fs-xs">all</a></div>
                    <?php if (empty($ladder)): ?>
                        <div class="dim fs-sm">no characters yet. <a href="<?php echo getLink($logged ? 'account/characters/create' : 'account/create'); ?>">be the first.</a></div>
                    <?php else: foreach ($ladder as $i => $c): ?>
                        <div class="row between" style="padding:6px 0;border-bottom:1px dotted var(--border)">
                            <div class="row gap-8">
                                <span class="mute tabular" style="width:18px"><?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?></span>
                                <a href="<?php echo getPlayerLink($c['name'], false); ?>"><?php echo htmlspecialchars($c['name']); ?></a>
                            </div>
                            <div class="row gap-8 fs-xs dim">
                                <span class="tabular">L <?php echo (int)$c['level']; ?></span>
                                <span class="voc <?php echo ot_voc_class($c['vocation']); ?>"><?php echo ot_voc_short($c['vocation']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
                <div class="panel">
                    <div class="panel-head"><h3>upcoming</h3></div>
                    <div class="col gap-8 fs-sm">
                        <div class="row between"><span class="dim">red saturday</span><span class="warn">sat 14:00</span></div>
                        <div class="row between"><span class="dim">house auctions close</span><span class="tabular">fri 21:00</span></div>
                        <div class="row between"><span class="dim">server save</span><span class="tabular">daily 04:00</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hr-solid"></div>

        <div class="grid-3">
            <div class="panel">
                <div class="fs-xs mute mb-8"><span class="prompt"></span>cat /docs/rates</div>
                <div class="fs-lg mb-8">mid rates, on purpose</div>
                <div class="dim fs-sm">Fast early levels easing to 1× up top; loot stays at 1×. A level 1000 should take effort, not a long weekend. The grind is the point.</div>
            </div>
            <div class="panel">
                <div class="fs-xs mute mb-8"><span class="prompt"></span>man bot</div>
                <div class="fs-lg mb-8">botting is permitted</div>
                <div class="dim fs-sm">Walk a route, refill mana, hunt overnight. We don't care. AFK trading and exploit chains still get you banned.</div>
            </div>
            <div class="panel">
                <div class="fs-xs mute mb-8"><span class="prompt"></span>cron --list pvp</div>
                <div class="fs-lg mb-8">saturdays are red</div>
                <div class="dim fs-sm">One day a week, PvP rules relax. Houses stay locked. Bring a war shield and a sense of humour.</div>
            </div>
        </div>
    </div>
</section>
