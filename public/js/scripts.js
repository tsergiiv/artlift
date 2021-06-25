$(document).ready(function(){
    let defInput = $('.input-default');
    defInput.each(function() {
        checkForInput(this);
    });
    defInput.on('change keyup', function() {
        checkForInput(this);
    });
    $('.form-show').click(function(){
        let el = $(this).parent().find('input');
        if (el.attr('type') == 'password') {
            el.attr('type', 'text');
            $(this).parent().addClass('show-password');
        } else {
            el.attr('type', 'password');
            $(this).parent().removeClass('show-password');
        }
    });

    $('.dropdown-control').click(function(){
        $('.dropdown-control').removeClass('active');
        $(this).addClass('active');
    });
    $(document).on('click', function(e) {
        if (!$(e.target).is('.dropdown-control, .dropdown-control *')) {
            $('.dropdown-control').removeClass('active');
        }
    });
    $('body').on('click', '.backdrop', function(){
        $('body').removeClass('modal-open');
        $(document).find('.modal').fadeOut(300);
        $(this).fadeOut(300, function(){$(this).remove();});
    });
    $('body').on('click', '.modal-close', function(e){
        $('body').removeClass('modal-open');
        $('.backdrop').fadeOut(300);
        $(this).parents('.modal').fadeOut(300);
        e.preventDefault();
    });
    $('body').on('click', '.modal-toggle', function(e){
        let modal = $(this).attr('data-toggle');
        let link = $(this).attr("href");
        $(document).find('#cancel').attr('href',link);
        $(document).find('#cancel2').attr('href',link);

        $(document).find('#' + modal).fadeIn(300);
        $('body').addClass('modal-open');
        $('<div class="backdrop"></div>').hide().appendTo('body').fadeIn(300);
        e.preventDefault();
    });
    $('body').on('click', '.column-flow__title', function(){
        $(this).parent().toggleClass('active');
        $(this).next().animate({height: 'toggle'}, 300);
    });
});
function checkForInput(element) {
    const $label = $(element).siblings('label');
    if ($(element).val().length > 0) {
        $label.addClass('input-focused');
    } else {
        $label.removeClass('input-focused');
    }
}
