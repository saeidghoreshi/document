<!DOCTYPE html>
<html>
<head>
 <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />


	<title><?=PRODUCT_NAME?> <?=VERSION?></title>
	<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<!--google Maps Apis  -->
    <!--V2-->
    <!--script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?=GOOGLEAPIKEY?>" type="text/javascript"></script-->  
    <!--script src='http://www.google.com/jsapi?key=<?=GOOGLEAPIKEY?>'></script-->
    
    <!--V3-->
    <script src="http://maps.googleapis.com/maps/api/js?key=<?=GOOGLEAPIKEY?>&sensor=true" ></script>
    
    <!--ABQIAAAAhBhxrOjhRdye1GSCNC0nzhTdfI-5C_lp0IJP5qTDBY2oNKHwQBQDxIGmTARUzKcc7t7CFZe-9K2cig-->
    
	<!-- YUI 2.8 -->
	<script type="text/javascript" src="http://endeavor.servilliansolutionsinc.com/yui/build/yuiloader/yuiloader-min.js"></script>
	
	<!-- Ext JS 4.0 -->
	<link rel="stylesheet" type="text/css" href="http://endeavor.servilliansolutionsinc.com/ext-4.0.2a/resources/css/ext-all.css" />
    <script type="text/javascript" src="http://endeavor.servilliansolutionsinc.com/ext-4.0.2a/ext-all-debug.js"></script>
    
        
    <!--JQUERY BASE-->
    <script type="text/javascript" src="http://springrainbow.ca/assets/library/jquery/1.3.2-jquery.min.js"></script>
    <script type="text/javascript" src="http://springrainbow.ca/assets/library/jquery/jquery-ui-1.8.18.custom/js/jquery-ui-1.8.18.custom.min.js"></script>                                    
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>                                 
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
    
    <!-- jQuery 1.7.1-->
    <script src="assets/js/plugins/jquery171min.js"></script>
    
    <!-- Spectrum 2.0 -->
    <link rel="stylesheet" type="text/css" href="/assets/css/spectrum2.css" />
    <link rel="stylesheet" type="text/css" href="/assets/css/menubar.php" />
    <link rel="stylesheet" type="text/css" href="/index.php/endeavor/get_css_file" />
    <script type="text/javascript" src="/assets/js/components/global/controller.js"></script><!-- was class.spectrum.js-->
    
    <!--  Shadowbox -->
    <link rel="stylesheet" type="text/css" href="/assets/js/plugins/shadowbox/shadowbox.css">
	<script type="text/javascript" src="/assets/js/plugins/shadowbox/shadowbox.js"></script>
	<script type="text/javascript">
		//http://www.shadowbox-js.com/
		Shadowbox.init(
		{
			skipSetup: false
			, language: 'en'
     		, players:  ['img', 'html', 'iframe', 'qt', 'wmp', 'swf', 'flv']
		}
		);
	</script>
    
    
</head>
<body>
<!-- Google Ad-Sense -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-21834755-1']);
  _gaq.push(['_setDomainName', 'none']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<input id="GOOGLEAPIKEY" value="<?=GOOGLEAPIKEY?>" type="hidden"/>
<input type="hidden" id="token" value="T"/>
<input type="hidden" id="ip" value="<?=$_SERVER['REMOTE_ADDR']?>"/>
<script src="http://yui.yahooapis.com/2.9.0/build/yahoo/yahoo-min.js"></script>
<script src="http://yui.yahooapis.com/2.9.0/build/event/event-min.js"></script>
<script src="http://yui.yahooapis.com/2.9.0/build/connection/connection_core-min.js"></script>
<script src="http://yui.yahooapis.com/2.9.0/build/connection/connection-min.js"></script>
 

   
   <p id="userName"></p> <?// what is this for? is it still in use or @depreciated  ?>
   
   
   
   
   
   
<script type="text/javascript" charset="utf-8">
	var is_ssl = ("https:" == document.location.protocol);
	var asset_host = is_ssl ? "https://s3.amazonaws.com/getsatisfaction.com/" : "http://s3.amazonaws.com/getsatisfaction.com/";

	document.write(unescape("%3Cscript src='" + asset_host + "javascripts/feedback-v2.js' type='text/javascript'%3E%3C/script%3E"));
</script>

<script type="text/javascript" charset="utf-8">
	var feedback_widget_options = {};

	feedback_widget_options.display = "overlay"; 
	feedback_widget_options.company = "playerspectrum";
	//valid options for the placement tag include 'right' 'left' 'bottom' 'hidden'
	feedback_widget_options.placement = "bottom";
	feedback_widget_options.color = "#003366";
	feedback_widget_options.style = "idea";
	var feedback_widget = new GSFN.feedback_widget(feedback_widget_options);

	//this generates an anchor tag with id #fdbk_tab 
	// css is added to this widget by id, in the file /assets/css/spectrum2.css
</script>   
   
</body>
</html>