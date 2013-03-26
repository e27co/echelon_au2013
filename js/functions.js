/* Create a closure to maintain scope of the '$' and remain compatible with other frameworks.  */

(function (jQuery) {

    /* @group same as jQuery(document).ready(); event fires when the DOM is ready */
    jQuery(function () {    

       // Carousel
		jQuery('#slides').slides({
			preload: true,
			generatePagination: false,
			pagination: false,
			preloadImage: '<?php echo get_template_directory_uri(); ?>/img/ajax-loader.gif',
			play: 5000,
			pause: 2500,
			hoverPause: true
		});

		jQuery('#quotes2').slides({
			effect: 'fade',
			generatePagination: false,
			pagination: false,
			preload: true,
			preloadImage: '<?php echo get_template_directory_uri(); ?>/img/ajax-loader.gif',
			play: 8000,
			pause: 5000,
			hoverPause: true,
			
		});
		jQuery('#quotes1').slides({
			effect: 'fade',
			generatePagination: false,
			pagination: false,
			preload: true,
			preloadImage: '<?php echo get_template_directory_uri(); ?>/img/ajax-loader.gif',
			play: 8000,
			pause: 5000,
			hoverPause: true,
			
		});
		
		jQuery('#cm_partners').slides({
			effect: 'fade',
			generatePagination: false,
			pagination: false,
			preload: true,
			preloadImage: '<?php echo get_template_directory_uri(); ?>/img/ajax-loader.gif',
			play: 3000,
			pause: 3000,
			hoverPause: true,
			
		});
		
		jQuery('#strategic_partners').slides({
			effect: 'fade',
			generatePagination: false,
			pagination: false,
			preload: true,
			preloadImage: '<?php echo get_template_directory_uri(); ?>/img/ajax-loader.gif',
			play: 3000,
			pause: 3000,
			hoverPause: true,
			
		});
		
		jQuery('#country_sponsors').slides({
			effect: 'fade',
			generatePagination: false,
			pagination: false,
			preload: true,
			preloadImage: '<?php echo get_template_directory_uri(); ?>/img/ajax-loader.gif',
			play: 3000,
			pause: 3000,
			hoverPause: true,
			
		});
       
       
		//Persistent Header   
		jQuery(function() {

		   var clonedHeaderRow;

		   jQuery(".persist-area").each(function() {
			   clonedHeaderRow = jQuery(".persist-header", this);
			   clonedHeaderRow
				 .before(clonedHeaderRow.clone())
				 .css("width", clonedHeaderRow.width())
				 .addClass("floatingHeader");
				 
		   });
		   
		  jQuery(window)
			.scroll(UpdateTableHeaders)
			.trigger("scroll");
		   
		});
    //Persistent eader end
        
	

  



    });
    /* @end jQuery(document).ready(); */


    /* @group bind window load. events doesn't fire until the document is fully loaded (images and all) */
    jQuery(window).bind("load", function () {

    });
    /* @end: bind window load */

})(jQuery);

/* =End closure */