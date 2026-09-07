<div class="footer-content">
    <div class="top">
        <p class="my-0">Histoires d'Autres Mondes - site du Chevalier Connard</p>
    </div>
    <div class="bottom">
        <ul class="footer-content__pages justify-center pl-0">
            <?php foreach ($footerLinks as $name => $link) { ?>
                <li class="pages-btn">
                    <a href="<?= $link ?>"><?= $name ?></a>
                </li>
            <?php } ?>
        </ul>
        <ul class="footer-content__socials justify-center">
            <?php
            $copySocial = false;
            foreach ($socials as $social) { ?>
                <li class="socials-btn" title="<?= $social['title'] ?>">
                    <?php switch($social['type']) {
                        case 'url': ?>
                            <a href="<?= $social['link'] ?>" target="_blank">
                                <?php if($social['icon'] !== null) { 
                                    switch($social['iconExtension']) {
                                        case 'svg': ?>
                                            <?= $social['icon'] ?>
                                        <?php break;
                                        default: ?>
                                            <img src="<?= $social['icon'] ?>" alt="<?= $social['title'] ?>">
                                        <?php break;
                                    } ?>
                                <?php } ?>
                            </a>
                            <?php break;
                        case 'copy': 
                            $copySocial = true; 
                            if($social['icon'] !== null) { 
                                switch($social['iconExtension']) {
                                    case 'svg': ?>
                                        <span class="copy-js" data-copy="<?= $social['link'] ?>" data-message="<?= $social['copy_message'] ?>">
                                            <?= $social['icon'] ?>
                                        </span>
                                    <?php break;
                                    default: ?>
                                        <img src="<?= $social['icon'] ?>" alt="<?= $social['title'] ?>" class="copy-js" data-copy="<?= $social['link'] ?>" data-message="<?= $social['copy_message'] ?>">
                                    <?php break;
                                } 
                            } 
                            break;
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
