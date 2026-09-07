<div class="content">
    <div class="container-fluid">
        <div class="h-[800px] w-[1000px] mx-auto border-black border-2 flex flex-col" id="karaoke-container">
            <?php if($karaoke_video): ?>
                <video id="karaoke-video" class="w-full h-full object-contain">
                    <source src="<?= $karaoke_video ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <input type="range" id="progress-bar" class="" value="0" min="0" max="100">
                <audio id="karaoke-audio" src="<?= $karaoke_luke ?>"></audio>
                <button id="mute-button" class="">
                    Mute Karaoke
                </button>
                <button id="audio-mute-button" class="">
                    Mute Audio
                </button>
                <button id="pause-button" class="">
                    Pause Karaoke
                </button>
                <button id="replay-button" class="">
                    Replay Karaoke
                </button>
                <label for="volume-bar" class="">Volume</label>
                <input type="range" id="volume-bar" class="" value="100" min="0" max="100">
                <label for="audio-volume-bar" class="">Volume Audio</label>
                <input type="range" id="audio-volume-bar" class="" value="100" min="0" max="100">
            <?php else: ?>
                <p class="text-center text-gray-500">Aucune vidéo disponible pour cet événement.</p>
            <?php endif; ?>
        </div>
    </div>
</div>