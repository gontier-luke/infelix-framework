$(document).ready(function() {
    $('.copy-js').click(function() {
        let copyText = $(this).attr('data-copy');
        let $temp = $("<input>");
        $("body").append($temp);
        $temp.val(copyText).select();
        document.execCommand("copy");
        $temp.remove();
        $('.modal-copy .extra-message').html($(this).data('message'));
        $('.modal-copy').show();
    });

    $('.modal .modal-close').click(function() {
        $(this).parents('.modal').hide();
    });
});