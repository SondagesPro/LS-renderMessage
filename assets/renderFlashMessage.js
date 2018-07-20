$(document).on('ready pjax:scriptcomplete',function() {
    if(renderFlashMessage && renderFlashMessage.message) {
        if($('#outerframeContainer').length) {
            $(renderFlashMessage.message).prependTo('#outerframeContainer');
            return;
        }
        if($('.container-fluid').length==1) {
            $(renderFlashMessage.message).prependTo('.container-fluid');
            return;
        }
        if($('article').length==1) {
            $(renderFlashMessage.message).prependTo('article');
            return;
        }
        $(renderFlashMessage.message).appendTo('body');
    }
});
