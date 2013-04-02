<?php 
	// Connect to your Database 
	mysql_connect("localhost", "username", "password") or die(mysql_error()); 
	mysql_select_db("test") or die(mysql_error()); 
 
	// Select accounts
	$response = mysql_query("SELECT id as record_id, parentid, title, description, phone, email, photo FROM accounts") or die(mysql_error()); 
   
	// create some class for your records
	class Account
	{
		public $id = 0;
		public $parentid = null;
		public $title = '';
		public $desciption = '';
		public $phone = '';
		public $email = '';
		public $photo = '';
		
		public $children = array();
		
		public function load($record) {
			$this->id = intval($record['record_id']);
			$this->parentid = intval($record['parentid']);
			$this->title = $record['title'];
			$this->description = $record['description'];
			$this->phone = $record['phone'];
			$this->email = $record['email'];
			$this->photo = $record['photo'];
		} 
	}
	
	// create hash and group all children by parentid
	$children = Array();
	while($record = mysql_fetch_array( $response )) 
	{ 
		$account = new Account();
		$account->load($record);
		
		if( !isset($children[$account->parentid])) {
			$children[$account->parentid] = array();
		}
		array_push($children[$account->parentid], $account);
	} 

	// Create hierarchical structure starting from  $rootAccount
	function recursiveLoadChildren($parent, $children) {
		if(isset($children[$parent->id])) {
			foreach($children[$parent->id] as $id => $account) {
				array_push($parent->children, $account);
				recursiveLoadChildren($account, $children);
			}
		}
	}
  
	$rootAccount = $children[0][0];
	recursiveLoadChildren($rootAccount, $children);	
	
	function encodeURIComponent($str) {
        $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
        return strtr(rawurlencode($str), $revert);
    }

	// serialize $rootAccount object including all its children into JSON string  
	$jsonstring = encodeURIComponent(json_encode($rootAccount));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>jQuery Widget Hierarchical Chart PHP Demo</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="demo/js/jquery/jquery-1.7.2.js"></script>
	<script type="text/javascript" src="demo/js/jquery/jquery-ui-1.8.16.custom.min.js"></script>
	<link href="demo/js/jquery/ui-lightness/jquery-ui-1.8.16.custom.css" media="screen" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="demo/js/json3.min.js"></script>

	<!-- jQuery UI Layout -->
	<script type="text/javascript" src="demo/jquerylayout/jquery.layout-latest.min.js"></script>
	<link rel="stylesheet" type="text/css" href="demo/jquerylayout/layout-default-latest.css" />

	<!-- CSS Drop Down Menu -->
	<link href="demo/css/dropdown/dropdown.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="demo/css/dropdown/themes/default/default.ultimate.css" media="screen" rel="stylesheet" type="text/css" />

	<script type="text/javascript">
		jQuery(document).ready(function () {
			jQuery('body').layout(
			{
				center__paneSelector: "#contentpanel"
				, north__paneSelector: "#northpanel"
				, north__resizable: false
				, north__closable: false
				, north__spacing_open: 0
				, north__size: 40
			});
		});
	</script>

	<!-- header -->
	<link href="demo/css/primitives.latest.css?1017" media="screen" rel="stylesheet" type="text/css" />
	<script  type="text/javascript" src="demo/js/primitives.min.js?1019"></script>


	<script type="text/javascript">
		/* insert serialized JSON string here */
		<?php Print("var data=\"" . $jsonstring . "\";"); ?>		 
	</script>

	<script type="text/javascript">
		var orgDiagram = null;
		var treeItems = {};
		var contextidcounter = 0;
		var currentHighlightDataTreeItem = null;
		var currentCursorDataTreeItem = null;

		jQuery(document).ready(function () {
			jQuery.ajaxSetup({
				cache: false
			});

			jQuery('#contentpanel').layout(
			{
				center__paneSelector: "#centerpanel"
				, south__paneSelector: "#southpanel"
				, south__resizable: false
				, south__closable: false
				, south__spacing_open: 0
				, south__size: 50
				, west__size: 200
				, west__paneSelector: "#westpanel"
				, west__resizable: true
				, center__onresize: function () {
					if (orgDiagram != null) {
						jQuery("#centerpanel").orgDiagram("update", primitives.orgdiagram.UpdateMode.Refresh);
					}
				}
			});

			var templates = [];
			templates.push(getContactTemplate());

			var rootItem = getTreeItem(JSON.parse(decodeURIComponent(data)));
			rootItem.templateName = "contactTemplate";
			
			orgDiagram = jQuery("#centerpanel").orgDiagram({
				rootItem: rootItem,
				cursorItem: rootItem,
				graphicsType: primitives.common.GraphicsType.SVG,
				pageFitMode: primitives.orgdiagram.PageFitMode.FitToPage,
				verticalAlignment: primitives.common.VerticalAlignmentType.Middle,
				connectorType: primitives.orgdiagram.ConnectorType.Angular,
				minimalVisibility: primitives.orgdiagram.Visibility.Dot,
				selectionPathMode: primitives.orgdiagram.SelectionPathMode.FullStack,
				leavesPlacementType: primitives.orgdiagram.ChildrenPlacementType.Horizontal,
				hasButtons: primitives.common.Enabled.False,
				hasSelectorCheckbox: primitives.common.Enabled.False,
				templates: templates,
				onButtonClick: onButtonClick,
				onCursorChanging: onCursorChanging,
				onCursorChanged: onCursorChanged,
				onHighlightChanging: onHighlightChanging,
				onHighlightChanged: onHighlightChanged,
				onSelectionChanged: onSelectionChanged,
				onItemRender: onTemplateRender,
				itemTitleFirstFontColor: primitives.common.Colors.White,
				itemTitleSecondFontColor: primitives.common.Colors.White
			});
		});

		function getTreeItem(sourceItem) {
			var result = new primitives.orgdiagram.ItemConfig();
			result.title = sourceItem.title;
			result.description = sourceItem.description;
			result.phone = sourceItem.phone;
			result.email = sourceItem.email;
			result.image = "demo/images/photos/" +sourceItem.photo;
			result.groupTitle = sourceItem.title;
			result.id = sourceItem.id;
			result.href = "showdetails.php?recordid=" + result.id;
			if (sourceItem.children != null) {
				for (var index = 0; index < sourceItem.children.length; index += 1) {
					result.items.push(getTreeItem(sourceItem.children[index]));
				}
			}
			return result;
		}

		function getContactTemplate() {
			var result = new primitives.orgdiagram.TemplateConfig();
			result.name = "contactTemplate";

			result.itemSize = new primitives.common.Size(220, 120);
			result.minimizedItemSize = new primitives.common.Size(3, 3);
			result.highlightPadding = new primitives.common.Thickness(2, 2, 2, 2);


			var itemTemplate = jQuery(
			  '<div class="bp-item bp-corner-all bt-item-frame">'
				+ '<div class="bp-item bp-corner-all bp-title-frame" style="top: 2px; left: 2px; width: 216px; height: 20px;">'
					+ '<div name="title" class="bp-item bp-title" style="top: 3px; left: 6px; width: 208px; height: 18px;">'
					+ '</div>'
				+ '</div>'
				+ '<div class="bp-item bp-photo-frame" style="top: 26px; left: 2px; width: 50px; height: 60px;">'
					+ '<img name="photo" style="height:60px; width:50px;" />'
				+ '</div>'
				+ '<div name="phone" class="bp-item" style="top: 26px; left: 56px; width: 162px; height: 18px; font-size: 12px;"></div>'
				+ '<div name="email" class="bp-item" style="top: 44px; left: 56px; width: 162px; height: 18px; font-size: 12px;"></div>'
				+ '<div name="description" class="bp-item" style="top: 62px; left: 56px; width: 162px; height: 36px; font-size: 10px;"></div>'
				+ '<a name="readmore" class="bp-item" style="top: 104px; left: 4px; width: 212px; height: 12px; font-size: 10px; font-family: Arial; text-align: right; font-weight: bold; text-decoration: none; z-index:100;">Read more ...</a>'
			+ '</div>'
			).css({
				width: result.itemSize.width + "px",
				height: result.itemSize.height + "px"
			}).addClass("bp-item bp-corner-all bt-item-frame");
			result.itemTemplate = itemTemplate.wrap('<div>').parent().html();

			return result;
		}

		function onTemplateRender(event, data) {
			var hrefElement = data.element.find("[name=readmore]");
			switch (data.renderingMode) {
				case primitives.common.RenderingMode.Create:
					/* Initialize widgets here */
					hrefElement.click(function (e)
					{
						/* Block mouse click propogation in order to avoid layout updates before server postback*/
						primitives.common.stopPropagation(e);
					});
					break;
				case primitives.common.RenderingMode.Update:
					/* Update widgets here */
					break;
			}

			var itemConfig = data.context;

			if (data.templateName == "contactTemplate") {
				data.element.find("[name=photo]").attr({ "src": itemConfig.image });

				var fields = ["title", "description", "phone", "email"];
				for (var index = 0; index < fields.length; index += 1) {
					var field = fields[index];

					var element = data.element.find("[name=" + field + "]");
					if (element.text() != itemConfig[field]) {
						element.text(itemConfig[field]);
					}
				}
			}
			hrefElement.attr({ "href": itemConfig.href });
		}

		function onSelectionChanged(e, data) {
			var selectedItems = jQuery("#centerpanel").orgDiagram("option", "selectedItems");
			var message = "";
			for (var index = 0; index < selectedItems.length; index += 1) {
				var itemConfig = selectedItems[index];
				if (message != "") {
					message += ", ";
				}
				message += "<b>'" + itemConfig.title + "'</b>";
			}
			message += (data.parentItem != null ? " Parent item <b>'" + data.parentItem.title + "'" : "");
			jQuery("#southpanel").empty().append("User selected next items: " + message);
		}

		function onHighlightChanging(e, data) {
			var message = (data.context != null) ? "User is hovering mouse over item <b>'" + data.context.title + "'</b>." : "";
			message += (data.parentItem != null ? " Parent item <b>'" + data.parentItem.title + "'" : "");

			jQuery("#southpanel").empty().append(message);
		}

		function onHighlightChanged(e, data) {
			var message = (data.context != null) ? "User hovers mouse over item <b>'" + data.context.title + "'</b>." : "";
			message += (data.parentItem != null ? " Parent item <b>'" + data.parentItem.title + "'" : "");

			jQuery("#southpanel").empty().append(message);
		}

		function onCursorChanging(e, data) {
			var message = "User is clicking on item '" + data.context.title + "'.";
			message += (data.parentItem != null ? " Parent item <b>'" + data.parentItem.title + "'" : "");

			jQuery("#southpanel").empty().append(message);

			data.oldContext.templateName = null;
			data.context.templateName = "contactTemplate";
		}

		function onCursorChanged(e, data) {
			var message = "User clicked on item '" + data.context.title + "'.";
			message += (data.parentItem != null ? " Parent item <b>'" + data.parentItem.title + "'" : "");
			jQuery("#southpanel").empty().append(message);
		}

		function onButtonClick(e, data) {
			var message = "User clicked <b>'" + data.name + "'</b> button for item <b>'" + data.context.title + "'</b>.";
			message += (data.parentItem != null ? " Parent item <b>'" + data.parentItem.title + "'" : "");
			jQuery("#southpanel").empty().append(message);
		}

	</script>
