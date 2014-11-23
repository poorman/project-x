<?php  if ( ! defined('BASEPATH')) exit('Access denied.'); ?>
<script>
/*
 * Global javaScript variables
*/
window.action = false;
window.base_url = '<?php echo $app['base_url'];?>index.php/';
window.containers = null;
window.containers_obj = null;
window.debug = true;//false;
window.pending_changes = false;
window.pre = "ui";
window.processing = null;
window.pseudo_link = null;
window.refresh_data = null;
window.richtext_content = null;
window.screen_height = null;
window.screen_width = null;
window.speed = 150;// speed for fadeIn/fadeOut 
window.timestamp =new Date().getTime();
window.url = null;
/*
 * Appends hash to url
*/
if(!window.location.hash) {
	window.location.hash = window.pre;
}
else {
	window.hash = window.location.hash.slice(1);
	window.url = window.base_url+window.hash;
	window.url = window.url.replace('#','');
	var data = '&ajax=1&screen_height='+window.screen_height+'&screen_width='+window.screen_width;
	$.ajax ({
		data : data,
		type : "GET",
		dataType : 'json',
		url : window.url,
		
		success : function(data) {
			var count = 0;
			$.each (data, function(div, content) {
				if(div == 'script') {
					eval(content);
				}
				if(jQuery.inArray(div,containers) == -1) {
					ui.discard(div);
					window.containers_obj.push(document.getElementById(div));
					window.containers.push(div);
				}
				window.containers_obj[jQuery.inArray(div,containers)].innerHTML = content;
				count++;
			});
			data = null;
		},
		error : function (xhr, ajaxOptions, thrownError) {
			alert(thrownError);
		console.log('ajaxOptions');
		} 
	});
}
/*
	Listen for hash change and
	use path and call ajax request.
*/
$(window).bind('hashchange', function () {
	window.hash = window.location.hash.slice(1);
	if (window.hash == window.pre) {
		ui.refresh_page();
		return false;
	}
	else {
		window.url = window.base_url+window.hash;
		window.url = window.url.replace('#','');
		var data = '&ajax=1&screen_height='+window.screen_height+'&screen_width='+window.screen_width;
		$.ajax( {
			data : data,
			type : "GET",
			dataType : 'json',
			url : window.url,
			
			success : function(data) {
				var count = 0;
				$.each (data, function(div, content) {
					if (div == 'script') {
						eval(content);
					}
					if (jQuery.inArray(div,containers) == -1) {
						ui.discard(div);
						window.containers_obj.push(document.getElementById(div));
						window.containers.push(div);
					}
					window.containers_obj[jQuery.inArray(div,containers)].innerHTML = content;
					count++;
				});
				
				data = null;
			},
			error : function (xhr, ajaxOptions, thrownError) {
				alert(thrownError);
				console.log('ajaxOptions');
			}
		});
	}
});
/*
	Initialize
*/
$(document).ready(function() {
	window.screen_height = ui.get_screen_height();
	window.screen_width = ui.get_screen_width();
	$('a[rel*=facebox]').facebox();
	window.processing = $('.processing');
	window.pseudo_link = document.createElement('a');
	window.containers = new Array();
	window.containers_obj = new Array();	
});
/*
 * UI object
 */
