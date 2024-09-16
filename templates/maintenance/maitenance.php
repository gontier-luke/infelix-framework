<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <?php foreach ($stylesheets as $stylesheet){ ?>
        <link rel="stylesheet" type="text/css" href="<?= $stylesheet ?>">
    <?php } ?>
</head>
<body id="maintenance">
    <h2> Maintenance en cours </h2>

    <p> Le site est actuellement en maintenance. Veuillez revenir plus tard</p>
</body>
</html>