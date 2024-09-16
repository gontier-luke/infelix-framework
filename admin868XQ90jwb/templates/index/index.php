<!DOCTYPE html>
<html lang="FR">

<head>
    <meta charset="UTF-8">
    <title>Accès Espace Administrateur</title>
    <link rel="stylesheet" href="<?= $siteRoot; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= $adminRoot; ?>assets/css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= $siteRoot; ?>admin34572ed7dqk/assets/scripts/modal.js"></script>
    <link rel="icon" type="image/x-icon" href="./assets/img/icon_question_mark.ico">
    <script src="<?= $adminRoot; ?>/assets/scripts/confirm-delete.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript"> var adminRoot = '<?= $adminRoot; ?>'; </script>
</head>

<body>
    <?php require_once $templatesRoot . 'header.php'; ?>

    <!-- Sub Header -->
    <div class="sub-header">
        <div class="left-part">
            <span class="title">Espace<br>Administrateur</span>
            <!--
            <p>Pour nous aider à évaluer et comparer les perceptions de la valeur de l'information entre les
                journalistes/professionnels de l'information et le grand public</p>
                !-->
        </div>
        <div class="right-part">
            <div class="top">
                <div class="top-left">
                    <img src="<?= $siteRoot; ?>assets/img/white-star.png" class="left-star" alt="white-star">
                    <img src="<?= $siteRoot; ?>assets/img/black-star.png" class="right-star" alt="black-star">
                </div>
                <div class="top-right">

                </div>
            </div>

            <div class="bot">
                <!--
                <a id="imsic-link" href="https://www.imsic.fr/"><img class="left-star" src="<?= $siteRoot; ?>assets/img/Help.png"/>L’IMSIC c’est quoi ?</a>
                !-->
            </div>
        </div>
    </div>

    <div class="desac-ip">
        <div class="desac-ip__error">
            <ul class="alert-error">
                <?php
                foreach ($ipErrors as $message) {
                ?>
                    <li>
                        <?= $message; ?>
                    </li>
                <?php
                }
                ?>
            </ul>
            <?= $addDesacIpForm ?>
        </div>
    </div>
    <!-- End Sub Header -->

    <div class="gestion">
        <div class="gestion__top">
            <div class="gestion__top--title">
                <h2>Gestion des adresses ip</h2>
                <div class="gestion_button">
                    <span>Vous pouvez supprimer des ip.</span>
                </div>
            </div>
            <div class="gestion__content">
                <ul class="alert-error">
                    <?php
                    foreach ($ipErrors as $message) {
                    ?>
                        <li>
                            <?= $message; ?>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
                <ul class="content">
                    <?php foreach ($ipAdresses as $ip) { ?>
                        <li>
                            <span><?= $ip['ip'] ?></span>
                            <span><?= $ip['date_last_co'] ?></span>
                            <a href="<?= $adminRoot ?>/ipAdresses/?action=remove&id=<?= $ip['ip'] ?>"><img class="trash" src="<?= $siteRoot; ?>assets/img/Empty_Trash.png" />Supprimer</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <?php if ($owner) { ?>

        <div class="gestion">
            <div class="gestion__top">
                <div class="gestion__top--title">
                    <h2>Gestion des administrateurs</h2>
                    <div class="gestion_button">
                        <span>Vous pouvez ajouter ou supprimer des administrateurs.</span>
                        <button class="showModal" data-form-name="addUser">Ajouter un admin</button>
                    </div>
                </div>
                <div class="gestion__content">
                    <ul class="alert-error">
                        <?php
                        foreach ($userErrors as $message) {
                        ?>
                            <li>
                                <?= $message; ?>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                    <ul class="content">
                        <?php foreach ($adminUsers as $user) { ?>
                            <li>
                                <span><?= $user['identifiant'] ?></span>
                                <span><?= $user['date'] ?></span>
                                <?php if ($user['identifiant'] != $_SESSION['login']) { ?>
                                    <a href="<?= $adminRoot ?>/user/?action=remove&id=<?= $user['identifiant'] ?>"><img class="trash" src="<?= $siteRoot; ?>assets/img/Empty_Trash.png" />Supprimer</a>
                                <?php } else {
                                ?>
                                    <div></div>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>


            <div class="modal addUser">
                <div class="modal_body">
                    <div class="modal__form">
                        <?= $addUserForm ?>
                    </div>
                    <div class="modal__constraint">
                        <p>Le mot de passe doit comporter :</p>
                        <ul>
                            <li>8 caractères</li>
                            <li>1 majuscule</li>
                            <li>1 minuscule</li>
                            <li>1 caractère spécial</li>
                        </ul>
                    </div>
                </div>
                <!-- stars -->
                <img src="<?= $siteRoot; ?>assets/img/white-star.png" class="left-star" alt="white-star">
                <img src="<?= $siteRoot; ?>assets/img/black-star.png" class="right-star" alt="black-star">
            </div>
        </div>

    <?php } ?>
    <div class="gestion gestion--question">
        <div class="gestion__top">
            <div class="gestion__top--title">
                <h2>Gestion du questionnaire</h2>
                <div class="gestion_button">
                    <span>Vous pouvez ajouter ou supprimer des questions.</span>
                    <button class="showModal showModalQuestion" data-form-name="addQuestion">Ajouter une question</button>
                </div>
            </div>

        </div>
        <div class="gestion__content">
            <ul class="alert-error">
                <?php
                foreach ($questionErrors as $message) {
                ?>
                    <li>
                        <?= $message; ?>
                    </li>
                <?php
                }
                ?>
            </ul>
            <ul class="content">
                <?php foreach ($questions as $question) { ?>
                    <li>
                        <span><?= $question['libelle'] ?></span>
                        <a href="<?= $adminRoot ?>/question/?action=remove&id=<?= $question['id'] ?>"><img class="trash" src="<?= $siteRoot; ?>assets/img/Empty_Trash.png" />Supprimer</a>
                        <a data-form-name="addQuestion" data-id="<?= $question['id'] ?>" class="showModal updateQuestion"><img class="update" src="<?= $siteRoot; ?>assets/img/pen.png" />Modifier</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="modal addQuestion">
        <div class="modal_body">
            <div class="modal__form">
                <?= $addQuestionForm ?>
            </div>
        </div>
    </div>

    <div class="gestion">
        <div class="gestion__top">
            <div class="gestion__top--title">
                <h2>Gestion des réponses</h2>
                <div class="gestion_button">
                    <span>Vous pouvez exporter les données.</span>
                    <a href="<?= $adminRoot; ?>/exportCSV">Exporter les réponses</a>
                </div>
            </div>
        </div>
    </div>
</body>
<?php require_once $templatesSiteRoot . 'footer.php'; ?>

</html>