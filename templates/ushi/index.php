
<style>
    .audio-player {
        width: 100%;
        height: 200px;
        display: flex;
        flex-direction: row;
    }
    .audio-controls {
        width: 10%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
    }
    .soundwave {
      width: 90%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: space-evenly;
      gap: 2px;
      overflow: hidden;
    }
    .bar {
      width: 6px;
      background: #00f5d4;
      transition: height 0.1s ease;
    }
    audio {
      display: none;
    }
    .audio-control_img{
        max-width: 100%;
        height: auto;
        cursor: pointer;
    }
  </style>
<div class="container">
    <h1>Hello</h1>
</div>
<div class="container" id="three-background">
    <div id="chaise-container" data-model="/model/Def.chaise4.stl" data-x="-1.66" data-y="-4.18"></div>
    <div id="ushi-container">
        <audio id="ushi-audio" src="/audio/assassin_theme.mp3"></audio>
        <div class="audio-player">
            <div class="soundwave" id="soundwave"></div>
            <div class="audio-controls">
                <button id="ushi-button" class="ushi-button">
                    <span class="ushi-button-text">Pause/Play Bouton</span>
                </button>
                <img id="replay-button" src="/image/reset.png" class="replay-button audio-control_img" alt="reset audio" title="Recommencer le son"/>
            </div>
        </div>

        <div id="texte-container">
            <p class="citation">“Délire d’oiseaux n’intéresse pas l’arbre”</p>
            <p class="auteur">
                Gératrd
            </p>
        </div>
    </div>

</div>