<div class="content">
    <div class="container" id="karaoke-container">
        <?php if($karaoke_video): ?>
            <div class="video-overlay">
                <video id="karaoke-video">
                    <source src="<?= $karaoke_video ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div class="pause-overlay-content">
                    <div class="pause_icon"><?= $icons['pauseicon'] ?></div>
                    <div class="play_icon"><?= $icons['playicon'] ?></div>
                </div>
            </div>
            <div class="karaoke-controls">
                <div class="pause-controls karaoke-button">
                    <div class="pause_icon karaoke-icon"><?= $icons['pauseicon'] ?></div>
                    <div class="play_icon karaoke-icon"><?= $icons['playicon'] ?></div>
                </div>
                <input type="range" id="progress-bar" class="" value="0" min="0" max="100">
                <div class="deployable volume">
                    <div class="karaoke-button volume-button highvolume">
                        <div class="karaoke-icon lowvolume_icon"><?= $icons['lowvolumeicon'] ?></div>
                        <div class="karaoke-icon highvolume_icon"><?= $icons['highvolumeicon'] ?></div>
                        <div class="karaoke-icon mutevolume_icon"><?= $icons['mutevolumeicon'] ?></div>
                    </div>
                    <div class="karaoke-volume">
                        <input orient="vertical" type="range" id="volume-bar" class="volume-range" value="100" min="0" max="100">
                    </div>
                </div>
                <div class="deployable voix">
                    <div class="karaoke-button voix-button unmute">
                        <div class="karaoke-icon unmute_icon"><?= $icons['unmuteicon'] ?></div>
                        <div class="karaoke-icon mute_icon"><?= $icons['muteicon'] ?></div>
                    </div>
                    <div class="karaoke-volume">
                        <input orient="vertical" type="range" id="audio-volume-bar" class="audio-volume-range" value="100" min="0" max="100">
                    </div>
                </div>
                <div id="replay-button" class="karaoke-button">
                    <div class="karaoke-icon"><?= $icons['replayicon'] ?></div>
                </div>
                <audio id="karaoke-audio" src="<?= $karaoke_luke ?>"></audio>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500">Aucune vidéo disponible pour cet événement.</p>
        <?php endif; ?>
    </div>
</div>