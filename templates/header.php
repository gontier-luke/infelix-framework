    <script type="text/javascript">
        const siteRoot = '<?= $siteRoot;?>';
        const $templatesRoot = '<?= $templatesRoot;?>';
    </script>
    <?php foreach ($scripts as $script) { ?>
        <script src="<?= $script ?>"></script>
    <?php } ?>
    <div class="header-menu container-fluid closed">
        <div class="title"><a href="<?= $indexLink ?>"><?= $siteName ?></a><div class="menucross"><span></span><span></span></div></div>
        <div class="pages">
            <?php foreach ($menuLinks as $name => $link) { ?>
                <a class="pages-btn" href="<?= $link ?>">
                        <?= $name ?>
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="menu-overlay">
        <span class="menu-overlay-bar"></span>
        <span class="menu-overlay-bar"></span>
        <span class="menu-overlay-bar"></span>
    </div>
