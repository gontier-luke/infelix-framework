$(document).ready( function () {

    let headerMenu = $('.header-menu');
    let header = $('header');
    let menuclosebutton = $('.menucross');
    let overlay = $('.menu-overlay');

    menuclosebutton.on('click', function(){
        toggleHeaderMenu(headerMenu, header);
    });

    overlay.on('click', function(){
        toggleHeaderMenu(headerMenu, header);
    });

    $(document).on('click', function(event) {
        if (header.is(event.target) && !menuclosebutton.is(event.target) && !overlay.is(event.target)) {
            toggleHeaderMenu(headerMenu, header);
        }
    });

});

function toggleHeaderMenu(headerMenu, header){
    headerMenu.toggleClass('closed');

    setTimeout(function() {
        header.toggleClass('closed');
    }, 150);
}