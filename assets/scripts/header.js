$(document).ready( function () {

    let header = $('.header')[0];

    let winTop = $(window).scrollTop();
    if (winTop > 1) {
        headerToBubble(header);
    }

    $(window).scroll(function () {
        let winTop = $(window).scrollTop();
        if (winTop > 1) {
            headerToBubble(header);
        }
        if (winTop < 1) {
            headerToWide(header);
        }
    });

});

function headerToBubble(header){
    header.classList.add('bubble')
}

function headerToWide(header){
    header.classList.remove('bubble')
}