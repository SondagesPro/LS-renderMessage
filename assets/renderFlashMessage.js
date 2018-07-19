jQuery(function($) {
    if(renderFlashMessage && renderFlashMessage.message) {
        if($('#outerframeContainer').length) {
            $(renderFlasMessage.message).prependTo('#outerframeContainer');
            return;
        }
        if($('.container-fluid').length==1) {
            $(renderFlasMessage.message).prependTo('.container-fluid');
            return;
        }
        if($('article').length==1) {
            $(renderFlasMessage.message).prependTo('article');
            return;
        }
        $(renderFlasMessage.message).appendTo('body');
    }
});
