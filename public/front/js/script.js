
        
        jQuery(document).ready(function($) {
                $(".scroll").click(function(event){		
                        event.preventDefault();
                        $('html,body').animate({scrollTop:$(this.hash).offset().top},1000);
                });
        });


        $(document).ready(function() {
        /*
                var defaults = {
                containerID: 'toTop', // fading element id
                containerHoverID: 'toTopHover', // fading element hover id
                scrollSpeed: 1200,
                easingType: 'linear' 
                };
        */

        $().UItoTop({ easingType: 'easeOutQuart' });

            });



        jQuery(document).ready(function($) {	  
          $("#owl-demo").owlCarousel({		
               navigation : true,
               slideSpeed : 300,
               paginationSpeed : 400,
               autoPlay : true,
               singleItem:true
          });
          $("#owl-demo2").owlCarousel({
                       items : 4,
               lazyLoad : true,
               autoPlay : true,
               navigation : true,
               pagination : false
          });
        });
        
        
        
         $('.like-btn').on('click', function() {
            $(this).toggleClass('is-active');
          });
        
                
      $('.check').click(function(){
        $(this).toggleClass('active');
      });



