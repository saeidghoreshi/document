<html>
<head>
	<title>Spectrum | Online Play Management3</title>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" /> 
	<link rel="stylesheet" type="text/css" href="/assets/css/console.css"/>
	<link rel="stylesheet" type="text/css" href="/assets/css/panel.css"/>
	<link rel="stylesheet" type="text/css" href="/assets/css/window.css"/>
	<link rel="stylesheet" type="text/css" href="/assets/css/form.css"/>
	<link rel="stylesheet" type="text/css" href="/index.php/endeavor/get_css_file"/>
    <link rel="stylesheet" type="text/css" href="/assets/css/folders/tree.css"> 
    <link rel="stylesheet" type="text/css" href="http://endeavor.servilliansolutionsinc.com/ext-4.0.0/resources/css/ext-all.css" />
	<!-- http://endeavor.servilliansolutionsinc.com/ext-4.0.0/ -->
    
    
    <!--EXT -->
    <link rel="stylesheet" type="text/css" href="http://endeavor.servilliansolutionsinc.com/ext-4.0.0/resources/css/ext-all.css"/>
    <script type="text/javascript" src="http://endeavor.servilliansolutionsinc.com/ext-4.0.0/ext-all-debug.js"></script>
    <!--EXT -->    
    
    <!--google Maps Apis  -->
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?=GOOGLEAPIKEY?>" type="text/javascript"></script>  
    <script src='http://www.google.com/jsapi?key=<?=GOOGLEAPIKEY?>'></script>
    <!--ABQIAAAAhBhxrOjhRdye1GSCNC0nzhTdfI-5C_lp0IJP5qTDBY2oNKHwQBQDxIGmTARUzKcc7t7CFZe-9K2cig-->
    <!-- Yui 2.x -->
    <script type="text/javascript" src="http://yui.yahooapis.com/combo?2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js&2.8.2r1/build/container/container-min.js"></script> 
	<script type="text/javascript" src="http://endeavor.servilliansolutionsinc.com/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
	<script type="text/javascript" src="http://endeavor.servilliansolutionsinc.com/yui/build/yuiloader/yuiloader-debug.js"></script>
	<script type="text/javascript" src="http://endeavor.servilliansolutionsinc.com/yui/build/event/event-min.js"></script>
	<script type="text/javascript" src="http://endeavor.servilliansolutionsinc.com/yui/build/element/element-min.js"></script>
	<!-- Ext JS -->
	<script type="text/javascript" src="http://endeavor.servilliansolutionsinc.com/ext-4.0.0/adapter/ext/ext-base.js"></script>
    <script type="text/javascript" src="http://endeavor.servilliansolutionsinc.com/ext-4.0.0/ext-all-debug.js"></script>
	<!-- Spectrum -->
	<script type="text/javascript" src="/assets/js/class.application.js"></script>
    <!-- Ext JS -->
	<link rel="stylesheet" type="text/css" href="http://endeavor.servilliansolutionsinc.com/ext-4.0.0/resources/css/ext-all.css" />

    <script type="text/javascript" src="http://endeavor.servilliansolutionsinc.com/ext-4.0.0/ext-all-debug.js"></script>
    
</head>
<body class="spectrum-main-body"><?# was <body class="yui-skin-sam">?>

<input id="GOOGLEAPIKEY" value="<?=GOOGLEAPIKEY?>" type="hidden"/>

<div id="endeavor">
	<table id="endeavor" cellpadding="0" cellspacing="0">
	<tr><td id="menu" colspan="2"><?=$menubar?></td></tr>
	<tr>
		<td id="middle">
			<div id="mainLoader"></div>
			<div id="workspace" class="wrapper">
			</div>
		</td>
		<td id="right">
			<div class="wrapper" id="panel">
				
			</div>
			<!--<div id=btn_admin_login></div>-->
		</td>
	</tr>
	<tr>
		<td id="status" colspan="2"></td>
	</tr>
	</table>
</div>
<input type="hidden" id="token" value="T"/>

</body>
<HEAD>
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
</HEAD>
</html>
