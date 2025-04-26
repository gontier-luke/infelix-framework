<header>
    <!-- code captcha -->
    <link rel="stylesheet" href="<?= $siteRoot ?>assets/style/popupCaptchaStyle.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script type="text/javascript">
        const siteRoot = '<?= $siteRoot;?>';
    </script>
    <script src="<?= $siteRoot ?>assets/script/scriptCaptcha.js"></script>
    <?php include $templatesSiteRoot.'/popupCaptcha/index.php' ?>

    <a class="button" href="<?= $siteRoot ?>">
        <div id="left-part">
            <img class="left-star" src="<?= $siteRoot; ?>assets/img/ask-question.png"/>
            <span class="left-title">Infelix Commentator</span>
        </div>
    </a>

    <div id="right-part" class="button">
        <?php
        if (key_exists('token', $_SESSION)) {
            ?>
            <a href="<?= $adminRoot ?>/disconnect" id="disconnect">Déconnexion</a>
            <?php
        }
        ?>
        <a id="openPopupBtn"><img class="left-star" src="<?= $siteRoot; ?>assets/img/Help.png"/><span>Questionnaire</span></a>
    </div>
</header>