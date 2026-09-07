$(document).ready(function() {
    // alert("Coucou, c'est le pioupiou !");
    const pioupiou = $('#pioupiouGame').find('.pioupiou');
    const boxHeight = $('#pioupiouGame').height();
    const boxWidth = $('#pioupiouGame').width();
    const pioupiouHeight = pioupiou.height();
    const pioupiouWidth = pioupiou.width();
    const pipeImg = $('#pioupiouGame').data('img-pipe');
    const upImg = pioupiou.data('img-up');
    const downImg = pioupiou.data('img-down');
    const bgImg = $('#pioupiouGame').data('img-bg');

    let positionY = boxHeight/2 - pioupiouHeight/2;
    let maxVelocityY = 15.5;
    let minVelocityY = -6.5;
    let gravity = 1.5;
    let velocityY = -gravity;
    let jumpStrength = 20.5;
    let isGameOver = false;
    let score = 0;
    let timeFlap = 0;
    let canFlap = true;
    let maxPipes = 5;
    let timeToNextPipe = 180; // Frames until the next pipe appears
    let gapNextPipe = 135; // Distance between pipes
    let idPipe = 0;
    let pipes = [];
    
    gameInterval = setInterval(gameLoop, 1000/60); // 60fps
    pioupiou.css('background-image', 'url(' + upImg + ')');
    function gameLoop() {
        if (isGameOver){
            $(document).trigger('gameOver');
          return;  
        } 

        if(velocityY > minVelocityY) {
            velocityY -= gravity;
        }
        positionY += velocityY;
        
        if ( positionY + pioupiouHeight >= boxHeight || positionY <= 0) {
            positionY = boxHeight/2 - pioupiouHeight/2;
            isGameOver = true;
            return;
        }
        if(timeFlap > 0){
            timeFlap--;
        }
        if(timeFlap == 1){
            pioupiou.css('background-image', 'url(' + upImg + ')');
        }
        pioupiou.css('bottom', positionY + 'px');
        timeToNextPipe--;
        if(timeToNextPipe == 0 && pipes.length < maxPipes) {
            pipes.push(Pipe.getRandomPipe(idPipe, pioupiouHeight, boxWidth, boxHeight, boxWidth * 0.08, pipeImg));
            idPipe++;
            timeToNextPipe = gapNextPipe; // Réinitialiser le compteur pour la prochaine pipe
            gapNextPipe--; // Réduire l'écart pour augmenter la difficulté à mesure que le jeu progresse
        }
        pipes.forEach(pipe => pipe.move(score, pioupiouHeight, pioupiouWidth, positionY));
        bgMove('#pioupiouGame', score);
    }

    $(document).on('keypress', function(e) {
        e.preventDefault();
        if (e.keyCode === 32) { // Spacebar
            if (!isGameOver && canFlap) {
                velocityY += jumpStrength;
                if(velocityY > maxVelocityY) {
                    velocityY = maxVelocityY;
                }
                pioupiou.css('background-image', 'url(' + downImg + ')');
                timeFlap = 15; // Durée de l'animation du flap en frame
                canFlap = false;
            }
        }
    });

    $(document).on('keyup', function(e) {
        e.preventDefault();
        if (e.keyCode === 32) { // Spacebar
            canFlap = true;
        }
    });

    $(document).on('gameOver', function() {
        alert("Game Over !");
        clearInterval(gameInterval);
    });

    $(document).on('scoreUp', function() {
        score++;
        $('#score').text(score);
    });

    $(document).on('destroyPipes', function() {
        pipes.shift(); 
    });
});

class Pipe {
    constructor(x, height, width, gap, score, boxHeight, boxWidth, image) {
        this.x = x;
        this.height = height;
        this.width = width;
        this.gap = gap;
        this.idPipe = score;
        this.boxHeight = boxHeight;
        this.boxWidth = boxWidth;
        this.image = image;
        this.scored = false;
        this.display();
    }

    static getRandomPipe(score, minGap, boxWidth, boxHeight, pipeWidth, image) {
        let gap = Math.max(minGap, 480 - score * 5.5); // Réduire l'écart à mesure que le score augmente
        let height = Math.floor(Math.random() * (boxHeight - gap - 150)) + 75; // Assurer une hauteur minimale pour les pipes
        return new Pipe(boxWidth, height, pipeWidth, gap, score, boxHeight, boxWidth, image);
    }

    move(currentScore, heightPioupiou, widthPioupiou, positionYPioupiou) {
        let vitesse = 5 + currentScore * 0.1; // Augmenter la vitesse à mesure que le score augmente
        this.x -= vitesse;
        this.htmlElementBottom.css('left', this.x + 'px');
        this.htmlElementTop.css('left', this.x + 'px');
        if(this.x + this.width < 0) {
            this.__destroy();
            return;
        }
        // Vérification de collision
        if (this.x <= (this.boxWidth/2 + widthPioupiou/2) && this.x+this.width >= (this.boxWidth/2 - widthPioupiou/2)) {
            if (positionYPioupiou <= this.height || positionYPioupiou + heightPioupiou >= this.height + this.gap) {
                $(document).trigger('gameOver');
            }
            if (this.x + this.width/2 <= (this.boxWidth/2) && !this.scored) {
                $(document).trigger('scoreUp');
                this.scored = true;
            }
        }
    }

    display() {
        $('#pioupiouGame').append('<div class="pipe" id="pipe' + this.idPipe + '_bottom" style="position: absolute; left: ' + this.x + 'px;  width: ' + this.width + 'px;"><img id="pipe' + this.idPipe + '_bottom_img" src="' + this.image + '" alt="Pipe" style="max-width: 100%; height: auto;"></div>');
        this.htmlElementBottom = $('#pipe' + this.idPipe + '_bottom');
        $('#pioupiouGame').append('<div class="pipe" id="pipe' + this.idPipe + '_top" style="position: absolute; left: ' + this.x + 'px; bottom: ' + (this.height + this.gap) + 'px; width: ' + this.width + 'px;"><img id="pipe' + this.idPipe + '_top_img" src="' + this.image + '" alt="Pipe" style="max-width: 100%; height: auto; transform: rotate(180deg);"></div>');
        this.htmlElementTop = $('#pipe' + this.idPipe + '_top');
        let pipe = this;
        $('img').on('load', function() {
            let imageHeight = $(this).height();
            pipe.htmlElementBottom.height(imageHeight);
            pipe.htmlElementBottom.css('bottom', pipe.height - imageHeight + 'px');
            pipe.htmlElementTop.height(imageHeight);
            pipe.htmlElementTop.css('bottom', pipe.height + pipe.gap + 'px');
        });
    }

    __destroy() {
        this.htmlElementBottom.remove();
        this.htmlElementTop.remove();
        $(document).trigger('destroyPipes');
    }
}

function bgMove(selector, score) {
    let vitesse = (5 + score * 0.1) * 0.5; // La vitesse de déplacement du fond, ajustée pour être plus lente que les pipes
    $(selector).css('background-position-x', (parseFloat($(selector).css('background-position-x')) - vitesse) + 'px');
}