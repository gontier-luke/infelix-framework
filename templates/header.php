    <!-- code captcha -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script type="text/javascript">
        const siteRoot = '<?= $siteRoot;?>';
        const $templatesRoot = '<?= $templatesRoot;?>';
    </script>
    <?php foreach ($scripts as $script) { ?>
        <script src="<?= $script ?>"></script>
    <?php } ?>
    <div class="header-menu container-fluid">
        <h1 class="title"><a href="<?= $indexLink ?>"><?= $siteName ?></a></h1>
        <div class="pages">
            <?php foreach ($menuLinks as $name => $link) { ?>
                <a class="pages-btn" href="<?= $link ?>">
                    <div>
                        <?= $name ?>
                    </div>
            </a>
            <?php } ?>
        </div>
    </div>
