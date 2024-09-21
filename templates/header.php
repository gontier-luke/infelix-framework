    <!-- code captcha -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script type="text/javascript">
        const siteRoot = '<?= $siteRoot;?>';
        const $templatesRoot = '<?= $templatesRoot;?>';
    </script>
    <script src="<?= $siteRoot ?>assets/script/scriptCaptcha.js"></script>
    <div class="header-menu container-fluid">
        <h1 class="title"><a href="/">Infelix Commentator</a></h1>
        <div class="pages">
            <?php foreach ($menuLinks as $name => $link) { ?>
                <div class="pages-btn">
                    <a href="<?= $link ?>"><?= $name ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
