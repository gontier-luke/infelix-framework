<div class="slider <?= $htmlClass ?>">
    <div class="slider-content row p-relative">
        <div class="slides col-12">
            <?php foreach ($slides->getAll() as $slide) { 
                    /** @var SliderableInterface $slide */
                ?>
                <div class="slide">
                    <img src="<?= $slide->getImage() ?>" alt="<?= $slide->getLabel() ?>">
                    <div class="slide-content">
                        <h2><?= $slide->getLabel() ?></h2>
                        <p><?= $slide->getDescription() ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="slider-controls col-12 p-absolute w-100">
            <div class="prev-slide">
                <i class="fas fa-chevron-left"></i>
            </div>
            <div class="next-slide">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>
    </div>
</div>