var ui = {
/*
 * Discards element
 */
	discard : function (div) {
		try {
			window.containers_obj.splice([jQuery.inArray(div,window.containers)],1);
			window.containers.splice([jQuery.inArray(div,window.containers)],1);
			return true;
		}
		catch (e) {
			if(window.debug) {
				alert('discard()::caught::'+e);
			}
		}
	},
/*
 * Loads within element
 */
	element : function (obj,silent) { // gets data via ajax and places in container; does not change url in browser
		var self = this;
		try {
			if (silent != true) {
				window.processing.fadeIn(window.speed);
			}
			window.url = $(obj).attr('href');
			window.url = window.url.replace('#','');
			var get = window.url.indexOf('?');
			var query_str = '';
			if (get != -1) {
				query_str = '&'+window.url.substr(get+1);
				window.url = window.url.substr(0,get)
			}
			window.url=window.base_url+window.url;
			var data = '&ajax=1&screen_height='+window.screen_height+'&screen_width='+window.screen_width;
			$.ajax ({
				data : data,
				type : "GET",
				dataType : 'json',
				url : window.url,
			
				success : function(data) {
					if (data.error != null) {
						window.location = window.base_url;
					}
					else {
						if (data.redirect != null) { 
							window.location = window.base_url + data.redirect;
						}
					}
					var count = 0;
					$.each (data, function(div, content) {
						if (div == 'script') {
							eval(content);
						}
						if (jQuery.inArray(div,containers) == -1) {
							self.discard(div)
							window.containers_obj.push(document.getElementById(div));
							window.containers.push(div);
						}
						window.containers_obj[jQuery.inArray(div,window.containers)].innerHTML = content;
						count++;
					});
					data = null;
					if (silent != true) {
						window.processing.fadeOut(window.speed);
					}
					return true; 
				},
				error : function(request, status, error) {
					self.handle_error('do_import()::error::'+error);
					if (window.debug) { 
						alert('do_import()::error::'+error);
					}
				}
			});
		}
		catch (e) {
			alert('do_import()::caught::'+e.message);
		}
	},
/* 
 * Screen Properties 
 * Screen Height
 */
	get_screen_height : function ( ) {
		return $(window).height(); 
		if (window.innerWidth) {
			return window.innerHeight;
		}
		else {
			if (document.all) {
				return document.documentElement.clientHeight;
			}
			else {
				return document.getElementsByTagName('body')[0].clientHeight
			}
		}
	},

/* 
 * Screen Properties 
 * Screen Width
 */
	get_screen_width : function ( ) {
		return $(window).width();
		if (window.innerWidth) {
			return window.innerWidth;
		}
		else {
			if (document.all) {
				return document.documentElement.clientWidth;
			}
			else {
				return document.getElementsByTagName('body')[0].clientWidth
			}
		}
	},

/*
 * Refreshes page
 */
	refresh_page : function(auto) {
		var self = this; // for easy reference
		try {
			var auto_refreshed = '0';
			if (auto) {
				auto_refreshed = '1';
			}
			if (auto_refreshed == '0') {
				window.processing.fadeIn(window.speed);
			}
			window.url = document.location.href;
			if (window.url.indexOf(window.pre) == -1) {
				self.load_home();
				window.processing.fadeOut();
				return false;
			}
			var url_array = window.url.split('#');
			window.hash = url_array[1];
			window.url = window.base_url+window.hash;	
			window.refresh_data = document.getElementById('refresh_data').innerHTML;
			var q = '?';
			if (window.url.indexOf('?') == -1) {
				q = '&';
			}
			var data = q+'ajax=1&refresh=true&refresh_data='+window.refresh_data+'&auto_refreshed='+auto_refreshed+'&screen_height='+window.screen_height+'&screen_width='+window.screen_width;
			$.ajax ({
				data : data,
				type : "GET",
				dataType : 'json',
				url : window.url,
				success : function (data) {
					$.each (data, function(div, content) {
						if (div == 'script') {
							eval(content);
						}
						else {
							if (document.getElementById(div)) {
								if (jQuery.inArray(div,window.containers) == -1) {
									self.discard(div)
									window.containers_obj.push(document.getElementById(div));
									window.containers.push(div);
								}
								window.containers_obj[jQuery.inArray(div,window.containers)].innerHTML = content;
							}
						}
						
					});
					data = null;
					window.processing.fadeOut(window.speed);
				},
				error : function (request, status, error) {
					window.processing.fadeOut(window.speed);
					if(status == 'timeout') {
						self.show_msg('<div class="msg-error">'+status+'</div>');
					}
					self.handle_error('UI.refresh_page()::error::'+error);
					if(window.debug) {
						alert('UI.refresh_page()::error::'+error+':::request:::'+request+':::status:::'+status);
					}
				}
			});
		}
		catch (e) {
			self.handle_error('refresh_page()::caught::'+e.message);
			if(window.debug) {
				alert('refresh_page()::caught::'+e.message);
			}
		}
	},

/*
 * Refreshes page without querystring
 */
	refresh_page_no_querystring : function (auto) {
		var self = this; // for easy reference;
		try {
			var auto_refreshed = '0';
			if (auto) {
				auto_refreshed = '1';
			}
			if (auto_refreshed == '0') {
				window.processing.fadeIn(window.speed);
			}
			window.url = document.location.href;
			if(window.url.indexOf(window.pre) == -1){
				self.load_home();
				window.processing.fadeOut();
				return false;
			}
			var url_array = window.url.split('#');
			window.hash = url_array[1];
			window.url = window.base_url+window.hash;	
			window.refresh_data = document.getElementById('refresh_data').innerHTML;
			var data =  '&ajax=1&refresh=true&refresh_data='+window.refresh_data+'&auto_refreshed='+auto_refreshed+'&screen_height='+window.screen_height+'&screen_width='+window.screen_width;
			$.ajax ({
				data : data,
				type : "GET",
				dataType : 'json',
				url : window.url,
				success : function(data) {
					$.each (data, function(div, content) {
						if (div == 'script') { 
							eval(content);
						}
						else {
							if (document.getElementById(div)) {
								if (jQuery.inArray(div,containers) == -1) {
									self.discard(div)
									window.containers_obj.push(document.getElementById(div));
									window.containers.push(div);
								}
								window.containers_obj[jQuery.inArray(div,window.containers)].innerHTML = content;
							}
						}
					});
					data = null;
					window.processing.fadeOut(window.speed);
				},
				error : function(request, status, error) {
					window.processing.fadeOut(window.speed);
					if (status == 'timeout') {
						self.show_msg('<div class="msg-error">'+status+'</div>');
					}
					self.handle_error('refresh_page()::error::'+error);
					if(window.debug) {
						alert('refresh_page()::error::'+error+':::request:::'+request+':::status:::'+status);
					}
				}
			});
		}
		catch (e) {
			self.handle_error('refresh_page()::caught::'+e.message);
			if (window.debug) {
				alert('refresh_page()::caught::'+e.message);
			}
		}
	},
	
/*
 * clears dialog
 */
	dialog_clear : function() {
		this.discard('facebox');
		$('#facebox').remove();
	},
/*
 * closes dialog
 */
	dialog_close : function( ) { 
		jQuery(document).trigger('close.facebox');
	},
/*
 * opens dialog
 */
	dialog_open : function (content){ // opens dialog box with specified content
		jQuery.facebox(content.dialog);
		if (content.script) {
			eval(content.script);
		}
	},
/*
 * opens no script
 */
	do_dialog_raw : function(content) {
		jQuery.facebox(content);
	},
/*
 * dialog in window
 */
	dialog_window : function(obj) {
		var left_pos = screen.width - 1000
		left_pos = (left_pos/2)-5;
		var top_pos = screen.height - 600;
		top_pos = (top_pos/2)-30;
		window.url = $(obj).attr('href');
		window.url = window.url.replace(window.base_url,'');
		window.url = window.base_url + window.url;	
		// get ?
		var get = window.url.indexOf('?');
		var query_str = '?tutorial=true';
		if (get != -1) {
			query_str += '&'+window.url.substr(get+1);
			window.url = window.url.substr(0,get)
		}
		window.url += query_str;
		window.open(window.url, "tutorial", "width=900,height=650,scrollbars=1,left="+left_pos+",top="+top_pos+"&screen_height="+window.screen_height+"&screen_width="+window.screen_width);
	},
	
/*
* Dialog Functions
*/
	dialog : function(obj) { // retrieves content to be displayed via ajax and places it into a dialog box
		var self = this;
		try {
			window.processing.fadeIn(window.speed);
			window.url = $(obj).attr('href');
			var url_array = window.url.split('#');
			window.hash = url_array[1];
			window.url = window.base_url+window.hash;		
			var data = '&ajax=1&screen_height='+window.screen_height+'&screen_width='+window.screen_width;
			$.ajax ({
				data : data,
				type : "GET",
				dataType : 'json',
				url : window.url,
			
				success : function(data) {
					if (data.error) {
						window.location = window.base_url;
					}
					else {
						if (data.redirect) { 
							if (data.redirect == 'current') { // page refresh
								self.refresh_page();
								self.dialog_close();
							}
							else { // page redirect
							window.location = window.base_url + data.redirect;
							}
						}
						else {
							if(data.dialog) { // open dialog
								self.dialog_open(data);
							}
							else { // open error dialog 
								self.dialog_open(data.error);
							}
						}
					}
					window.processing.fadeOut(window.speed);
					data = null;
				},
				error : function (xhr, ajaxOptions, thrownError) {
					alert(thrownError);
					console.log('ajaxOptions');
				}
			});
			return false;
		}
		catch (e) {
			self.handle_error('do_dialog()::caught::'+e.message);
			if (window.debug) {
				alert('do_dialog()::caught::'+e.message);
			}
		}
	},

	dialog_by_href : function (href) { //retrieves content to be displayed via ajax and places it into a dialog box
		var self = this;
		try {
			window.processing.fadeIn(speed);
			window.url = href.replace('#','');
			window.url = window.base_url + window.url;
			$.ajax ({
				type : "GET",
				dataType : 'json',
				url : window.url+'?ajax=true&screen_height='+window.screen_height+'&screen_width='+window.screen_width, 
				success : function(data){
					if (data.error){
						window.location = window.base_url;
					}
					else {
						if(data.dialog){ // open dialog
							self.dialog_open(data);
						}
						else { // open error dialog
							self.dialog_open(data.error);
						}
					}
					window.processing.fadeOut(window.speed);
				},
				error : function (request, status, error){
					self.handle_error('do_dialog_by_href()::error::'+error);
					if(window.debug) {
						alert('do_dialog_by_href()::error::'+error);
					}
				}
			});
			return false;
		}
		catch (e) {
			self.handle_error('do_dialog_by_href()::caught::'+e.message);
			if (window.debug) {
				alert('do_dialog_by_href()::caught::'+e.message);
			}
		}
	},
	load_home : function () { // called on intital load
		var self = this;
		try{
			window.location.hash = window.pre;
			window.url = window.base_url+window.pre+'/home';
			var data ='&ajax=1&screen_height='+window.screen_height+'&screen_width='+window.screen_width;
			$.ajax ({
				data: data,
				type: "GET",
				dataType: 'json',
				url: window.url,
				success : function(data) {
					var count = 0;
					$.each(data, function(div, content) {
						if (div == 'script') {
							eval(content);
						}
						if (jQuery.inArray(div,containers) == -1) {
							self.discard(div)
							window.containers_obj.push(document.getElementById(div));
							window.containers.push(div);
						}
						window.containers_obj[jQuery.inArray(div,window.containers)].innerHTML = content;
						count++;
					});
					data = null;
				},
				error:function (xhr, ajaxOptions, thrownError) {
					alert(thrownError);
					console.log('ajaxOptions');
				} 
			});
		}
		catch (e) {
			alert('error');
		}
	}
};

