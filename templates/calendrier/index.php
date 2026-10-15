<div class="content">
    <div class="container">
        <div class="row">
            <h1>Calendrier</h1>
            <a href="<?= $deconnexion_url ?>" class="btn btn-danger">Déconnexion</a>
        </div>
        <div class="row calendrier-container">
            <?php foreach ($cases as $case): ?>
                <div class="case <?= $case['is_open'] ? 'open' : 'closed' ?>">
                    <div class="case-day"><?= $case['day'] ?></div>
                    <?php if ($case['is_open']): ?>
                        <a class="case-content" href="<?= $case['route'] ?>">
                            <span><?= $case['content'] ?></span>
                    </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>