<div class="slider w-full overflow-hidden h-full <?= $htmlClass ?>">
    <div class="slides h-full flex flex-row transition-transform ease-in-out" style="width: <?= $slides->count() * 100 ?>%">
        <?php foreach ($slides->getAll() as $key => $slide): ?>
            <div class="slide-container w-full h-full" style="width: <?= 100 / $slides->count() ?>%">
            <?php if ($slide->getLink()): ?>
                <a href="<?= $slide->getLink() ?>" class="slide-link">
            <?php endif; ?>
                <div class="slide <?= $slide->isActive() ? 'active' : '' ?> h-full">
                    <?php if ($slide->getImage()): ?>
                        <img src="<?= $slide->getImage() ?>" alt="<?= $slide->getDescription() ?>" class="slide-image w-full h-full object-cover">
                    <?php endif; ?>
                </div>
            <?php if ($slide->getLink()): ?>
                </a>
            <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="controls">
        <button class="prev"><</button>
        <button class="next">></button>
    </div>
    <div class="indicators">
        <?php foreach ($slides->getAll() as $key => $slide): ?>
            <span class="indicator <?= $slide->isActive() ? 'active' : '' ?>" data-slide="<?= $key ?>"></span>
        <?php endforeach; ?>
    </div>
</div>