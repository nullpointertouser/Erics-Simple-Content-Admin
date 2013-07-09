$(document).ready(function(){
    $('section[data-type="background"]').each(function(){
        var $bgobj = $(this); // assigning the object
     
        $(window).scroll(function() {
	    var speed = $bgobj.data('speed');
	    var imgHeight = $bgobj.data('height');
	    var maxPos = -Math.round(( imgHeight-$(window).height() ) / 2);
	    var yPos = 0;
	    // Special speed setting "auto"
	    if (speed == "auto") {
	      yPos = maxPos / ( ($(document).height() - $(window).height()) / $(window).scrollTop() )
	    } else {
	      yPos = -($(window).scrollTop() / $bgobj.data('speed')); 
	    }
	    
	    // Don't ever scroll past background
	    if (yPos < maxPos) {
	      yPos = maxPos;
	    }
             
            // Put together our final background position
            var coords = '50% '+ yPos + 'px';
 
            // Move the background
            $bgobj.css({ backgroundPosition: coords });
        }); 
    });    
}); 