var option = {
/*
 * Discards element
 */
	ChangeLanguage : function (obj) {
		var url = pre+'/language/change/'+obj.value;
		url = url.replace('#','');alert(url);
		window.pseudo_link.setAttribute('href',url);
		ui.element(pseudo_link);
		return false;
	},
	ChangeTemplate : function (obj) {
		alert('Change Template');
	},
	ChangeTheme : function (obj) {
		alert('Change Theme');
	}
};












































/* ===========================================================================================================================*/

/* ===========================================================================================================================*/

/* ===========================================================================================================================*/
/* ===========================================================================================================================*/

/* ===========================================================================================================================*/
/* ===========================================================================================================================*/
/* ===========================================================================================================================*/

/* ===========================================================================================================================*/
/* ===========================================================================================================================*/

/* ===========================================================================================================================*/
/* ===========================================================================================================================*/

/* ===========================================================================================================================*/
/* ===========================================================================================================================*/// submits a form 
function submit_form(form){
	try{
		refresh_data = document.getElementById('refresh_data').innerHTML;
		if($(form).attr('class') == 'http'){
			document.form.submit();
			return false;
		}
		processing.fadeIn(speed);
		var url = $(form).attr('action');
		var form_id = $(form).attr('id');
		var data = '&data=';
		
		url = url.replace('#','');
		var new_url = base_url + url;
		$('form#'+form_id+' input[type=text]').each(function(){
			data += '&'+$(this).attr('name')+'='+escape($(this).val());									
		});
		$('form#'+form_id+' input[type=password]').each(function(){
			data += '&'+$(this).attr('name')+'='+escape($(this).val());										
		});
		$('form#'+form_id+' input[type=hidden]').each(function(){
			data += '&'+$(this).attr('name')+'='+escape($(this).val());										
		});
		$('form#'+form_id+' select :selected').each(function(){
			data += '&'+$(this).parent().attr('name')+'='+escape($(this).val());										
		});
		$('form#'+form_id+' select optgroup option:selected').each(function(){
			data += '&'+$(this).parent().parent().attr('name')+'='+escape($(this).val());										
		});	
		$('form#'+form_id+' input[type=checkbox]:checked').each(function(){
			data += '&'+$(this).attr('name')+'='+escape($(this).val());										
		});
		$('form#'+form_id+' input[type=radio]:checked').each(function(){
			data += '&'+$(this).attr('name')+'='+escape($(this).val());										
		});	
		$('form#'+form_id+' textarea').each(function(){
			data += '&'+$(this).attr('name')+'='+escape($(this).val());	
		});	
		if(richtext_content){
			data += richtext_content;
			richtext_content = null;
		} 
		screen_height = get_screen_height();
		screen_width = get_screen_width();
		data = '&ajax=1&screen_height='+screen_height+'&screen_width='+screen_width+'&refresh_data='+refresh_data+data
		url = base_url+url;
		
		$.ajax({ // process returned data
			   type: "POST",
			   url: url,
			   data: data,
			   dataType: 'json',
			   success: function(data){
				  if(data.error){
					  window.location = base_url;
				  }
				  else if(data.redirect){ 
					  window.location = base_url + data.redirect;
				  }
				  else if(data.dialog){ // open dialog				  
					  do_dialog_open_box(data);
				  }
				  else {
					  $.each(data, function(div, content) {
						  if(div == 'title') document.title = content; // set title in browser
						  else if(div == 'raw') show_raw(content);
						  else if(div == 'script') eval(content); 
						  else {
							  if(jQuery.inArray(div,containers) == -1){
								  containers_obj.push(document.getElementById(div));
								  containers.push(div);
							  }
							  containers_obj[jQuery.inArray(div,containers)].innerHTML = content;
						  }
					 });
					 data = null;
				  }
				  pending_changes = false;
				  processing.fadeOut(speed);
			   },
			   error:function(request, status, error){
				   handle_error('submit_form()::error::'+error);
				   if(debug) alert('submit_form()::error::'+error);
			   }
				
		});	
		return false;
	}
	catch (e)
	{
		handle_error('submit_form()::caught::'+e.message);
		if(debug) alert('submit_form()::caught::'+e.message);
	}
}
/* ===========================================================================================================================*/
/* ===========================================================================================================================*/// submits a form 
function set_richtext_content(id){
	richtext_content = '&richtext='+escape(tinyMCE.activeEditor.getContent([id]));
}

//stop form submit on enter
$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
</script>