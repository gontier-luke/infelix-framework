<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Accès Espace Administrateur</title>
    <link rel="stylesheet" href="<?= $siteRoot; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= $adminRoot; ?>assets/css/admin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../../assets/img/icon_question_mark.ico">
</head>
<body>
<?php require_once $templatesRoot . 'header.php'; ?>

<div class="content">
    <ul class="alert-error">
        <?php
        foreach ($errors as $message) {
            ?>
            <li>
                <?= $message; ?>
            </li>
            <?php
        }
        ?>
    </ul>
    <div id="adminConnect">
        <div id="form">
            <?= $form; ?>
        </div>
        <div id="text">
            <img class="left-star" src="<?= $siteRoot; ?>assets/img/white-star.png"/>
            <h1>Accès Espace<br/>Administrateur</h1>
            <img class="right-star" src="<?= $siteRoot; ?>assets/img/black-star.png"/>
        </div>
    </div>
</div>
<div id="void">
</div>
<?php require_once $templatesSiteRoot . 'footer.php'; ?>
</body>
</html>