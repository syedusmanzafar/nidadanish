<script type="text/javascript">
    (function (_, $) {
        $(document).ready(function () {
            
			
			
			  $(".an-brand-block").click(function(e) {
                var target = $(e.target);

                if (!target.is("a")) {
                    $(this).children("a").click();

                } else {
                    return;
                }
            });
			
			
			
            $(".ty-scroller-list__item").click(function(e){
                var target = $( e.target );
                
                if(e.target != this && !target.is( "a" ) && !target.is("button")  && !target.is("i")  && !target.is("input")) {
                    //return;
                    $(this).children(".ty-scroller-list__img-block").children("a").click();

                }
                else {
                    return;
                    //$(this).children(".ty-scroller-list__img-block").children("a").click();
                }
                    // only continue if the target itself has been clicked
                // this section only processes if the .nav > li itself is clicked.
                //alert("you clicked .nav > li, but not it's children");
            });

            /*
            $.(".ty-scroller-list__item").click(function () {
                $(this).children(".ty-scroller-list__img-block").click();
            }).children().click(function (e) {
                return false;
            });

            */
        });
    }(Tygh, Tygh.$));
</script>