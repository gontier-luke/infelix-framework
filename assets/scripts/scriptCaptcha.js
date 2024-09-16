document.addEventListener('DOMContentLoaded', (event) => {
    const openPopupBtn = document.getElementById('openPopupBtn');
    const startBtn = document.getElementById('start');
    const popup = document.getElementById('popup');
    const closePopupBtn = document.getElementById('closePopupBtn');

    openPopupBtn.addEventListener('click', () => {
        popup.style.display = 'block';
    });

    startBtn.addEventListener('click', () => {
        popup.style.display = 'block';
    });

    closePopupBtn.addEventListener('click', () => {
        popup.style.display = 'none';
        grecaptcha.reset()
    });

    window.addEventListener('click', (event) => {
        if (event.target === popup) {
            popup.style.display = 'none';
            grecaptcha.reset();
        }
    });
});

function recaptchaCallback(response) {
    if (response) {
        //vérification du captcha via AJAX
        fetch(siteRoot+'verifCaptcha/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `g-recaptcha-response=${response}`,
        })
            .then(response => response.text())
            .then(data => {
                if (parseInt(data) === 1) {
                    document.location.href = siteRoot+"questionnaireDebut"; //a modifier pour la page questionnaire
                } else {
                    alert("Problème lors de la validation du Captcha");
                }
                document.getElementById('popup').style.display = 'none'; // Ferme le popup en cas de succès
                grecaptcha.reset();
            })
            .catch(error => console.error('Error:', error));
    }
}