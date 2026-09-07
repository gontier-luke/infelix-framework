<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <link rel="icon" type="image/png" href="<?= $logo ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <?php foreach ($stylesheets as $stylesheet){ ?>
        <link rel="stylesheet" type="text/css" href="<?= $stylesheet ?>">
    <?php } ?>
    <?php foreach ($scripts as $script){ ?>
        <script src="<?= $script ?>"></script>
    <?php } ?>
</head>
<body class="<?= $bodyClass ?> bg-grey-100">
    <?php  if(!isset($displayMenuFooter) || $displayMenuFooter !== false) { ?>
    <header class="admin-header flex flex-col justify-start">
        <?php require 'header.php'; ?>
    </header>
    <?php } ?>
    <main>
        <?php  eval('?>' . $content); ?>
    </main>
</body>
</html>