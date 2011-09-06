Custom HelpSpot Widget 1.0
==========================


Description
-----------
[HelpSpot](http://www.helpspot.com "HelpSpot") is an excellent, web-based Help 
Desk/Support Center solution. While they are constantly working to improve their 
software, one of the things they have not gotten around to updating (yet) is 
their "widgets" (the contact forms that you add to your page, which integrate  
into your HelpSpot account)
 
That's where this plugin, *HelpSpot-Custom-Widget*, comes in. It basically clones 
the default HelpSpot widget, but gives you **much** more control over the forms, 
as well as many more customization options.


What's New in This Version?
---------------------------
This is the first "official" (non-beta) release of this plugin, so there were 
several improvements made.

* The most notable improvement was that the forms are now loaded inside of an 
AJAX "ligthbox" instead of an iFrame. Let's face it... iFrames are so 1999. 
Plus, by loading the forms in a lightbox, things just work better: The forms 
automatically resize better, we no longer have to include our stylesheets 
and javascript libraries multiple times, and more!
* I aded the ability to use and create themes; making it even easier to 
customize how your forms look. There are four [very basic] themes, not 
counting the default theme, included.
* Even more customization options were added
	

Requirements
------------
There are only two things required to use this plugin:

* Obviously, you need an account with [HelpSpot](http://www.helpspot.com "HelpSpot")
* Secondly, this plugin requires jQuery to work. If you're already using jQuery 
on your site, then great! If not, you will need to include it for the plugin to 
work (there are instructions in the documentation on how to do this.)

Installation & Basic Usage
--------------------------
    <!-- include our stylesheet -->
    <link href="widget.css" rel="stylesheet" type="text/css" media="screen" />

    <!-- include jQuery -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
    
    <!-- include widget.js -->
    <script type="text/javascript" src="widget.js"></script>
    
    <!--setup and initialize the widget plugin -->
    <script type="text/javascript">
    $(function(){
		
        // you could probably bind this to just about any element...
        $('html').HelpSpotWidget({
				
            // the base url to where HelpSpot is installed (***include trailing slash***)
            host: 'http://support.yourdomain.com/',
				
            // the base url to where the widget plugin is located (***include trailing slash***)
            // only needs to be set if the widget files are outside of the current directory
            base: ''
        });
    });	
    </script>

Documentation
-------------
You can find the plugin documentation in the docs/ directory. You can also find the documentation, as well as a live sample, at **[http://labs.mikeeverhart.net/helpspot](http://labs.mikeeverhart.net/helpspot "Documentation and a Live Sample")**

Plugin Author
--------------
This plugin was created by Mike Everhart. You can find me around the web at:

* My Personal Site: [MikeEverhart.net](http://www.mikeeverhart.net "My personal site")
* My Side Project: [plasticbrain.net](http://www.plasticbrain.net "My part time project")
* My Social Life: [Facebook](https://www.facebook.com/plasticbrain "Friend me on Facebook!")

Reporting Bugs
--------------
So you found a bug? Hey, nobody's perfect, right?

Please use [GitHub's Issue Tracker](https://github.com/plasticbrain/HelpSpot-Custom-Widget-1.0/issues/new "Submit a Bug") to submit a bug report.

Suggestions & Improvements
--------------------------
Got an idea to make this plugin even better? Well, lucky for you, you have two choices!

* Email your suggestions to **[feedback@plasticbrain.net](mailto:feedback@plasticbrain.net "Submit Feedback")**
* Or, you can even fork this plugin and create your own version!

Credits
-------
The latest version of this plugin uses the excellent [jQuery Validation plugin](http://bassistance.de/jquery-plugins/jquery-plugin-validation/ "jQuery Validation Plugin")