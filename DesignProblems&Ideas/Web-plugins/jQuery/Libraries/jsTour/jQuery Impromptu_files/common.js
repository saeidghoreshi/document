/*
 * ---------------------------
 * functions for the examples
 * ---------------------------
 */
function mycallbackfunc(e,v,m,f){
	alert('i clicked ' + v);
}

function mycallbackform(e,v,m,f){
	if(v != undefined)
		alert(v +' ' + f.alertName);
}

function mysubmitfunc(e,v,m,f){
	an = m.children('#alertName');
	if(f.alertName == ""){
		an.css("border","solid #ff0000 1px");
		return false;
	}
	return true;
}

(function($){
	$.fn.extend({
		dropIn: function(speed,callback){
			var $t = $(this);

			if($t.css("display") == "none"){
				eltop = $t.css('top');
				elouterHeight = $t.outerHeight(true);

				$t.css({ top: -elouterHeight, display: 'block' }).animate({ top: eltop },speed,'swing',callback);
			}
		}
	});
})(jQuery);

var txt = 'Please enter your name:<br /><input type="text" id="alertName" name="alertName" value="name here" />';
var txt2 = 'Try submitting an empty field:<br /><input type="text" id="alertName" name="alertName" value="" />';	

var brown_theme_text = '<h3>Example 13</h3><p>Save these settings?</p><img src="images/help.gif" alt="help" class="helpImg" />';

var statesdemo = {
	state0: {
		html:'test 1.<br />test 1..<br />test 1...',
		buttons: { Cancel: false, Next: true },
		focus: 1,
		submit:function(e,v,m){ 
			if(!v) return true;
			else $.prompt.goToState('state1');//go forward
			return false; 
		}
	},
	state1: {
		html:'test 2',
		buttons: { Back: -1, Exit: 0 },
		focus: 1,
		submit:function(e,v,m){ 
			if(v==0) $.prompt.close()
			else if(v=-1) $.prompt.goToState('state0');//go back
			return false; 
		}
	}
};

var tourSubmitFunc = function(e,v,m,f){
			if(v === -1){
				$.prompt.prevState();
				return false;
			}
			else if(v === 1){
				$.prompt.nextState();
				return false;
			}
},
tourStates = [
	{
		html: 'Welcome to jQuery Impromptu, lets take a quick tour of the plugin.',
		buttons: { Next: 1 },
		focus: 1,
		position: { container: '#header', x: 10, y: 45, width: 200, arrow: 'tl' },
		submit: tourSubmitFunc
	},
	{
		html: 'When you get ready to use Impromptu, you can get it here.',
		buttons: { Prev: -1, Next: 1 },
		focus: 1,
		position: { container: '#downloadHeader', x: 170, y: -10, width: 300, arrow: 'lt' },
		submit: tourSubmitFunc
	},
	{
		html: "You will also need this CSS",
		buttons: { Prev: -1, Next: 1 },
		focus: 1,
		position: { container: '#cssHeader', x: 40, y: -100, width: 250, arrow: 'bl' },
		submit: tourSubmitFunc
	},
	{
		html: 'A description of the options are under the Docs section.',
		buttons: { Prev: -1, Next: 1 },
		focus: 1,
		position: { container: '#docsHeader', x: 115, y: -85, width: 200, arrow: 'lb' },
		submit: tourSubmitFunc
	},
	{
		html: 'You will find plenty of examples to get you going.. including this tour..',
		buttons: { Prev: -1, Next: 1 },
		focus: 1,
		position: { container: '#examplesHeader', x: -300, y: -45, width: 250, arrow: 'rm' },
		submit: tourSubmitFunc
	},
	{
		html: 'Yep, see, creating a tour is easy.. Here is the source:',
		buttons: { Prev: -1, Next: 1 },
		focus: 1,
		position: { container: '#tourExample', x: -340, y: 5, width: 300, arrow: 'rt' },
		submit: tourSubmitFunc
	},
	{
		html: 'This concludes our tour. If you found Impromptu helpful, please see the links to the left, if not, thanks for stopping by!',
		buttons: { Done: 2 },
		focus: 1,
		position: { container: '#donationHeader', x: 420, y: 0, width: 300, arrow: 'lm' },
		submit: tourSubmitFunc
	}
];
$(function(){
	$('#tourButton').click(function(e){
		$.prompt(tourStates, { opacity: 0.3 });
		return false;
	});
});
