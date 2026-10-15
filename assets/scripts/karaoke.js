$(document).ready(function() {
    // Initialize the karaoke functionality
    initKaraoke();
});


function initKaraoke() {
    const container = $('#karaoke-container');
    const progressBarContainer = container.find('#progress-bar-container');
    const audioVolume = 100; // Volume de l'audio (0 à 100)
    const videoVolume = 100; // Volume de la vidéo (0 à 100)

    // Récupérer la vidéo et l'audio
    const videoOverlay = $('.video-overlay');
    const pauseOverlayContent = $('.pause-overlay-content');
    const video = $('#karaoke-video');
    const audio = $('#karaoke-audio');
    if(!video || !audio || video.duration === 0) {
        console.error('Vidéo ou audio non trouvé dans le conteneur karaoke.');
        return;
    }
    
    video[0].volume = videoVolume / 100;
    audio.volume = audioVolume / 100;
    // Récupérer les CTA
    const pauseButton = container.find('.pause-controls');
    const replayButton = container.find('#replay-button');
    const progressBar = container.find('#progress-bar');
    const fullscreenButton = container.find('#fullscreen-button');
    const volumeButton = container.find('.volume-button');
    const audioVolumeButton = container.find('#audio-mute-button');
    const volumeBar = container.find('#volume-bar');
    const audioVolumeBar = container.find('#audio-volume-bar');

    // Synchroniser la vidéo et l'audio
    syncVideoAudio();
    let intervalId;
    video.on('play', function() {
        intervalId = setInterval(syncVideoAudio, 500); // Vérifier la synchronisation toutes les 1/2 secondes
        audio.play();
        pauseOverlayContent.addClass('playing');
        pauseButton.addClass('playing');
    });

    video.on('pause', function() {
        audio.pause();
        pauseOverlayContent.removeClass('playing');
        pauseButton.removeClass('playing');
        clearInterval(intervalId);
    });

    video.on('seeking', function() {
        syncVideoAudio();
    });

    video.on('ended', function() {
        clearInterval(intervalId);
    });
    $(audio).on('ended', function() {
        clearInterval(intervalId);
    });

    // Gérer les boutons de contrôle
    pauseButton.on('click', function() {
        if (video[0].paused) {
            video[0].play();

            return;
        }
        video[0].pause();
    });

    $(document).on('keydown', function(e) {
        switch(e.key) {
            case ' ':
                e.preventDefault();
                pauseButton.click();
                break;
            case '0':
            case 'à':
                e.preventDefault();
                replayButton.click();
                break
            case 'ArrowDown':
                e.preventDefault();
                audioVolumeBar.val(Math.max(0, audioVolumeBar.val() - 10));
                volumeBar.val(Math.max(0, volumeBar.val() - 10));
                audio.volume = audioVolumeBar.val() / 100;
                video[0].volume = volumeBar.val() / 100;
                break;
            case 'ArrowUp':
                e.preventDefault();
                audioVolumeBar.val(Math.min(100, parseInt(audioVolumeBar.val()) + 10));
                volumeBar.val(Math.min(100, parseInt(volumeBar.val()) + 10));
                audio.volume = audioVolumeBar.val() / 100;
                video[0].volume = volumeBar.val() / 100;
                break;
            case 'l':
                e.preventDefault();
                audioVolumeButton.click();
                break;
            case 'm':
                e.preventDefault();
                if(audio.muted == video[0].muted) {
                    audioVolumeButton.click();
                }
                volumeButton.click();
                break;
            case 'ArrowLeft':
                e.preventDefault();
                let newProgress = Math.max(0, (parseFloat(video[0].currentTime) - 5) * 100 / video[0].duration);
                progressBar.val(newProgress);
                updateVideoProgress(newProgress);
                break;
            case 'ArrowRight':
                e.preventDefault();
                let newProgressRight = Math.min(100, (parseFloat(video[0].currentTime) + 5) * 100 / video[0].duration);
                progressBar.val(newProgressRight);
                updateVideoProgress(newProgressRight);
                break;
        }
    });

    replayButton.on('click', function() {
        progressBar.val(0);
        progressBar.trigger('input');
        video[0].play();
    });

    progressBar.on('input', function() {
        const progress = parseFloat(progressBar.val()).toFixed(2);
        updateVideoProgress(progress);
    });

    volumeBar.on('input', function() {
        const volume = parseFloat(volumeBar.val()).toFixed(2);
        video[0].volume = volume / 100;
        switch(true) {
            case volume <= 0:
                volumeButton.removeClass('lowvolume');
                volumeButton.removeClass('highvolume');
                volumeButton.addClass('muted');
                if(!video[0].muted) {
                    volumeButton.click();
                }
                break;
            case volume > 0 && volume <= 50:
                volumeButton.removeClass('highvolume');
                volumeButton.removeClass('muted');
                volumeButton.addClass('lowvolume');
                break;
            case volume > 50:
                volumeButton.removeClass('lowvolume');
                volumeButton.removeClass('muted');
                volumeButton.addClass('highvolume');
                break;
        }

    });

    audioVolumeBar.on('input', function() {
        const volume = parseFloat(audioVolumeBar.val()).toFixed(2);
        audio.volume = volume / 100;
        switch(volume) {
            case '0':
                audioVolumeButton.removeClass('unmute');
                audioVolumeButton.addClass('muted');
                break;
            default:
                audioVolumeButton.removeClass('muted');
                audioVolumeButton.addClass('unmute');
                break;
        }
    });

    videoOverlay.on('click', function() {
        pauseButton.click();
    });
    
    video.on('timeupdate', function() {
        const progress = (video[0].currentTime / video[0].duration) * 100;
        progressBar.val(progress);
    });

    volumeButton.on('click', function() {
        if(video[0].muted && volumeBar.val() == 0) {
            video[0].muted = false;
            volumeBar.val(10);
            return;
        }
        video[0].muted = !video[0].muted;
        volumeButton.toggleClass('muted', video[0].muted);
    });
    audioVolumeButton.on('click', function() {
        if(audio.muted && audioVolumeBar.val() == 0) {
            audio.muted = false;
            audioVolumeBar.val(10);
            return;
        }
        audio.muted = !audio.muted;
        audioVolumeButton.toggleClass('muted', audio.muted);
    });
    function updateVideoProgress(progress) {
        let currentTime = video[0].currentTime;
        // progressBar.width(progress + '%');
        if (progress > 100) {
            alert('La vidéo ne peut pas être au delà de 100%');
            return;
        }
        let videoTime = parseInt((progress / 100) * video[0].duration).toFixed(2);
        video[0].currentTime = videoTime;
        audio.currentTime = videoTime;
    }
    function syncVideoAudio() {
        if(Math.abs(video[0].currentTime - audio.currentTime) > 0.05) {
            console.log('Desynchronisation détectée, resynchronisation en cours...');
            audio.currentTime = video[0].currentTime;
        }
    }

}