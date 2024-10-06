<div class="footer-content">
    <div class="top">
        <p>Crée et maintenu par : Infelix Commentator</p>
    </div>
    <div class="bottom">
        <ul class="footer-content__pages flex flex-row container justify-content-center align-items-center">
            <?php foreach ($footerLinks as $name => $link) { ?>
                <li class="pages-btn">
                    <a href="<?= $link ?>"><?= $name ?></a>
                </li>
            <?php } ?>
        </ul>
        <ul class="footer-content__socials flex flex-row container justify-content-center align-items-center">
            <?php
            $copySocial = false;
            foreach ($socials as $social) { ?>
                <li class="socials-btn" title="<?= $social['title'] ?>">
                    <?php switch($social['type']) {
                        case 'url': ?>
                            <a href="<?= $social['link'] ?>" target="_blank">
                                <img src="<?= $siteRoot . $social['icon'] ?>" alt="<?= $social['title'] ?>">
                            </a>
                            <?php break;
                        case 'copy': 
                            $copySocial = true; ?>
                            <img src="<?= $siteRoot . $social['icon'] ?>" alt="<?= $social['title'] ?>" class="copy-js" data-copy="<?= $social['link']?>" data-message="<?= $social['copy_message']?>">
                            <?php break;
                    } ?>
                </li>
            <?php } ?>
    </div>
</div>

<?php if($copySocial) { ?>
    <div class="modal modal-copy container-sm">
        <div class="row">
            <div class="col">
                <div class="modal-header">
                    <span class="modal-close">&times;</span>
                </div>
                <div class="modal-content">
                    <p class="modal-content__message text-center">
                        Copié dans le presse-papier.
                    </p>
                    <div class="extra-message"></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