</head>
<body style="font-size:12px">
	<div id="contentpanel" style="padding: 0px;">
		<div id="westpanel" style="padding: 5px; margin: 0px; border-style: solid; font-size: 12px; border-color: grey; border-width: 1px; overflow: scroll;">
			<h2>Hierarchy Chart PHP Demo</h2>
			<p>This demo requires PHP 5.2 & MySQL connection.</p>
			<p>Create test database from mysqldump folder and edit connection string in header of this PHP file.</p>
			<p>Cancel mouse events propogation before sending post back to server. See reference of event arguments</p>
		</div>
		<div id="centerpanel" style="overflow: hidden; padding: 0px; margin: 0px; border: 0px;">
		</div>
		<div id="southpanel">
		</div>
	</div>
	<div id="northpanel" style="padding: 0px; margin: 0px;">
		<ul id="nav" class="dropdown dropdown-horizontal">
			<li><a href="index.html">Navigation</a></li>
			<li><a href="orientation.html">Chart Orientation</a></li>
			<li><a href="crossteamgroup.html">Cross Team Group</a></li>
			<li><a href="verticallayout.html">Vertical Children Layout</a></li>
			<li><a href="directreports40.html">Chart Editor</a></li>
			<li><a href="resize.html">Misc</a></li>
			<li><a href="bootstrap.html">Bootstrap</a></li>
			<li><a href="phpdemo.php">PHP Demo</a></li>
			<li><a href="text.html">Text</a></li>
			<li><a href="callout.html">Callout</a></li>
		</ul>
	</div>
</body>
</html>