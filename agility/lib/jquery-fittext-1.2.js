/**
 * adjust font-size style based on container width
 */

(function( $ ){

  $.fn.fitText = function( scl, options ) {

    // Setup options
    var scale = scl || 0.1; // default is 10% of parent's size
    var settings = $.extend({
          'minFontSize' : Number.NEGATIVE_INFINITY,
          'maxFontSize' : Number.POSITIVE_INFINITY
        }, options);

    return this.each(function(){

      var $this = $(this);      // Store the object

      // Resizer() resizes items by mean of scaling from current width
      var resizer = function () {
    	  var min=parseFloat(settings.minFontSize);
    	  var max=parseFloat(settings.maxFontSize);
    	  var cur= $this.parent().height() * scale;
    	  $this.css('font-size', Math.max(Math.min(cur, max), min)+'px');
      };

      // Call once to set.
      resizer();

      // Call on resize. Opera debounces their resize by default.
      $(window).on('resize.fittext orientationchange.fittext', resizer);

    });

  };

})( jQuery );
