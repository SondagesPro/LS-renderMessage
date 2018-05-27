jQuery(function($) {
    if(renderFlasMessage && renderFlasMessage.message) {
        if($('#outerframeContainer').length) {
            $(renderFlasMessage.message).prependTo('#outerframeContainer');
            return;
        }
        if($('#dynamicReloadContainer').length) {
            $(renderFlasMessage.message).prependTo('#dynamicReloadContainer');
            return;
        }
        if($('article').length==1) {
            $(renderFlasMessage.message).prependTo('article');
            return;
        }
        $(renderFlasMessage.message).appendTo('body');
    }
});
