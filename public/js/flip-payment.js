var $ = jQuery;

document.addEventListener('DOMContentLoaded', function() {
    composite.init();
});


var composite = (function(){
    function init() {
        console.log('flip ready')
    }

    return {
        init : init
    }
})();