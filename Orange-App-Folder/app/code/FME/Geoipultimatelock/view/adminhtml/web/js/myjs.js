
function RunWithJQuerySupport(success, src)
{
    if (typeof jQuery == 'undefined') {
        function getScript(success1)
        {
            var head = document.getElementsByTagName('head')[0];
            var script = document.createElement('script');
            script.src = src;
            done = false;
            script.onload = script.onreadystatechange = function () {
                if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
                    done = true;
                    if (typeof (success1) != 'undefined' && success1 != null) {
                        success1(); }

                    script.onload = script.onreadystatechange = null;
                };
            };
            head.appendChild(script);
        };
 
        getScript(
            function () {
            if (typeof jQuery == 'undefined') {
                // Super failsafe - still somehow failed...
            } else {
               //jQuery.noConflict();
                if (typeof (success) != 'undefined' && success != null) {
                    success(); }
            }
            }
        );
    } else {
        if (typeof (success) != 'undefined' && success != null) {
            success(); }
    };
}

//Read more: http://developmentsimplyput.blogspot.com/2012/12/how-make-sure-jquery-is-loaded-and-only.html#ixzz2szMWqjkV
