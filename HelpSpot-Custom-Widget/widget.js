//------------------------------------------------------------------------------
// Custom HelpSpot Widget
// (c) 2011 Mike Everhart // Eagle Web Assets, Inc. // mikeeverhart.net
//------------------------------------------------------------------------------
//
// Changelog
//------------------------------------------------------------------------------
// 	2011-08-27 (version 0.1)
//		- Initial Version
//
//	2011-08-29 (version 0.2)
//		- Added a better function to center the elements on screen
//	
//	2011-09-04 (version 1.0)
//		-	Replaced the use of iFrames with a lightbox to load the form
//		-	Added the ability to create and use themes 
//		-	Included the jQuery AJAX Validation plugin to validate form fields
//		- Added more customization options (overlay opacity, text labels,etc)
//
//------------------------------------------------------------------------------

(function($){  
	
	$.fn.HelpSpotWidget = function(options) {  
  	
  	//--------------------------------------------------------------------------
		// Default Options
		//--------------------------------------------------------------------------
		var defaults = {  
			
			// URLs and Paths
			base: '', // The base location of where these files are installed -- include a traling slash!
			host: '', // The base location of your HelpSpot installation -- include a traling slash!
			ajax: 'ajax.php', // the file that will handle all the AJAX functions for the form
			form: 'form.php', // what PHP file that contains the form to use
			faqs: '', // The url (not including the HOST) to your FAQs section (if this applies to you)
						
			// Customization
			overlay_opacity: 40, // in % form, how much we should fade in the overlay
			tab_type: 'support', // which tab image to show (support, feedback, help, questions)
			tab_color: '#000000', // what color to use for the tab's background
			tab_top: '30%', // the CSS "top" position of the tab
			tab_alignment: 'right', // where to position the tab on the page
			close_on_overlay_click: true, // whether or not to close the form/lighbox if the overlay is clicked
			width: '500px', // the width of the "popup" form
			height: '575px', // the height of the "popup" form
			ajax_validation: true, // whether or not to use jquery AJAX Validation on the form
			
			// Text Labels
			submit_button_text: 'Submit', // the text to use on the form submit button
			submitting_text: 'Please wait while we submit your request...', // the text to use when the user submits the form
			success_headline_text: 'Your Message Has Been Received!',
			success_body_text: 'We will review your submission and someone will reply to you soon.',
			
			// form field values
			default_first_name: '',
			default_last_name: '',
			default_email: '',
			default_phone: '',
			default_department: '', // the ID of department/category that will be automatically selected
			
			// CSS ID's
			overlay_id: 'custom_widget_overlay',
			widget_content_id : 'custom_widget_content',
			close_button_id: 'custom_widget_close',
			form_id: 'custom_widget_form',
			iframe_id: 'custom_widget_iframe',
			tab_id: 'custom_widget_tab',
			submit_button_id: 'custom_widget_submit',
						
			// Debug mode
			debug: false, // doesn't do anything at this point
			demo: false,  // doesn't do anything at this point
			
			// Callback functions
			onLoad: function(){},
			onShow: function(){},
			onHide: function(){},
			onError: function(){},
			onSuccess: function(){}
			
		};  
		var options = $.extend(defaults, options);  
		
		var overlay;
		var tab;
		var content;
		
		var overlay_opacity = (options.overlay_opacity/100);
		
		//--------------------------------------------------------------------------
		// function to return debug messages
		//--------------------------------------------------------------------------
		function debug(msg) {
    	if(options.debug && window.console){
      	console.log(msg);
      }
		}
		
		
		//--------------------------------------------------------------------------
		// Setup the plugin (Add any HTML elements that we need, etc)
		//--------------------------------------------------------------------------
		function setup() {
			
			// setup all of the HTML we need
			var html = '';
			
			// widget container
			html += "<div id='" + options.widget_content_id + "' style='width:" + options.width + "; height:" + options.height + ";'>\n";
					
				// close button
				html += "<a href='#' id='" + options.close_button_id + "'>Close</a>\n";
				
				// lightbox that holds the form
				html += "<div id='" + options.iframe_id + "' style='display:none;'></div>\n";
				
			
			html += "</div>\n";
			html += "<!-- end custom widget content -->\n\n";
			
			// the tab
			html += "<div id='" + options.tab_id + "' class='" + options.tab_type + " " + options.tab_alignment + "' style='display:none; top: " + options.tab_top + "; background-color:" + options.tab_color + ";'></div>\n"
			
			// the overlay 
			html += "<div id='" + options.overlay_id + "' style=''></div>\n"; 
			html += "<!-- end custom widget overlay -->\n\n";
			
			// append all of our HTML to the <body> element
			$('body').append($(html));
			
			// "cache" the DOM elements for future use			
			overlay = $('#' + options.overlay_id);
			content = $('#' + options.widget_content_id);
			tab = $('#' + options.tab_id);
			frame = $('#' + options.iframe_id);
			
			// Show the tab
			tab.fadeIn(750);
			
			// lastly, call the callback function
			options.onLoad.call(this);
			return true;
		}
		
		//--------------------------------------------------------------------------
		// Show the form
		//--------------------------------------------------------------------------
		function showForm() {
			
			// show the overlay
			overlay.css({opacity:0}).show().animate({opacity: overlay_opacity}, 300).addClass('showing');
			
			// show the content holder
			content.css({opacity:0}).show().cwCenter().animate({opacity: 1}, 300).addClass('showing loading');
			
			// show the content
			frame.css({opacity:0}).show().html('<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />').animate({opacity: 1}, 300).addClass('showing');

			// we need to use AJAX to fetch the form
			var form_url = options.base + options.form;
			var params = makeQueryString(options) + '&rand=' + Math.floor(Math.random()*1000);
			// alert(form_url + params);
			$.ajax({
				url: form_url,
				data: params,
				success: function(data) {
					frame.html(data);
					content.css({'height':'auto'}).removeClass('loading').cwCenter();
				},
				error: function() {
					content.removeClass('loading');
					frame.html('We\'re sorry, but the requested form could not be loaded!');
				}
				
			});
			
			// call the callback function
			options.onShow.call(this);
			return true;
		}
		
		//--------------------------------------------------------------------------
		// Hide the form
		//--------------------------------------------------------------------------
		function hideForm() {
			
			// hide the content
			content.animate({opacity: 0}, 300, function(){
				$(this).removeClass('showing').hide();
			});
			
			// hide the overlay
			overlay.animate({opacity: 0}, 300, function(){
				$(this).removeClass('showing').hide();
			});
			
			// call the callback function
			options.onHide.call(this);
			return true;
		}
		
		//--------------------------------------------------------------------------
		// Funtion to create a query string from an array
		//--------------------------------------------------------------------------
		function makeQueryString(arr) {
    	var qs = "";
    	for( var q in arr ) {
    		qs += '&' + encodeURIComponent(q) + "=" + encodeURIComponent(arr[q]);
    	}
    	return qs.substring(1);
    }
		
		//--------------------------------------------------------------------------
		// Finally, we start the plugin 
		//--------------------------------------------------------------------------
		return this.each(function() {  
			
			obj = $(this); 
			
			setup();
			
			// When the tab is clicked show the form
			$('#' + options.tab_id).bind('click', function(){
				showForm();
				return false;
			});
			
			// Close the form when the close button is clicked
			$('#' + options.close_button_id).bind('click', function(){
				hideForm();
				return false;
			});
			
			if( options.close_on_overlay_click == true ) {
				// Close the form if the user clicks on the overlay
				overlay.bind('click', function(){
					hideForm();
				return false;
				});	
			}
			
		});  
		
		
	};  
})(jQuery);  

//---------------------------------------------------------------------------
// Center Elements
//---------------------------------------------------------------------------
(function($) {
	$.fn.cwCenter = function() {
		var el;
		return this.each(function(index) {
			if(index == 0) {
				el = $(this);
    		el.css("position","absolute");
    		el.css("top", (($(window).height() - el.outerHeight()) / 2) + $(window).scrollTop() + "px");
    		el.css("left", (($(window).width() - el.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    	}
   	 });
  	};
})(jQuery);