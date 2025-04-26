<div class="slider container <?= $htmlClass ?>">
    
    <div class="slider-content row flex-row p-relative">
        <div class="prev-slide navigations col-1 flex align-items-center">
            <i class="fas fa-chevron-left"></i>
        </div>
        <div class="slides col-10">
            <div class="slides-container">
                <?php
                $firstSlide = true;
                foreach ($slides->getAll() as $slide) { 
                        /** @var SliderableInterface $slide */
                    ?>
                    <div class="slide container flex-row <?php if($firstSlide){?>active<?php $first = false;}?>">
                        <img class="col-4" src="<?= $slide->getImage() ?>" alt="<?= $slide->getLabel() ?>">
                        <div class="slide-content col-8">
                            <h2 class="slide-content__title mt-0"><?= $slide->getLabel() ?></h2>
                            <p class="slide-content__desc"><?= $slide->getDescription() ?></p>
                            <a href="slide-content__link" target="<?= $slide->getTarget() ?>" href="<?= $slide->getLink() ?>"></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="next-slide navigations col-1 flex align-items-center">
            <i class="fas fa-chevron-right"></i>
        </div>
    </div>
</div>
