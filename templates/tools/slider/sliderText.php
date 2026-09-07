<div class="slider w-full overflow-hidden h-full <?= $htmlClass ?>">
    <div class="slides h-full flex flex-row transition-transform ease-in-out" style="width: <?= $slides->count() * 100 ?>%">
        <?php foreach ($slides->getAll() as $key => $slide): ?>
            <div class="slide-container px-4 w-full h-full" style="width: <?= 100 / $slides->count() ?>%">
                <div class="slide <?= $slide->isActive() ? 'active' : '' ?> h-full">
                    <?php if ($slide->getImage()): ?>
                        <div class="slide-image-container w-2/12 h-full">
                            <img src="<?= $slide->getImage() ?>" alt="<?= $slide->getDescription() ?>" class="slide-image w-full h-full object-cover">
                        </div>
                    <?php endif; ?>
                    <div class="slide-text-container w-10/12 h-full flex flex-col justify-center p-4">
                        <h3 class="slide-title text-2xl font-bold mb-2"><?= $slide->getLabel() ?></h3>
                        <p class="slide-description text-base"><?= $slide->getDescription() ?></p>
                        <?php if ($slide->getLink()): ?>
                            <a href="<?= $slide->getLink() ?>" class="slide-link rounded-lg bg-[dark-red]-500 text-white">Plus d'informations</a>
                        <?php endif; ?>
                    </div>
                </div>
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