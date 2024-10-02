<div class="footer-content">
    <div class="left">
        <p>Crée et maintenu par : Infelix Commentator</p>
    </div>
    <div class="right">
        <div class="footer-content__pages">
            <?php foreach ($footerLinks as $name => $link) { ?>
                <div class="pages-btn">
                    <a href="<?= $link ?>"><?= $name ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
