hljs.initHighlightingOnLoad();
$(function() {
    document.addEventListener('play', function(e){
        $('audio, video').each(function(obj) {
            if(this != e.target){
                this.pause();
            }
        });
    }, true);

    document.addEventListener('ended', function(e) {
        flag_play_next = false;
        objs = $('audio');
        for (var k = 0; k < objs.length; k++) {
            obj = objs[k];
            if (flag_play_next && obj) {
                obj.play();
                break;
            }
            if (obj == e.target){
                flag_play_next = true;
            }
        }
    }, true);

    if (window.location.hash != '') {
        setPointer(window.location.hash.substring(1));
    }

    $(".pointer").each(function(){
        $(this).click(function(){
            setPointer(this.getAttribute("href").substring(1), false);
        });
    });

});


function setPointer(pointer, scroll = true) {
        $(".highlighted").each(function(){
            $(this).removeClass('highlighted');
        });
        $("*").find("[file_id='" + pointer + "']").each(function(){
            if (scroll) {
                window.scrollTo( 0, this.offsetTop);
            }
            $(this).addClass('highlighted');
        });

}