<div class="container-fluid">
    <div class="row">
        <h1 class="mb-0 flex">
            <svg id="title-image" viewBox="0 0 1100 100">
                <circle cx="50%" cy="50%" r="150" class="title-circle" stroke-width="4" />
                <text  x="0" y="0" class="great-title">
                    <tspan x="50%" dy="0" text-anchor="middle">INFELIX</tspan>
                    <tspan x="50%" dy="1.2em" text-anchor="middle">COMMENTATOR</tspan>
                </text>
            </svg>
        </h1>
    </div>
    <div class="row">
        <div class="col-12 text-center secondary-section py-4">
            <p>
                <?=  $this->trans('Bonjour à toi ! Si tu es ici c\'est soit parce que tu me connais déjà, soit que tu ne me connais pas encore !') ?>
            </p>
            <p>
                <?=  $this->trans('Dans le second cas, je pense qu\'une présentation s\'impose. Donc me voici :') ?>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center py-4 px-2">
            <fieldset class="presentation-block-left presentation-block">
                <legend class="font-bold"><?=  $this->trans('Développeur de génie') ?></legend>
                bla bla
            </fieldset>
        </div>
    </div>
    <div id="circle"></div>
</div>