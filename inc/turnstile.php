<?php
if (isset($_GET['cf-turnstile-response'])) {
    include_once '../inc/config.inc';
    $curldata = array(
        'SK' => _TURNSTILE_SECRET,
        'RS' => $_GET['cf-turnstile-response'],
        'RI' => $_SERVER['REMOTE_ADDR'],
        'TKT'=> hash_hmac('sha256', strval(_TURNSTILE_SECRET) . strval($_GET['cf-turnstile-response']) . strval($_SERVER['REMOTE_ADDR']), _TURNSTILE_PROXY_SECRET)
    );
    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, _TURNSTILE_PROXY_ENDPOINT);
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($curldata));
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($verify);
    // var_dump($response);
    $responseData = json_decode($response);
    if ($responseData->success && $responseData->hostname == _TURNSTILE_DOMOIN) {
        $_SESSION['Humanity'] = _TURNSTILE_PERIOD;
    }
}
if ($_SESSION['admin']) {
    $_SESSION['Humanity'] = _TURNSTILE_PERIOD;
}
if (!isset($_SESSION['Humanity']) || $_SESSION['Humanity'] < 1) {
    $_SESSION['IP'] = $ip;
    $_SESSION['UA'] = $_SERVER['HTTP_USER_AGENT'];
    http_response_code(402);
    ?>
    <!doctype html>
    <html lang='<?php $_SESSION['LANG']; ?>'>
    <head>
        <meta name="theme-color" content="#FFA1C0" />
        <title>
            <?php __e('人机验证 - [ServiceName] ([Organisation])'); ?>
        </title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <script src='<?= _CDN_URL ?>/voez1.min.js' fetchpriority="high"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <script decode-utilities-auto src='<?= _CDN_URL ?>/jquery.js' fetchpriority="high"></script>
        <link href="<?= _CDN_URL ?>/origin.min.css" rel="stylesheet">
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js"></script>
    </head>

    <body class="js-enabled nhsuk-core app-u-background-pink" oncut="return false;" oncopy="return false;" onload="voez.init;">
        <a href="#content" class="nhsuk-skip-link">
            <?php __e('跳转到主要内容'); ?>
        </a>
        <div class="nhsuk-core">
            <header class="nhsuk-header" role="banner">
                <div class="nhsuk-width-container nhsuk-header__container">
                    <div class="nhsuk-header__logo">
                        <a class="nhsuk-header__link nhsuk-header__link--service " href="/">
                        <img alt='<?php __e('[Organisation]'); ?>' src='<?= _CDN_URL ?>/[Organisation].png' style='filter: brightness(0) invert(1); max-width: 200px; max-height: 
50px; height: auto; width: auto;' height='45em'> <span class="nhsuk-header__service-name">
                                <?php __e('[ServiceName]'); ?>
                            </span>
                        </a>
                    </div>
                </div>
            </header>
        </div>

        <div class="nhsuk-core" unselectable="on" onselectstart="return false;">
            <div class="nhsuk-width-container">
                <main id="content" class="nhsuk-main-wrapper" role="main">
                    <div class="nhsuk-u-reading-width">
                        <h1>
                            <?php __e('我们需要验证您是人类。'); ?>
                        </h1>
                        <p>
                            <?php __e('十分抱歉给店长带来了不好的体验，但使此服务可以长久运行且不受机器人滥用，我们需要定期对访客进行检查。'); ?>
                        </p>
                        <p>
                            <?php __e('验证过程是自动的。当您看到下方出现绿底白勾后，便可以点击下方的“继续”按钮。'); ?>
                        </p>
                        <form action="" method="GET">
                            <?php
                            foreach ($_GET as $key => $value) {
                                echo "<input type='hidden' name='$key' value='$value'>";
                            }
                            ?>
                            <div class="cf-turnstile" data-sitekey="<?php echo _TURNSTILE_SITEKEY; ?>"
                                data-language="<?php echo $_SESSION['LANG'] ?>"></div>
                            <hr />
                            <button class="nhsuk-button " type="submit" data-module="nhsuk-button">
                                <?php __e('继续'); ?>
                            </button>
                        </form>
                    </div>
                </main>
            </div>
        </div>

        <footer role="contentinfo">
            <div class="nhsuk-footer" id="nhsuk-footer">
                <div class="nhsuk-width-container">
                    <div class="nhsuk-grid-row">
                        <div class="nhsuk-grid-column-two-thirds">
                            <p class="nhsuk-body-s">
                                <?php __e('人机验证 (Turnstile)。您的登录状态和剩余页面访问计数不受影响。'); ?>
                            </p>
                            <hr class="nhsuk-section-break nhsuk-section-break--m nhsuk-section-break--visible">
                            <p><img alt='<?php __e('[Organisation]'); ?>' src='<?= _CDN_URL ?>/[Organisation].png' height='45em'></p>
                            <p class="nhsuk-body-s">
                                <a href="<?= _URL ?>">
                                    <?php __e('[Organisation] [ServiceName]'); ?>
                                </a><br /><a href="https://beian.miit.gov.cn">[PRC-ICP-Filling]</a>
                            </p>
                            <p class="nhsuk-body-s">
                                <?php __e('软体版本：');
                                echo _SOFTWARE_VERSION; ?>
                            </p>
                            <p class="nhsuk-body-s">
                                <?php __e('本档案馆内容版权所有，未经许可请勿转载。本站发布前已获授权。'); ?>
                                <br />
                                <?php __e('剧情内容 &copy;1999-{year} [CopyrightHolder]', array('year' => date("Y"))); ?>
                                <br />
                                <?php __e('网站系统 &copy;2021-{year} [Organisation], [Organisation], Chise Hachiroku', array('year' => date('Y'))); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        </div>
    </body>

    </html>
    <?php
    exit();
} else {
    http_response_code(200);
}
