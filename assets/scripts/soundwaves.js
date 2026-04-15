$(document).ready(function () {
const barCount = 26;
    var degree = 13; // Degré du polynôme
    let coeffs = Array(degree + 1).fill(0).map(() => Math.random() * 2 - 1); // Coefficients [-1,1]

    // Générer les barres
    for (let i = 0; i < barCount; i++) {
        $('#soundwave').append('<div class="bar"></div>');
    }

    function evalPolynomial(x, coeffs) {
        return coeffs.reduce((acc, coeff, i) => acc + coeff * Math.pow(x, i), 0);
    }

    function animateBars() {
        $('.bar').each(function(index) {
        const x = index / (barCount - 1) * 2 - 1; // x entre [-1, 1]
        let y = evalPolynomial(x, coeffs);
        y = Math.max(0, Math.min(1, (y + 1) / 2)); // Normalise entre [0,1]
        const height = y * 95 + 5; // entre 10 et 90%
        $(this).css('height', `${height}%`);
        });
    }

    function updateCoefficients() {
        // Ajoute une petite variation à chaque coefficient
        coeffs = coeffs.map(()  => Math.random() * 2 - 1);
        animateBars();
    }

    let soundInterval = setInterval(updateCoefficients, 100); // Met à jour les coefficients 60 fois par seconde

    let soundElement = $('#ushi-audio');
    let sound = soundElement[0]; // Récupérer l'élément audio
    console.log(sound);
    $('#ushi-button').on('click', function() {
        if (soundInterval) {
            stopSound();
            return;
        }
        soundInterval = setInterval(updateCoefficients, 100);
        sound.play();
    });

    $('#replay-button').on('click', function() {
        sound.currentTime = 0; // Remettre le son au début
        sound.play(); // Rejouer le son
        if (!soundInterval) {
            soundInterval = setInterval(updateCoefficients, 100);
        }
    });

    soundElement.on('ended', function() {
        stopSound();
    });

    function stopSound() {
        clearInterval(soundInterval);
        soundInterval = null;
        $('.bar').css('height', '1%');
        sound.pause();
    }

});