<div class="container mt-5">
    <div class="row justify-center crud-card p-4 rounded-lg shadow-xl/20  <?= $formBgColor ?? '' ?> ">
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger mb-4">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>
        <div class="blocks flex justify-between gap-4">
            <div class="dernieres-actualites-form w-full max-w-4xl mx-auto p-4 bg-gray-800 rounded-lg shadow-md">
                <div class="text-white flex justify-between gap-4">
                    <span class="font-bold">Dernières actualités</span>
                    <?=  $dernieresActualitesForm ?? '' ?>
                </div>
                <div class="bg-white text-black mt-5">
                    <div id="toolbar-container-dernieres-actualites" class="toolbar-container">
                        <span class="ql-formats">
                            <select class="ql-font"></select>
                            <select class="ql-size"></select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-bold"></button>
                            <button class="ql-italic"></button>
                            <button class="ql-underline"></button>
                            <button class="ql-strike"></button>
                        </span>
                        <span class="ql-formats">
                            <select class="ql-color"></select>
                            <select class="ql-background"></select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-script" value="sub"></button>
                            <button class="ql-script" value="super"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-header" value="1"></button>
                            <button class="ql-header" value="2"></button>
                            <button class="ql-blockquote"></button>
                            <button class="ql-code-block"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-list" value="ordered"></button>
                            <button class="ql-list" value="bullet"></button>
                            <button class="ql-indent" value="-1"></button>
                            <button class="ql-indent" value="+1"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-direction" value="rtl"></button>
                            <select class="ql-align"></select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-link"></button>
                            <button class="ql-image"></button>
                            <button class="ql-video"></button>
                            <button class="ql-formula"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-clean"></button>
                        </span>
                    </div>
                    <div id="quill-editor-dernieres-actualites" class="bg-white quill-editor" data-form-content-input=".dernieres-actualites-form #derniere_actualites" data-toolbar-id="dernieres-actualites"></div>
                </div>
            </div>
            <div class="objectifs-form w-full max-w-4xl mx-auto p-4 bg-gray-800 rounded-lg shadow-md">
                <div class="text-white flex justify-between gap-4">
                    <span class="font-bold">Les objectifs du mois</span>
                    <?=  $objectifsForm ?? '' ?>
                </div>
                <div class="bg-white text-black mt-5">
                    <div id="toolbar-container-objectifs" class="toolbar-container">
                        <span class="ql-formats">
                            <select class="ql-font"></select>
                            <select class="ql-size"></select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-bold"></button>
                            <button class="ql-italic"></button>
                            <button class="ql-underline"></button>
                            <button class="ql-strike"></button>
                        </span>
                        <span class="ql-formats">
                            <select class="ql-color"></select>
                            <select class="ql-background"></select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-script" value="sub"></button>
                            <button class="ql-script" value="super"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-header" value="1"></button>
                            <button class="ql-header" value="2"></button>
                            <button class="ql-blockquote"></button>
                            <button class="ql-code-block"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-list" value="ordered"></button>
                            <button class="ql-list" value="bullet"></button>
                            <button class="ql-indent" value="-1"></button>
                            <button class="ql-indent" value="+1"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-direction" value="rtl"></button>
                            <select class="ql-align"></select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-link"></button>
                            <button class="ql-image"></button>
                            <button class="ql-video"></button>
                            <button class="ql-formula"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-clean"></button>
                        </span>
                    </div>
                    <div id="quill-editor-objectifs" class="bg-white quill-editor" data-form-content-input=".objectifs-form #objectifs" data-toolbar-id="objectifs"></div>
                </div>
            </div>
        </div>
    </div>
