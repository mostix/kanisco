var base_url = "http://www.procad-bg.com/";

/**
 * http://www.openjs.com/scripts/events/keyboard_shortcuts/
 * Version : 2.01.B
 * By Binny V A
 * License : BSD
 */
shortcut = {
	'all_shortcuts':{},//All the shortcuts are stored in this array
	'add': function(shortcut_combination,callback,opt) {
		//Provide a set of default options
		var default_options = {
			'type':'keydown',
			'propagate':false,
			'disable_in_input':false,
			'target':document,
			'keycode':false
		}
		if(!opt) opt = default_options;
		else {
			for(var dfo in default_options) {
				if(typeof opt[dfo] == 'undefined') opt[dfo] = default_options[dfo];
			}
		}

		var ele = opt.target;
		if(typeof opt.target == 'string') ele = document.getElementById(opt.target);
		var ths = this;
		shortcut_combination = shortcut_combination.toLowerCase();

		//The function to be called at keypress
		var func = function(e) {
			e = e || window.event;
			
			if(opt['disable_in_input']) { //Don't enable shortcut keys in Input, Textarea fields
				var element;
				if(e.target) element=e.target;
				else if(e.srcElement) element=e.srcElement;
				if(element.nodeType==3) element=element.parentNode;

				if(element.tagName == 'INPUT' || element.tagName == 'TEXTAREA') return;
			}
	
			//Find Which key is pressed
			if (e.keyCode) code = e.keyCode;
			else if (e.which) code = e.which;
			var character = String.fromCharCode(code).toLowerCase();
			
			if(code == 188) character=","; //If the user presses , when the type is onkeydown
			if(code == 190) character="."; //If the user presses , when the type is onkeydown

			var keys = shortcut_combination.split("+");
			//Key Pressed - counts the number of valid keypresses - if it is same as the number of keys, the shortcut function is invoked
			var kp = 0;
			
			//Work around for stupid Shift key bug created by using lowercase - as a result the shift+num combination was broken
			var shift_nums = {
				"`":"~",
				"1":"!",
				"2":"@",
				"3":"#",
				"4":"$",
				"5":"%",
				"6":"^",
				"7":"&",
				"8":"*",
				"9":"(",
				"0":")",
				"-":"_",
				"=":"+",
				";":":",
				"'":"\"",
				",":"<",
				".":">",
				"/":"?",
				"\\":"|"
			}
			//Special Keys - and their codes
			var special_keys = {
				'esc':27,
				'escape':27,
				'tab':9,
				'space':32,
				'return':13,
				'enter':13,
				'backspace':8,
	
				'scrolllock':145,
				'scroll_lock':145,
				'scroll':145,
				'capslock':20,
				'caps_lock':20,
				'caps':20,
				'numlock':144,
				'num_lock':144,
				'num':144,
				
				'pause':19,
				'break':19,
				
				'insert':45,
				'home':36,
				'delete':46,
				'end':35,
				
				'pageup':33,
				'page_up':33,
				'pu':33,
	
				'pagedown':34,
				'page_down':34,
				'pd':34,
	
				'left':37,
				'up':38,
				'right':39,
				'down':40,
	
				'f1':112,
				'f2':113,
				'f3':114,
				'f4':115,
				'f5':116,
				'f6':117,
				'f7':118,
				'f8':119,
				'f9':120,
				'f10':121,
				'f11':122,
				'f12':123
			}
	
			var modifiers = { 
				shift: { wanted:false, pressed:false},
				ctrl : { wanted:false, pressed:false},
				alt  : { wanted:false, pressed:false},
				meta : { wanted:false, pressed:false}	//Meta is Mac specific
			};
                        
			if(e.ctrlKey)	modifiers.ctrl.pressed = true;
			if(e.shiftKey)	modifiers.shift.pressed = true;
			if(e.altKey)	modifiers.alt.pressed = true;
			if(e.metaKey)   modifiers.meta.pressed = true;
                        
			for(var i=0; k=keys[i],i<keys.length; i++) {
				//Modifiers
				if(k == 'ctrl' || k == 'control') {
					kp++;
					modifiers.ctrl.wanted = true;

				} else if(k == 'shift') {
					kp++;
					modifiers.shift.wanted = true;

				} else if(k == 'alt') {
					kp++;
					modifiers.alt.wanted = true;
				} else if(k == 'meta') {
					kp++;
					modifiers.meta.wanted = true;
				} else if(k.length > 1) { //If it is a special key
					if(special_keys[k] == code) kp++;
					
				} else if(opt['keycode']) {
					if(opt['keycode'] == code) kp++;

				} else { //The special keys did not match
					if(character == k) kp++;
					else {
						if(shift_nums[character] && e.shiftKey) { //Stupid Shift key bug created by using lowercase
							character = shift_nums[character]; 
							if(character == k) kp++;
						}
					}
				}
			}
			
			if(kp == keys.length && 
						modifiers.ctrl.pressed == modifiers.ctrl.wanted &&
						modifiers.shift.pressed == modifiers.shift.wanted &&
						modifiers.alt.pressed == modifiers.alt.wanted &&
						modifiers.meta.pressed == modifiers.meta.wanted) {
				callback(e);
	
				if(!opt['propagate']) { //Stop the event
					//e.cancelBubble is supported by IE - this will kill the bubbling process.
					e.cancelBubble = true;
					e.returnValue = false;
	
					//e.stopPropagation works in Firefox.
					if (e.stopPropagation) {
						e.stopPropagation();
						e.preventDefault();
					}
					return false;
				}
			}
		}
		this.all_shortcuts[shortcut_combination] = {
			'callback':func, 
			'target':ele, 
			'event': opt['type']
		};
		//Attach the function with the event
		if(ele.addEventListener) ele.addEventListener(opt['type'], func, false);
		else if(ele.attachEvent) ele.attachEvent('on'+opt['type'], func);
		else ele['on'+opt['type']] = func;
	},

	//Remove the shortcut - just specify the shortcut and I will remove the binding
	'remove':function(shortcut_combination) {
		shortcut_combination = shortcut_combination.toLowerCase();
		var binding = this.all_shortcuts[shortcut_combination];
		delete(this.all_shortcuts[shortcut_combination])
		if(!binding) return;
		var type = binding['event'];
		var ele = binding['target'];
		var callback = binding['callback'];

		if(ele.detachEvent) ele.detachEvent('on'+type, callback);
		else if(ele.removeEventListener) ele.removeEventListener(type, callback, false);
		else ele['on'+type] = false;
	}
}

shortcut.add("esc",function() {
  $(".row_over").removeClass("row_over_edit");
});
shortcut.add("F1",function() {
  window.location = "";
});

function JsPaginating(btn) {
  var pag_id = $(btn).attr("data");
  if(pag_id == "" || pag_id === undefined) return;
  var page_count = $("#products_list .page_count").val();
  var prev_page = "";
  var next_page = "";
  if(pag_id == "1") {
    $(".js_pagination .btn_prev_page").addClass("disabled");
    $(".js_pagination .btn_prev_page a").attr("data","");
    $(".js_pagination .btn_next_page").removeClass("disabled");
    $(".js_pagination .btn_next_page a").attr("data","2");
  }
  else if(pag_id == page_count){
    prev_page = parseInt(pag_id)-1;
    $(".js_pagination .btn_prev_page").removeClass("disabled");
    $(".js_pagination .btn_prev_page a").attr("data",prev_page);
    $(".js_pagination .btn_next_page").addClass("disabled");
    $(".js_pagination .btn_next_page a").attr("data","");
  }
  else {
    prev_page = parseInt(pag_id)-1;
    next_page = parseInt(pag_id)+1;
    $(".js_pagination .btn_prev_page").removeClass("disabled");
    $(".js_pagination .btn_prev_page a").attr("data",prev_page);
    $(".js_pagination .btn_next_page").removeClass("disabled");
    $(".js_pagination .btn_next_page a").attr("data",next_page);
  }
  if($(btn).parent().hasClass("active")) {
    // do nothing
  }
  else {
    $(".js_pagination li").removeClass("active");
    $(".js_pagination #pag_"+pag_id).addClass("active");
    $("#products_list table").hide();
    $("#products_list table.row_"+pag_id).show();
  }
  event.preventDefault();
}
      
function fixedEncodeURIComponent (str) {
  return encodeURIComponent(str).replace(/[!'()]/g, escape).replace(/\*/g, "%2A");
}

// Handling Cookies

function createCookie(name,value,hours) {
  var expires = "";
  if (hours) {
    var date = new Date();
    date.setTime(date.getTime()+(hours*60*60*1000));
    expires = "; expires="+date.toGMTString();
  }
  document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}

function eraseCookie(name) {
  createCookie(name,"",-3);
}

// Disable Right Click Script
 
function IE(e) {
  if (navigator.appName == "Microsoft Internet Explorer" && (event.button == "2" || event.button == "3")) {
    return false;
  }
}
function NS(e) {
  if (document.layers || (document.getElementById && !document.all)) {
    if (e.which == "2" || e.which == "3") {
      return false;
    }
  }
}
//document.onmousedown=IE;document.onmouseup=NS;document.oncontextmenu=new Function("return false");


//Custom alert box
var ALERT_TITLE = "Oops!";
var ALERT_BUTTON_TEXT = "Ok";

if(document.getElementById) {
  window.alert = function(txt) {
    createCustomAlert(txt);
  }
}

function createCustomAlert(txt) {
  d = document;

  if(d.getElementById("modal_container")) return;

  mObj = d.getElementsByTagName("body")[0].appendChild(d.createElement("div"));
  mObj.id = "modal_container";
  //mObj.style.height = $(window).height() + "px";

  alertObj = mObj.appendChild(d.createElement("div"));
  alertObj.id = "alert_box";
  if(d.all && !window.opera) alertObj.style.top = document.documentElement.scrollTop + "px";
  alertObj.style.left = (d.documentElement.scrollWidth - alertObj.offsetWidth)/2 + "px";
  alertObj.style.visiblity="visible";

  h1 = alertObj.appendChild(d.createElement("h1"));
  h1.appendChild(d.createTextNode(ALERT_TITLE));

  msg = alertObj.appendChild(d.createElement("p"));
  //msg.appendChild(d.createTextNode(txt));
  msg.innerHTML = txt;

  btn = alertObj.appendChild(d.createElement("a"));
  btn.id = "closeBtn";
  btn.appendChild(d.createTextNode(ALERT_BUTTON_TEXT));
  btn.href = "#";
  btn.focus();
  btn.onclick = function() { removeCustomAlert();return false; }

  alertObj.style.display = "block";
	
}

function removeCustomAlert() {
  document.getElementsByTagName("body")[0].removeChild(document.getElementById("modal_container"));
}

function ful(){
  alert('Alert this pages');
}

function Checkbox(checkbox) {
  state = checkbox.checked;
  //alert(state);
  if($(checkbox).parent().hasClass("checkbox_checked")) {
    $(checkbox).parent().removeClass("checkbox_checked");
    $(checkbox).attr("checked",false);
  }
  else {
    $(checkbox).parent().addClass("checkbox_checked");
    $(checkbox).attr("checked",true);
  }
}

function SelectAllCheckboxes(clicked_checkbox) {
  checkboxes = document.getElementsByTagName("input");
  state = clicked_checkbox.checked;
  for (i=0; i<checkboxes.length ; i++) {
    if (checkboxes[i].type == "checkbox") {
      checkboxes[i].checked = state;
    }
  }
}

function ToggleCollapse(cid) {
  document.getElementById(cid).style.display=(document.getElementById(cid).style.display!="block")? "block" : "none";
}

function NoRightsToEdit() {
  alert("You have no rights to insert or edit information!");
  $(".row_over").removeClass("row_over_edit");
}

function NoRightsToDelete() {
  alert("You have no rights to delete information!");
  $(".row_over").removeClass("row_over_edit");
}

function GetUserAccess() {

//  var user_id = $("#user_id").val();
//  if(user_id == "" || user_id == undefined) {
//    location.reload();
//    return false;
//  }
  var user_rights_access = $(".menu_ul_2_level li.active .menu_a_3_level").attr("user-rights-access");
  if(user_rights_access == undefined) {
    user_rights_access = $(".menu_ul_1_level li.active .menu_a_2_level").attr("user-rights-access");
  }
  if(user_rights_access == undefined) {
    user_rights_access = $("#menu li.active .menu_a_1_level").attr("user-rights-access");
  }
  return user_rights_access;
  
}

function CheckEditRights() {

//  var user_id = $("#user_id").val();
//  if(user_id == "" || user_id == undefined) {
//    location.reload();
//    return false;
//  }
  var user_access_edit = $(".menu_ul_2_level li.active .menu_a_3_level").attr("user-access-edit");
  if(user_access_edit == undefined) {
    user_access_edit = $(".menu_ul_1_level li.active .menu_a_2_level").attr("user-access-edit");
  }
  if(user_access_edit == undefined) {
    user_access_edit = $("#menu li.active .menu_a_1_level").attr("user-access-edit");
  }
  if(user_access_edit == 0) {
    alert("You have no rights to insert or edit information!");
    $(".row_over").removeClass("row_over_edit");
    return false;
  }
  else return true;
  
}
  
function CheckDeleteRights() {
  
//  var user_id = $("#user_id").val();
//  if(user_id == "" || user_id == undefined) {
//    location.reload();
//    return false;
//  }
  var user_access_delete = $(".menu_ul_2_level li.active .menu_a_3_level").attr("user-access-delete");
  if(user_access_delete == undefined) {
    user_access_delete = $(".menu_ul_1_level li.active .menu_a_2_level").attr("user-access-delete");
  }
  if(user_access_delete == undefined) {
    user_access_delete = $("#menu li.active .menu_a_1_level").attr("user-access-delete");
  }
  if(user_access_delete == 0) {
    alert("You have no rights to delete information!");
    $(".row_over").removeClass("row_over_edit");
    return false;
  }
  else return true;
  
}

function PreloadImages() {
  var aImages = new Array('images/favorit-logo.png','images/headers-38x44x46x43x36.png','images/waterdrops.jpg','images/about.jpg');
  for(var i=0; i < aImages.length; i++) {
    var img = new Image();
    img.src = aImages[i];
  }
}

function CountCharacters(element,count) {
  var len = element.value.length;
  if (len >= count) {
    //disable entering more characters
    //element.value = element.value.substring(0, count);
    $(element).parent().find(".warning").show();
    $(element).parent().find(".info b").text(count - len);
  } else {
    $(element).parent().find(".warning").hide();
    $(element).parent().find(".info b").text(count - len);
  }
};
        
function EditUserJQ(user_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var areChecked = [];
  var i = 0;
  $.each($( ".details"+user_id+" input:checkbox[name=access]:checked" ), function(){    
      var tempArray = [];
      var j = 0;
      tempArray[j] = $(this).val();
      $.each($( ".details"+user_id+" .page"+$(this).val()+" input:checkbox[name=rights]" ), function(){
          j++;
          tempArray[j] = ($(this).is(':checked') ? 1 : 0);
      });
      areChecked[i] = tempArray;
      i++;
  });
  //alert(user_id);
  $.ajax({
  url:"/_admin/administration/ajax/edit/edit-user.php",
  type:"POST",
  data:{
    user_access:user_access,
    user_id:user_id,
    user_name:$("#user_username"+user_id).val(),
    user_password:$("#user_password"+user_id).val(),
    user_is_ip_in_use:($("#ip_in_use"+user_id).is(':checked') ? 1 : 0),
    user_is_active:($("#active"+user_id).is(':checked') ? 1 : 0),
    user_rights:areChecked
    }
  }).done(function(data){
    //alert(data);
    $(".row_over").removeClass("row_over_edit");
    $(".users_details").removeClass("access_rights_edit");
    if(data == "") {
      $("#user"+user_id+" td").effect("highlight", {}, 3000);
    }
    else {
      alert(data);
    }
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function AddEditUsersTypeDefaultRights(user_type_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var AllPagesRights = {};
  var i = 0;
  $.each($( ".single_page" ), function(){
      var menu_id = $(this).attr("data-id");
      var menu_access_was_checked = 0;
      if($(".page"+menu_id+" .menu_is_set").length) {
        menu_access_was_checked = $(".page"+menu_id+" .menu_is_set").val();
      }
      var rights_access = ($(".page"+menu_id+" .menu_access").hasClass('checkbox_checked') ? 1 : 0);
      var rights_edit = ($(".page"+menu_id+" .rights_edit").is(':checked') ? 1 : 0);
      var rights_delete = ($(".page"+menu_id+" .rights_delete").is(':checked') ? 1 : 0);
      var SinglePageRights = {};
      var key = "menu_id";
      SinglePageRights[key] = menu_id;
      key = "menu_access_was_checked";
      SinglePageRights[key] = menu_access_was_checked;
      key = "rights_access";
      SinglePageRights[key] = rights_access;
      key = "rights_edit";
      SinglePageRights[key] = rights_edit;
      key = "rights_delete";
      SinglePageRights[key] = rights_delete;
      AllPagesRights[i] = SinglePageRights;
      i++;
  });
  //alert(user_type_id);
  $.ajax({
  url:"/_admin/administration/ajax/edit/add-edit-users-type-default-rights.php",
  type:"POST",
  data:{
    user_access:user_access,
    user_type_id:user_type_id,
    user_rights:AllPagesRights
    }
  }).done(function(data){
    
    $("#right_column").append(data);
    location.href = "/_admin/administration/administration-users-types-rights.php";
   
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteUser() {
  var user_id = $(".delete_user_link.active").attr("data-id"); 
  if(user_id == "1" || user_id == "2") {
    alert($("#cannnot_delete_admin").val());
    return
  }
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(user_id);
  $.ajax({
  url:"/_admin/administration/ajax/delete/delete-user.php",
  type:"POST",
  data:{
    user_access:user_access,
    user_id:user_id
    }
  }).done(function(data){
    //alert(data);
    $("#modal_confirm").dialog("close");
    $("#users_list #user"+user_id).effect("highlight", {}, 1000);
    $("#users_list #user"+user_id).remove();
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function EditRestrictedUser(user_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var user_div = "#user"+user_id;
  var user_username = $(user_div+" .user_username").val();
  var user_firstname = $(user_div+" .user_firstname").val();
  var user_lastname = $(user_div+" .user_lastname").val();
  var user_password = $(user_div+" .user_password").val();
  //alert(task_group_name);
  $.ajax({
  url:"/_admin/administration/ajax/edit/edit-restricted-user.php",
  type:"POST",
  data:{
    user_access:user_access,
    user_id:user_id,
    user_username:user_username,
    user_password:user_password,
    user_firstname:user_firstname,
    user_lastname:user_lastname
    }
  }).done(function(data){
    $(".row_over").removeClass("row_over_edit");
    
    if(data == "") {
      $(user_div+" td").effect("highlight", {}, 3000);
    }
    else {
      alert(data);
    }
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function EditUserForReset(user_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  $.post("/_admin/administration/ajax/edit/edit-user-for-reset.php",{
      user_access:user_access,
      user_id:user_id,
      user_name:$("#user_username"+user_id).val(),
      user_password:$("#user_password"+user_id).val()
  }).done(function(data) {
      $(".row_over").removeClass("row_over_edit");
      $(".users_detail").removeClass("access_rights_edit");
      if(data == "") {
        $("#user"+user_id+" td").effect("highlight", {}, 3000);
      }
      else {
        alert(data);
      }
      $("#ajax_loader").hide();
  }).fail(function(data) {
        console.log("Error: "+data);
        alert("Error: "+data);
  });
    
}

function MoveUserTypeForwardBackward(user_type_id, user_type_sort_order, action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/administration/ajax/move-users-types-forward-backward.php",
  type:"POST",
  data:{
    user_access:user_access,
    user_type_id:user_type_id,
    user_type_sort_order:user_type_sort_order,
    action:action
    }
  }).done(function(users_types){
    
    $("#users_types_list").html(users_types);
    $("#tr_"+user_type_id+" td").effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetUsersForType() {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var user_type_id = $(".selected_user_type a").attr("data-id");
  var user_type = $(".selected_user_type a").html();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/administration/ajax/get/get-users-for-type.php",
  type:"POST",
  data:{
    user_access:user_access,
    user_type_id:user_type_id,
    user_type:user_type
    }
  }).done(function(data){
    
    $(".contents_options").show();
    $("#right_column").show();
    $("#users_list").html(data);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetUsersTypesDefaultRights() {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var user_type_id = $(".selected_user_type a").attr("data-id");
  var user_type = $(".selected_user_type a").html();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/administration/ajax/get/get-users-types-default-rights.php",
  type:"POST",
  data:{
    user_access:user_access,
    user_type_id:user_type_id,
    user_type:user_type
    }
  }).done(function(data){
    
    $(".users_type_default_rights").attr("onclick","AddEditUsersTypeDefaultRights('"+user_type_id+"')");
    $(".users_type_default_rights").show();
    $("#users_type_default_rights tbody").html(data);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetUserLog(user_id) {
  var user_access = GetUserAccess();
  var url = "/_admin/administration/ajax/get/get-user-log.php?user_id="+user_id+"&user_access="+user_access;
  window.open(url,'mywindow','status=no,location=yes,resizable=yes,scrollbars=yes,width=800,height=800,left=100,top=0,screenX=0,screenY=0');
}

function ResetIP(user_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  $.ajax({
  url:"/_admin/administration/ajax/edit/edit-user-ip.php",
  type:"POST",
  data:{
    user_access:user_access,
    user_id:user_id
    }
  }).done(function(data){
    $(".row_over").removeClass("row_over_edit");
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function AddMenuLink() {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var current_page = $("#current_page").val();
  var menu_parent_id = $("#add_menu #add_menu_parent_id").val();
  var menu_parent_level = $("#add_menu #add_menu_parent_id :selected").attr("level");
  var menu_has_children = ($("#add_menu #add_menu_has_children").is(":checked") ? "1" : "0");
  var menu_name = $("#add_menu #add_menu_name").val();
  var menu_url = $("#add_menu #add_menu_url").val();
  var menu_friendly_url = $("#add_menu #add_menu_friendly_url").val();
  var menu_path_name = $("#add_menu #add_menu_path_name").val();
  var menu_image_url = $("#add_menu #add_menu_image_url").val();
  var menu_sort_order = $("#add_menu #add_menu_sort_order").val();
  var menu_show_in_menu = ($("#add_menu #add_menu_show_in_menu").is(":checked") ? "1" : "0");
  var menu_is_active = ($("#add_menu #add_menu_is_active").is(":checked") ? "1" : "0");
  if(menu_name == "") {
    alert("Please enter menu name!");
    $("#ajax_loader").hide();
    return;
  }
  //alert(user_access);return;
  $.ajax({
  url:"/_admin/administration/ajax/add/add-menu-link.php",
  type:"POST",
  data:{
    user_access:user_access,
    menu_parent_id:menu_parent_id,
    menu_parent_level:menu_parent_level,
    menu_has_children:menu_has_children,
    menu_name:menu_name,
    menu_url:menu_url,
    menu_friendly_url:menu_friendly_url,
    menu_path_name:menu_path_name,
    menu_image_url:menu_image_url,
    menu_sort_order:menu_sort_order,
    menu_show_in_menu:menu_show_in_menu,
    menu_is_active:menu_is_active
    }
  }).done(function(){
    
    window.location = current_page;
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function EditMenuLink(menu_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var current_page = $("#current_page").val();
  var menu_div = "#menu"+menu_id;
  var menu_parent_id = $(menu_div+" .menu_parent_id").val();
  var menu_parent_level = $(menu_div+" .menu_parent_id :selected").attr("level");
  var menu_has_children = ($(menu_div+" .menu_has_children").is(":checked") ? "1" : "0");
  var menu_name = $(menu_div+" .menu_name").val();
  var menu_url = $(menu_div+" .menu_url").val();
  var menu_friendly_url = $(menu_div+" .menu_friendly_url").val();
  var menu_path_name = $(menu_div+" .menu_path_name").val();
  var menu_image_url = $(menu_div+" .menu_image_url").val();
  var menu_sort_order = $(menu_div+" .menu_sort_order").val();
  var menu_show_in_menu = ($(menu_div+" .menu_show_in_menu").is(":checked") ? "1" : "0");
  var menu_is_active = ($(menu_div+" .menu_is_active").is(":checked") ? "1" : "0");
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/administration/ajax/edit/edit-menu-link.php",
  type:"POST",
  data:{
    user_access:user_access,
    menu_id:menu_id,
    menu_parent_id:menu_parent_id,
    menu_parent_level:menu_parent_level,
    menu_has_children:menu_has_children,
    menu_name:menu_name,
    menu_url:menu_url,
    menu_friendly_url:menu_friendly_url,
    menu_path_name:menu_path_name,
    menu_image_url:menu_image_url,
    menu_sort_order:menu_sort_order,
    menu_show_in_menu:menu_show_in_menu,
    menu_is_active:menu_is_active
    }
  }).done(function(data){
    
    window.location = current_page;
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function ToggleMenuTranslation(button,menu_id) {
  if($(".menu_translation_row_"+menu_id).hasClass("active")) {
    $(".menu_translation_row_"+menu_id).removeClass("active");
    $(button).html("+");
  } else {
    $(".menu_translation_row_"+menu_id).addClass("active");
    $(button).html("-");
  }
}

function AddMenuTranslation(menu_id,language_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var menu_translation_tr = "#menu_translation_"+menu_id+language_id;
  var menu_translation_text = $(menu_translation_tr+" .menu_translation_text").val();
  if(menu_translation_text == "") {
    alert("Please enter a resort name!");
    return;
  }
  //alert(menu_translation_text);
  $.ajax({
  url:"/_admin/administration/ajax/add/add-menu-translation.php",
  type:"POST",
  data:{
    user_access:user_access,
    menu_id:menu_id,
    language_id:language_id,
    menu_translation_text:menu_translation_text
    }
  }).done(function(){
    //alert(data);
    $(".row_over").removeClass("row_over_edit");
    $(menu_translation_tr+" td").effect("highlight", {}, 3000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function EditMenuTranslation(menu_id,language_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var menu_translation_tr = "#menu_translation_"+menu_id+language_id;
  var menu_translation_text = $(menu_translation_tr+" .menu_translation_text").val();
  //alert(menu_translation_text);return;
  $.ajax({
  url:"/_admin/administration/ajax/edit/edit-menu-translation.php",
  type:"POST",
  data:{
    user_access:user_access,
    menu_id:menu_id,
    language_id:language_id,
    menu_translation_text:menu_translation_text
    }
  }).done(function(){
    $(".row_over").removeClass("row_over_edit");
    $(menu_translation_tr+" td").effect("highlight", {}, 3000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteMenuLink() {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var menu_id = $(".delete_menu_link.active").attr("data-id")
  var menu_div = "#menu"+menu_id;
  var current_page = $("#current_page").val();
  var menu_parent_id = $(menu_div+" .menu_parent_id").val();
  $.ajax({
  url:"/_admin/administration/ajax/delete/delete-menu-link.php",
  type:"POST",
  data:{
    user_access:user_access,
    menu_id:menu_id,
    menu_parent_id:menu_parent_id
    }
  }).done(function(data){

    $("#modal_confirm").dialog("close");
    if(data == "") {
      window.location = current_page;
    }
    else {
      alert(data);
    }

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetMenuLinkChildren(level) {
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var prev_level = parseInt(level)-1;
  var menu_id = $(".selected_menu_link_level_"+prev_level+" a").attr("data");
  var menu_name = $(".selected_menu_link_level_"+prev_level+" a").html();
  //alert(prev_level);return false;
  $.ajax({
  url:"/_admin/administration/ajax/get/get-menu-link-children.php",
  type:"POST",
  data:{
    user_access:user_access,
    menu_id:menu_id,
    menu_name:menu_name,
    level:level
    }
  }).done(function(data){
    if(data == "") {
      // if there are no children then do nothing
    }
    else {
      $("#menu_links_level_"+level).html(data);
    }
    GetMenuLinkNote(menu_id);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetMenuLinkNote(menu_id) {
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var language_id = $(".selected_language a").attr("data");
  if(menu_id == undefined) {
    menu_id = $(".selected_menu_link_level_2 a").attr("data");
  }
  if(menu_id == undefined) {
    menu_id = $(".selected_menu_link_level_1 a").attr("data");
  }
  if(menu_id == undefined) {
    menu_id = $(".selected_menu_link_level_0 a").attr("data");
  }
  //alert(part_id);return false;
  $.ajax({
  url:"/_admin/administration/ajax/get/get-menu-link-note.php",
  type:"POST",
  data:{
    user_access:user_access,
    menu_id:menu_id,
    language_id:language_id
    }
  }).done(function(data){
    $("#add_new_menu_link_note").html("");
    $("#menu_link_note").html(data);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function AddMenuLinkNote() {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var language_id = $(".selected_language a").attr("data");
  var menu_id = $(".selected_menu_link_level_2 a").attr("data");
  var menu_link_note = $("#add_menu_link_note").val();
  if(menu_id == undefined) {
    menu_id = $(".selected_menu_link_level_1 a").attr("data");
  }
  if(menu_id == undefined) {
    menu_id = $(".selected_menu_link_level_0 a").attr("data");
  }
  //alert(menu_link_note);return;
  $.ajax({
  url:"/_admin/administration/ajax/add/add-menu-link-note.php",
  type:"POST",
  data:{
    user_access:user_access,
    menu_id:menu_id,
    language_id:language_id,
    menu_link_note:menu_link_note
    }
  }).done(function(data){
    //alert(data);
    GetMenuLinkNote(menu_id);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function EditMenuLinkNote(menu_id,language_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var menu_link_note_tr = "#menu_link_note"+menu_id+language_id;
  var menu_link_note = $(menu_link_note_tr+" .menu_link_note").val();
  //alert(news_feed_is_web);return;
  $.ajax({
  url:"/_admin/administration/ajax/edit/edit-menu-link-note.php",
  type:"POST",
  data:{
    user_access:user_access,
    menu_id:menu_id,
    language_id:language_id,
    menu_link_note:menu_link_note
    }
  }).done(function(data){
    //alert(data);
    $(".row_over").removeClass("row_over_edit");
    $(menu_link_note_tr+" td").effect("highlight", {}, 3000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteMenuLinkNote(menu_id,language_id) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var answer = confirm('Are you sure you want to delete this news feed?');
  if(answer) {
    $.ajax({
    url:"/_admin/administration/ajax/delete/delete-menu-link-note.php",
    type:"POST",
    data:{
      user_access:user_access,
      menu_id:menu_id,
      language_id:language_id
      }
    }).done(function(){
      //alert(data);
      $("div#menu_link_note"+menu_id+language_id).remove();
      $("#ajax_loader").hide();
    }).fail(function(error){
      console.log(error);
    })
  }
  else {
    $("#ajax_loader").hide();
    return;
  }
}

function DeleteCustomer() {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var customer_id = $(".delete_customer_link.active").attr("data-id");
  $.ajax({
  url:"/_admin/sales/ajax/delete/delete-customer.php",
  type:"POST",
  data:{
    user_access:user_access,
    customer_id:customer_id
    }
  }).done(function(){
    //alert(data);
    $("#modal_confirm").dialog("close");
    $("div#customer_"+customer_id).remove();
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function EditCustomer(customer_id) {
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var customer_group_id = $("#customer_"+customer_id+" .select_customer_group_id").val();
  var customer_is_in_mailist = ($("#customer_"+customer_id+" .customer_is_in_mailist").is(":checked") ? 1 : 0);
  var customer_is_blocked = ($("#customer_"+customer_id+" .customer_is_blocked").is(":checked") ? 1 : 0);
  var customer_is_active = ($("#customer_"+customer_id+" .customer_is_active").is(":checked") ? 1 : 0);
  $.ajax({
  url:"/_admin/sales/ajax/edit/edit-customer.php",
  type:"POST",
  data:{
    user_access:user_access,
    customer_id:customer_id,
    customer_group_id:customer_group_id,
    customer_is_in_mailist:customer_is_in_mailist,
    customer_is_blocked:customer_is_blocked,
    customer_is_active:customer_is_active
    }
  }).done(function(){
    //alert(data);
    $(".row_over").removeClass("row_over_edit");
    $("#customer_"+customer_id+" td").effect("highlight", {}, 3000);
//    var massage = $("#ajaxmessage_update_product_tab_success").val();
//    $("#ajax_notification .ajaxmessage").html(massage);
//    $("#ajax_notification").slideDown(500);
//    $("#ajax_notification").delay(3500).slideUp(900);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function ToggleCustomerDetails(customer_id) {
  if($("#customer_details_"+customer_id).hasClass("active")) {
    $("#customer_details_"+customer_id).removeClass("active");
    $("#customer_details_"+customer_id).slideUp();
    $("#customer_"+customer_id+" .toggle_user_details").html("&plus;");
    $("#customer_"+customer_id+" tr").addClass("hover");
  }
  else {
    $("#customer_details_"+customer_id).addClass("active");
    $("#customer_details_"+customer_id).slideDown();
    $("#customer_"+customer_id+" .toggle_user_details").html("&minus;");
    $("#customer_"+customer_id+" tr").removeClass("hover");
  }
}
        
function GetCustomerLog(customer_id) {
  var user_access = GetUserAccess();
  var url = "/_admin/sales/ajax/get/get-customer-log.php?customer_id="+customer_id+"&user_access="+user_access;
  window.open(url,'mywindow','status=no,location=yes,resizable=yes,scrollbars=yes,width=800,height=800,left=100,top=0,screenX=0,screenY=0');
}

function AddCustomerGroup() {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var customer_group_name = $("#add_customer_group_name").val();
  var customer_group_sort_order = $("#add_customer_group_sort_order").val();
  //alert(menu_link_note);return;
  $.ajax({
  url:"/_admin/sales/ajax/add/add-customer-group.php",
  type:"POST",
  data:{
    user_access:user_access,
    customer_group_name:customer_group_name,
    customer_group_sort_order:customer_group_sort_order
    }
  }).done(function(){
    //alert($("#current_page").val());
   
    window.location = base_url+$("#current_page").val();
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function EditCustomerGroup(customer_group_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var customer_group_div = "#customer_group_"+customer_group_id;
  var customer_group_name = $(customer_group_div+" .customer_group_name").val();
  var customer_group_sort_order = $(customer_group_div+" .customer_group_sort_order").val();
  //alert(menu_translation_text);return;
  $.ajax({
  url:"/_admin/sales/ajax/edit/edit-customer-group.php",
  type:"POST",
  data:{
    user_access:user_access,
    customer_group_id:customer_group_id,
    customer_group_name:customer_group_name,
    customer_group_sort_order:customer_group_sort_order
    }
  }).done(function(){
    $(".row_over").removeClass("row_over_edit");
    $(customer_group_div+" td").effect("highlight", {}, 3000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteCustomerGroup() {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var customer_group_id = $(".delete_customer_group_link.active").attr("data-id");
  $.ajax({
  url:"/_admin/sales/ajax/delete/delete-customer-group.php",
  type:"POST",
  data:{
    user_access:user_access,
    customer_group_id:customer_group_id
    }
  }).done(function(){
    //alert(data);
    $("#modal_confirm").dialog("close");
    $("div#customer_group_"+customer_group_id).remove();
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function ToggleCustomerGroupTranslation(button,customer_group_id) {
  if($(".customer_group_translation_row_"+customer_group_id).hasClass("active")) {
    $(".customer_group_translation_row_"+customer_group_id).removeClass("active");
    $(".customer_group_translation_row_"+customer_group_id).hide();
    $(button).html("+");
  } else {
    $(".customer_group_translation_row_"+customer_group_id).addClass("active");
    $(".customer_group_translation_row_"+customer_group_id).show();
    $(button).html("-");
  }
}

function AddCustomerGroupTranslation(customer_group_id,language_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var customer_group_translation_tr = "#customer_group_translation_"+customer_group_id+language_id;
  var customer_group_translation_text = $(customer_group_translation_tr+" .customer_group_translation_text").val();
  if(customer_group_translation_text == "") {
    alert("Please enter a resort name!");
    return;
  }
  //alert(customer_group_translation_text);
  $.ajax({
  url:"/_admin/sales/ajax/add/add-customer-group-translation.php",
  type:"POST",
  data:{
    user_access:user_access,
    customer_group_id:customer_group_id,
    language_id:language_id,
    customer_group_translation_text:customer_group_translation_text
    }
  }).done(function(){
    //alert(data);
    $(".row_over").removeClass("row_over_edit");
    $(customer_group_translation_tr+" td").effect("highlight", {}, 3000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function EditCustomerGroupTranslation(customer_group_id,language_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var customer_group_translation_tr = "#customer_group_translation_"+customer_group_id+language_id;
  var customer_group_translation_text = $(customer_group_translation_tr+" .customer_group_translation_text").val();
  //alert(customer_group_translation_text);return;
  $.ajax({
  url:"/_admin/sales/ajax/edit/edit-customer-group-translation.php",
  type:"POST",
  data:{
    user_access:user_access,
    customer_group_id:customer_group_id,
    language_id:language_id,
    customer_group_translation_text:customer_group_translation_text
    }
  }).done(function(){
    $(".row_over").removeClass("row_over_edit");
    $(customer_group_translation_tr+" td").effect("highlight", {}, 3000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function ToggleExpandContent(content_id, action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/content/ajax/edit/toggle-expand-content.php",
  type:"POST",
  data:{
    user_access:user_access,
    content_id:content_id,
    action:action
    }
  }).done(function(contents){
    
    $("#contents_list").html(contents);
    $("#tr_"+content_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function SetContentActiveInactive(link,content_id, set_content) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/content/ajax/edit/set-content-active-inactive.php",
  type:"POST",
  data:{
    user_access:user_access,
    content_id:content_id,
    set_content:set_content
    }
  }).done(function(){
    
    var img_active = $(".images_act_inact .img_active");
    var img_inactive = $(".images_act_inact .img_inactive");
    
    if(set_content == "0") {
      $(link).attr("onClick","SetContentActiveInactive(this,'"+content_id+"','1')");
      $(link).html(img_inactive);
    }
    else {
      $(link).attr("onClick","SetContentActiveInactive(this,'"+content_id+"','0')");
      $(link).html(img_active);
    }
    
    $("#tr_"+content_id).effect("highlight", {}, 1000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function SetNewsActiveInactive(link,news_id, set_news) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/news/ajax/edit/set-news-active-inactive.php",
  type:"POST",
  data:{
    user_access:user_access,
    news_id:news_id,
    set_news:set_news
    }
  }).done(function(){
    
    var img_active = $(".images_act_inact .img_active");
    var img_inactive = $(".images_act_inact .img_inactive");
    
    if(set_news == "0") {
      $(link).attr("onClick","SetNewsActiveInactive(this,'"+news_id+"','1')");
      $(link).html(img_inactive);
    }
    else {
      $(link).attr("onClick","SetNewsActiveInactive(this,'"+news_id+"','0')");
      $(link).html(img_active);
    }
    
    $("#tr_"+news_id).effect("highlight", {}, 1000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function SetContentAsHomePage(content_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/content/ajax/edit/set-content-as-home-page.php",
  type:"POST",
  data:{
    user_access:user_access,
    content_id:content_id
    }
  }).done(function(contents){
    
    $("#contents_list").html(contents);
    $("#tr_"+content_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function SetContentDefault(content_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/content/ajax/edit/set-content-default.php",
  type:"POST",
  data:{
    user_access:user_access,
    content_id:content_id
    }
  }).done(function(contents){
    
    $("#contents_list").html(contents);
    $("#tr_"+content_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function MoveContentForwardBackward(content_id, content_parent_id, content_menu_order, content_hierarchy_level, action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/content/ajax/edit/move-content-forward-backward.php",
  type:"POST",
  data:{
    user_access:user_access,
    content_id:content_id,
    content_parent_id:content_parent_id,
    content_menu_order:content_menu_order,
    content_hierarchy_level:content_hierarchy_level,
    action:action
    }
  }).done(function(contents){
    
    $("#contents_list").html(contents);
    $("#tr_"+content_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteContent() {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var content_id = $(".delete_content_link.active").attr("data-id");
  var content_parent_id = $(".delete_content_link.active").attr("data-parent");
  $.ajax({
  url:"/_admin/content/ajax/delete/delete-content.php",
  type:"POST",
  data:{
    user_access:user_access,
    content_id:content_id,
    content_parent_id:content_parent_id
    }
  }).done(function(contents){

    $("#modal_confirm").dialog("close");
    $("#contents_list").html(contents);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function ReorderContent() {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var contents_in_li = [];
  var i = 0;
  $.each($( "#reorder_pages li" ), function(){  
      contents_in_li[i] = $(this).attr("data-id");
      i++;
  });
  $.ajax({
  url:"/_admin/test.php",
//  url:"/_admin/content/ajax/test.php",
  type:"POST",
  data:{
    user_access:user_access,
    contents_in_li:contents_in_li
    }
  }).done(function(contents){

    //$("#contents_list").html(contents);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function SetLanguageActiveInactive(link,language_id, set_language) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/language/ajax/set-language-active-inactive.php",
  type:"POST",
  data:{
    user_access:user_access,
    language_id:language_id,
    set_language:set_language
    }
  }).done(function(){
    
    var img_active = $(".images_act_inact .img_active");
    var img_inactive = $(".images_act_inact .img_inactive");
    
    if(set_language == "0") {
      $(link).attr("onClick","SetLanguageActiveInactive(this,'"+language_id+"','1')");
      $(link).html(img_inactive);
    }
    else {
      $(link).attr("onClick","SetLanguageActiveInactive(this,'"+language_id+"','0')");
      $(link).html(img_active);
    }
    
    $("#tr_"+language_id).effect("highlight", {}, 1000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function MoveLanguageForwardBackward(language_id, language_menu_order, action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/language/ajax/move-language-forward-backward.php",
  type:"POST",
  data:{
    user_access:user_access,
    language_id:language_id,
    language_menu_order:language_menu_order,
    action:action
    }
  }).done(function(languages){
    
    $("#languages_list").html(languages);
    $("#tr_"+language_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function SetCourseActiveInactive(link,course_id, set_course) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/courses/ajax/edit/set-course-active-inactive.php",
  type:"POST",
  data:{
    user_access:user_access,
    course_id:course_id,
    set_course:set_course
    }
  }).done(function(){
    
    var img_active = $(".images_act_inact .img_active");
    var img_inactive = $(".images_act_inact .img_inactive");
    
    if(set_course == "0") {
      $(link).attr("onClick","SetCourseActiveInactive(this,'"+course_id+"','1')");
      $(link).html(img_inactive);
    }
    else {
      $(link).attr("onClick","SetCourseActiveInactive(this,'"+course_id+"','0')");
      $(link).html(img_active);
    }
    
    $("#tr_"+course_id).effect("highlight", {}, 1000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetCourseImage(course_id) {
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  $.ajax({
  url:"/_admin/courses/ajax/get/get-course-image.php",
  type:"POST",
  data:{
    course_id:course_id
    }
  }).done(function(current_image){

    $("#current_image").html(current_image);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function SetEventActiveInactive(link,event_id, set_event) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/events/ajax/edit/set-event-active-inactive.php",
  type:"POST",
  data:{
    user_access:user_access,
    event_id:event_id,
    set_event:set_event
    }
  }).done(function(){
    
    var img_active = $(".images_act_inact .img_active");
    var img_inactive = $(".images_act_inact .img_inactive");
    
    if(set_event == "0") {
      $(link).attr("onClick","SetEventActiveInactive(this,'"+event_id+"','1')");
      $(link).html(img_inactive);
    }
    else {
      $(link).attr("onClick","SetEventActiveInactive(this,'"+event_id+"','0')");
      $(link).html(img_active);
    }
    
    $("#tr_"+event_id).effect("highlight", {}, 1000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetEventImage(event_id) {
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  $.ajax({
  url:"/_admin/events/ajax/get/get-event-image.php",
  type:"POST",
  data:{
    event_id:event_id
    }
  }).done(function(current_image){

    $("#current_image").html(current_image);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function SetPositionCoords() {
  var event_map_address = $("#event_map_address").val();
  if(event_map_address == "") {
    alert("Address coulnd't be empy!"); return;
  }
  else {
    var address = event_map_address.toLowerCase().replace(/\s+/g, ' ').replace(/^\s+|\s+$/g, '');
    var geocoder = new google.maps.Geocoder();
    if (address.length != 0) {
        geocoder.geocode({ address: address, region: 'BG' }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK && status != google.maps.GeocoderStatus.ZERO_RESULTS) {
              
                var geometry = results[0].geometry; //console.log(results[0]);
                var latitude = geometry.location.lat();
                var longitude = geometry.location.lng();

                if(latitude == "" || longitude == "") {
                  alert("Google Maps could not find a location matching entry with the provided address, city and state");
                  return false;
                }
                else {
                  $("#event_map_lat").val(latitude);
                  $("#event_map_lng").val(longitude);
                }
                
            } else {
                alert("Google Maps could not find a location matching entry with the provided address, city and state");
                return false;
            }
        });
    } else {
        alert("Address coulnd't be empy!"); return;
        return false;
    }
  }
}

function MoveInstructorForwardBackward(team_member_id, team_member_sort_order, action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/team_members/ajax/move-team_member-forward-backward.php",
  type:"POST",
  data:{
    user_access:user_access,
    team_member_id:team_member_id,
    team_member_sort_order:team_member_sort_order,
    action:action
    }
  }).done(function(team_members){
    
    $("#team_members_list").html(team_members);
    $("#tr_"+team_member_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function SetLanguageDefault(language_id,default_for) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/language/ajax/set-language-default.php",
  type:"POST",
  data:{
    user_access:user_access,
    language_id:language_id,
    default_for:default_for
    }
  }).done(function(languages){
    
    $("#languages_list").html(languages);
    $("#tr_"+language_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteLanguage(step) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var language_id = $(".delete_language_link.active").attr("data-id");
  $.ajax({
  url:"/_admin/language/ajax/delete/delete-language.php",
  type:"POST",
  data:{
    user_access:user_access,
    language_id:language_id,
    step:step
    }
  }).done(function(languages){

    if(step == "first") {
      $("#modal_confirm").dialog("close");
    }
    else {
      $("#modal_confirm_delete_language").dialog("close");
    }
    $("#languages_list").html(languages);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function ToggleExpandCategory(category_id, action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/catalog/ajax/toggle-expand-category.php",
  type:"POST",
  data:{
    user_access:user_access,
    category_id:category_id,
    action:action
    }
  }).done(function(categories){
    
    $("#categories_list").html(categories);
    $("#tr_"+category_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function SetCategoryActiveInactive(link,category_id, set_category) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/catalog/ajax/set-category-active-inactive.php",
  type:"POST",
  data:{
    user_access:user_access,
    category_id:category_id,
    set_category:set_category
    }
  }).done(function(){
    
    var img_active = $(".images_act_inact .img_active");
    var img_inactive = $(".images_act_inact .img_inactive");
    
    if(set_category == "0") {
      $(link).attr("onClick","SetCategoryActiveInactive(this,'"+category_id+"','1')");
      $(link).html(img_inactive);
    }
    else {
      $(link).attr("onClick","SetCategoryActiveInactive(this,'"+category_id+"','0')");
      $(link).html(img_active);
    }
    
    $("#tr_"+category_id).effect("highlight", {}, 1000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function MoveCategoryForwardBackward(category_id, category_parent_id, category_sort_order, category_hierarchy_level, action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(category_id);return;
  $.ajax({
  url:"/_admin/catalog/ajax/move-category-forward-backward.php",
  type:"POST",
  data:{
    user_access:user_access,
    category_id:category_id,
    category_parent_id:category_parent_id,
    category_sort_order:category_sort_order,
    category_hierarchy_level:category_hierarchy_level,
    action:action
    }
  }).done(function(categories){
    
    $("#categories_list").html(categories);
    $("#tr_"+category_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteCategory() {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var category_id = $(".delete_category_link.active").attr("data-id");
  var category_parent_id = $(".delete_category_link.active").attr("data-parent");
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-category.php",
  type:"POST",
  data:{
    user_access:user_access,
    category_id:category_id,
    category_parent_id:category_parent_id
    }
  }).done(function(categories){

    $("#modal_confirm").dialog("close");
    $("#categories_list").html(categories);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteCategoryImage(category_id) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var category_image_path = $("#category_image_path").val();
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-category-image.php",
  type:"POST",
  data:{
    user_access:user_access,
    category_id:category_id,
    category_image_path:category_image_path
    }
  }).done(function(){

    $(".category_image_div").remove();
    $(".category_image_file").removeClass("hidden");

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetSubcategories(category_id,current_category_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var category_ids_list = $("#link_"+category_id).attr("data-ids");
//  var category_id = $("#link_"+category_id).attr("data-id");
  var category_name = $("#link_"+category_id).html();
  var category_hierarchy_level = $("#link_"+category_id).attr("data-h-level");
  $(".contents_options").hide();
  $("#right_column").hide();
  $.each($("#left_column .list_container"), function(){     
      var level = $(this).attr("level");
      if(level > category_hierarchy_level) $("#left_column .level_"+level).remove();
  });
  $("#left_column .level_"+category_hierarchy_level+" td").removeClass("active");
  $("#link_"+category_id).parent().addClass("active");
  //alert(attribute_group_id);return;
  $.ajax({
  url:"/_admin/catalog/ajax/get/get-subcategories-by-category.php",
  type:"POST",
  data:{
    user_access:user_access,
    category_ids_list:category_ids_list,
    category_id:category_id,
    category_name:category_name,
    category_hierarchy_level:category_hierarchy_level
    }
  }).done(function(categories){
    
    if(category_hierarchy_level > 1) {
      $("#left_column #subcategories").append(categories);
    }
    else {
      $("#left_column #subcategories").html(categories);
    }
    if(current_category_id != 0) {
      GetProductsByCategory(current_category_id);
    }
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetProductsByCategory(category_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var category_ids_list = $("#link_"+category_id).attr("data-ids");
//  var category_id = $("#link_"+category_id).attr("data-id");
  var category_name = $("#link_"+category_id).html();
  var category_hierarchy_level = $("#link_"+category_id).attr("data-h-level");
  if(category_hierarchy_level == 1) {
    $("#subcategories").html("");
  }
  $("#left_column .level_"+category_hierarchy_level+" td").removeClass("active");
  $("#link_"+category_id).parent().addClass("active");
  //alert(attribute_group_id);return;
  $.ajax({
  url:"/_admin/catalog/ajax/get/get-products-by-category.php",
  type:"POST",
  data:{
    user_access:user_access,
    category_ids_list:category_ids_list,
    category_id:category_id,
    category_name:category_name,
    category_hierarchy_level:category_hierarchy_level
    }
  }).done(function(products){
    
    $(".contents_options").show();
    $(".contents_options .pageoptions").attr("href","/_admin/catalog/products-add-new.php?category_ids_list="+category_ids_list+"&category_id="+category_id+"&category_name="+category_name);
    $("#right_column").show();
    $("#products_list").html(products);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function SetProductActiveInactive(link,product_id, set_product) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/catalog/ajax/edit/set-product-active-inactive.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_id:product_id,
    set_product:set_product
    }
  }).done(function(){
    
    var img_active = $(".images_act_inact .img_active");
    var img_inactive = $(".images_act_inact .img_inactive");
    
    if(set_product == "0") {
      $(link).attr("onClick","SetProductActiveInactive(this,'"+product_id+"','1')");
      $(link).html(img_inactive);
    }
    else {
      $(link).attr("onClick","SetProductActiveInactive(this,'"+product_id+"','0')");
      $(link).html(img_active);
    }
    
    $("#tr_"+product_id).effect("highlight", {}, 1000);
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function MoveProductForwardBackward(product_id, category_id, product_sort_order, action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(category_id);return;
  $.ajax({
  url:"/_admin/catalog/ajax/edit/move-product-forward-backward.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_id:product_id,
    category_id:category_id,
    product_sort_order:product_sort_order,
    action:action
    }
  }).done(function(products_list){
    
    $("#products_list").html(products_list);
    $("#tr_"+product_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteProduct(page) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var product_id = $(".delete_product_link.active").attr("data-id");
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-product.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_id:product_id
    }
  }).done(function(){

    $("#modal_confirm").dialog("close");
    
    if(page == "details") {
      window.location = $(".back_btn").attr("href");
    }
    else {
      $("#products_list #tr_"+product_id).remove();
    }
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteProductsAfterImportFromExcel() {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var products_ids_list = $("#new_inserted_products_ids").val();
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-products-after-import-from-excel.php",
  type:"POST",
  data:{
    user_access:user_access,
    products_ids_list:products_ids_list
    }
  }).done(function(){
    
    $("#modal_confirm_delete_products_after_import").dialog("close");
    var massage = $("#ajaxmessage_delete_products_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);
    $("#delete_products_after_import").remove()

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function EditProductMainTab(clicked_tab) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var product_id = $("#product_id").val();
  var language_ids = [];
  var pd_names = [];
  var pd_news_urls = [];
  var pd_meta_titles = [];
  var pd_meta_keywords = [];
  var pd_meta_descriptions = [];
  var pd_descriptions = [];
  var pd_overviews = [];
  var pd_novations = [];
  var pd_system_requirements = [];
  var pd_downloads = [];
  var i = 0;
  $.each($(clicked_tab+" .language_tab"), function(){     
      language_ids[i] = $(this).attr("data-id");
      pd_names[i] = $("#pd_name_"+language_ids[i]).val();
      pd_news_urls[i] = $("#pd_news_url_"+language_ids[i]).val();
      pd_meta_titles[i] = $("#pd_meta_title_"+language_ids[i]).val();
      pd_meta_keywords[i] = $("#pd_meta_keywords_"+language_ids[i]).val();
      pd_meta_descriptions[i] = $("#pd_meta_description_"+language_ids[i]).val();
      pd_descriptions[i] = CKEDITOR.instances["pd_description["+language_ids[i]+"]"].getData();
      pd_overviews[i] = CKEDITOR.instances["pd_overview["+language_ids[i]+"]"].getData();
      pd_novations[i] = CKEDITOR.instances["pd_novations["+language_ids[i]+"]"].getData();
      pd_system_requirements[i] = CKEDITOR.instances["pd_system_requirements["+language_ids[i]+"]"].getData();
      pd_downloads[i] = CKEDITOR.instances["pd_downloads["+language_ids[i]+"]"].getData();
      //alert(pd_names[i]);
      i++;
  });
  var product_trial_url = $("#product_trial_url").val();
  var is_there_old_categories_list = $("#is_there_old_categories_list").val();
  var old_categories_list = $("#old_categories_list").val();
  var new_categories_ids = $("#new_categories_ids").val();
  var product_is_active = ($("#product_is_active").is(":checked")) ? "1" : "0";
  //alert(clicked_tab);return;
  $.ajax({
  url:"/_admin/catalog/ajax/edit/edit-product-main-tab.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_id:product_id,
    language_ids:language_ids,
    pd_names:pd_names,
    pd_meta_titles:pd_meta_titles,
    pd_meta_keywords:pd_meta_keywords,
    pd_meta_descriptions:pd_meta_descriptions,
    pd_descriptions:pd_descriptions,
    pd_overviews:pd_overviews,
    pd_novations:pd_novations,
    pd_system_requirements:pd_system_requirements,
    pd_downloads:pd_downloads,
    pd_news_urls:pd_news_urls,
    product_trial_url:product_trial_url,
    is_there_old_categories_list:is_there_old_categories_list,
    old_categories_list:old_categories_list,
    new_categories_ids:new_categories_ids,
    product_is_active:product_is_active
    }
  }).done(function(){
    
    $("#edit_product").effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_product_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function EditProductDataTab() {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var product_id = $("#product_id").val();
  var product_isbn = $("#product_isbn").val();
  var product_model = $("#product_model").val();
  var product_price = $("#product_price").val();
  var product_quantity = $("#product_quantity").val();
  var product_minimum = $("#product_minimum").val();
  var product_subtract = $("#product_subtract").val();
  var product_weight = $("#product_weight").val();
  var weight_class_id = $("#select_product_weight_class_id").val();
  var product_width = $("#product_width").val();
  var product_height = $("#product_height").val();
  var product_length = $("#product_length").val();
  var length_class_id = $("#select_product_length_class_id").val();
  var product_is_active = ($("#product_is_active").is(":checked")) ? "1" : "0";
  var product_date_available = $("#product_date_available").val();
  var stock_status_id = $("#select_product_stock_status_id").val();
  var is_there_old_categories_list = $("#is_there_old_categories_list").val();
  var old_categories_list = $("#old_categories_list").val();
  var new_categories_ids = $("#new_categories_ids").val();
  //alert(clicked_tab);return;
  $.ajax({
  url:"/_admin/catalog/ajax/edit/edit-product-data-tab.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_id:product_id,
    product_isbn:product_isbn,
    product_model:product_model,
    product_price:product_price,
    product_quantity:product_quantity,
    product_minimum:product_minimum,
    product_subtract:product_subtract,
    product_weight:product_weight,
    weight_class_id:weight_class_id,
    product_width:product_width,
    product_height:product_height,
    product_length:product_length,
    length_class_id:length_class_id,
    product_is_active:product_is_active,
    product_date_available:product_date_available,
    stock_status_id:stock_status_id,
    is_there_old_categories_list:is_there_old_categories_list,
    old_categories_list:old_categories_list,
    new_categories_ids:new_categories_ids
    }
  }).done(function(){
    
    $("#edit_product").effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_product_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function ShowOneMoreImagesInput(max_images) {
  var current_input_id = $("#more_product_images_id").val();
  if($("input.product_image_file").length == max_images) {
    alert("You can't upload more then "+max_images+" gallery pictures");
    return;
  }
  $("#more_gal_imgs_container").append('<p  id="product_image_'+current_input_id+'"><input type="file" name="product_image[]" class="product_image_file" style="width: auto;" />&nbsp;<a onclick="RemoveProductImageRow('+current_input_id+')"><img src="/_admin/images/delete.gif" class="systemicon" alt="'+$("#alt_delete").val()+'" title="'+$("#alt_delete").val()+'" width="16" height="16" /></a></p>');
  $("#product_image_"+current_input_id).show();
  var next_input_id = (parseInt(current_input_id) + 1);
  $("#more_product_images_id").val(next_input_id);
}

function RemoveProductImageRow(current_input_id) {
  $("#product_image_"+current_input_id).remove();
}

function EditProductImagesTab() {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var product_id = $("#product_id").val();
  var product_image_ids = [];
  var i = 0;
  $.each($(".ui-state-default"), function(){     
      product_image_ids[i] = $(this).attr("data-id");
      i++;
  });
  //alert(clicked_tab);return;
  $.ajax({
  url:"/_admin/catalog/ajax/edit/edit-product-images-tab.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_id:product_id,
    product_image_ids:product_image_ids
    }
  }).done(function(){
    
    $("#edit_product").effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_product_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetProductImages(product_id) {
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  $.ajax({
  url:"/_admin/catalog/ajax/get/get-product-images.php",
  type:"POST",
  data:{
    product_id:product_id
    }
  }).done(function(product_images){

    $("#product_images_tab").html(product_images);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function EditProductAttributesTab() {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var product_id = $("#product_id").val();
  var product_attributes = {};
  $.each($(".product_attribute_row"), function(){
      var row_key = $(this).attr("row-key");
      if(!product_attributes[row_key]) {
          product_attributes[row_key] = {};  
      }
      product_attributes[row_key]["attribute_id"] = $(this).find("select[name='product_attribute["+row_key+"][attribute_id]']").val();
      $.each($(".existing_language"), function(){  
        var language_id = $(this).attr("data-id");
        if(!product_attributes[row_key]["product_attribute_values"]) {
            product_attributes[row_key]["product_attribute_values"] = {};        
        }
        if(!product_attributes[row_key]["product_attribute_values"][language_id]) {
            product_attributes[row_key]["product_attribute_values"][language_id] = {};        
        }
        product_attributes[row_key]["product_attribute_values"][language_id]["product_attribute_id"] = $("textarea[name='product_attribute_value["+row_key+"]["+language_id+"]").attr("data-id");
        product_attributes[row_key]["product_attribute_values"][language_id]["value"] = $("textarea[name='product_attribute_value["+row_key+"]["+language_id+"]").val();
         //console.log(product_attributes[row_key]["product_option_values"]);
      });
      //console.log(product_attributes[row_key]);
  });
  //alert(clicked_tab);return;
  $.ajax({
  url:"/_admin/catalog/ajax/edit/edit-product-attributes-tab.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_id:product_id,
    product_attributes:product_attributes
    }
  }).done(function(){
    
    $("#edit_product").effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_product_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);
    
    GetProductAttributes(product_id);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetProductAttributes(product_id) {
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var category_id = $("#category_id").val();
  var language_id = $("#language_id").val();
  $.ajax({
  url:"/_admin/catalog/ajax/get/get-product-attributes.php",
  type:"POST",
  data:{
    product_id:product_id,
    category_id:category_id,
    language_id:language_id
    }
  }).done(function(product_attributes){

    $("#product_attributes_tab").html(product_attributes);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function AddProductAttributeRow() {
  var product_attribute_row = $(".product_attributes_count").val();

  var html  = '<tbody class="product_attribute_row_'+product_attribute_row+' product_attribute_row" data-id="new_entry" row-key="'+product_attribute_row+'">';
  html += '  <tr>';	
  html += '    <td class="text_left">';
  html += '    <select name="product_attribute['+product_attribute_row+'][attribute_id]">';
  html += $(".attributes_select").html();
  html += '    </select></td>';
  html += '    <td class="text_left">';
  $.each($(".existing_language"), function(){  
      var language_id = $(this).attr("data-id");
      var language_code = $(this).attr("data-code");
      var language_name = $(this).attr("data-name");
      html += '<textarea name="product_attribute_value['+product_attribute_row+']['+language_id+']" data-id="new_entry" class="product_attribute_value"></textarea>';
      html += '&nbsp;&nbsp;<img src="/_admin/images/flags/'+language_code+'.png" title="'+language_name+'"><p class="clearfix"></p>';
  });
  html += '    </td>';
  html += '    <td><a onclick="RemoveProductAttributeRow(\''+product_attribute_row+'\')" class="button red">'+$("#text_btn_delete").val()+'</a></td>';
  html += '  </tr>';	
  html += '</tbody>';

  $("#product_attributes_tab table tfoot").before(html);

  product_attribute_row++;
  $("#product_attributes_tab .product_attributes_count").val(product_attribute_row);
}
    
function RemoveProductAttributeRow(product_attribute_row) {
  $("#product_attributes_tab .product_attribute_row_"+product_attribute_row).remove();
}

function DeleteProductAttributeValue(attribute_id) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var product_id = $("#product_id").val();
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-product-attribute.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_id:product_id,
    attribute_id:attribute_id
    }
  }).done(function(){

    $("#modal_confirm_delete_product_attribute").dialog("close");
    $("#product_attribute_"+attribute_id).remove();
    var massage = $("#ajaxmessage_delete_product_attribute_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function AddProductOptionValue(option_key) {
  var option_div = "#option_"+option_key;
  var product_option_value_row = $(option_div+" .product_option_values_count").val();

  var html  = '<tbody class="product_option_value_row_'+product_option_value_row+' product_option_value_row" row-key="'+product_option_value_row+'">';
  html += '  <tr>';	
  html += '    <td class="text_left">';
  html += '    <select name="product_option['+option_key+'][product_option_value]['+product_option_value_row+'][option_value_id]">';
  html += $(option_div+" .product_options_select").html();
  html += '    </select><input type="hidden" name="product_option['+option_key+'][product_option_value]['+product_option_value_row+'][product_option_value_id]" value="new_entry" /></td>';
  html += '    <td class="text_right"><input type="text" name="product_option['+option_key+'][product_option_value]['+product_option_value_row+'][pov_quantity]" value="0" size="3" /></td>';
  html += '    <td class="text_left"><select name="product_option['+option_key+'][product_option_value]['+product_option_value_row+'][pov_subtract]"><option value="1" selected="selected">'+$("#text_yes").val()+'</option><option value="0">'+$("#text_no").val()+'</option></select></td>';
  html += '    <td class="text_right"><select name="product_option['+option_key+'][product_option_value]['+product_option_value_row+'][pov_price_prefix]"><option value="+" selected="selected">&plus;</option><option value="-">&minus;</option></select> <input type="text" name="product_option['+option_key+'][product_option_value]['+product_option_value_row+'][pov_price]" value="0.0000" size="5" /></td>';
  html += '    <td class="text_right"><select name="product_option['+option_key+'][product_option_value]['+product_option_value_row+'][pov_points_prefix]"><option value="+" selected="selected">&plus;</option><option value="-">&minus;</option></select> <input type="text" name="product_option['+option_key+'][product_option_value]['+product_option_value_row+'][pov_points]" value="0" size="5" /></td>';
  html += '    <td class="text_right"><select name="product_option['+option_key+'][product_option_value]['+product_option_value_row+'][pov_weight_prefix]"><option value="+" selected="selected">&plus;</option><option value="-">&minus;</option></select> <input type="text" name="product_option['+option_key+'][product_option_value]['+product_option_value_row+'][pov_weight]" value="0.00000000" size="9" /></td>';
  html += '    <td><a onclick="RemoveProductOptionValue(\''+option_div+'\',\''+product_option_value_row+'\')" class="button red">'+$("#text_btn_delete").val()+'</a></td>';
  html += '  </tr>';	
  html += '</tbody>';

  $(option_div+" table tfoot").before(html);

  product_option_value_row++;
  $(option_div+" .product_option_values_count").val(product_option_value_row);
}
    
function RemoveProductOptionValue(option_div,product_option_value_row) {
  $(option_div+' .product_option_value_row_'+product_option_value_row).remove();
  //$(option_div+" .product_option_values_count").val(product_option_value_row);
}
    
function EditProductOptionsTab() {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var product_id = $("#product_id").val();
  var product_options = {};
  var i = 0;
  $.each($(".product_option_tab"), function(){
      if(!product_options[i]) {
          product_options[i] = {};        
      }
      product_options[i]["product_option_id"] = $(this).find("input[name='product_option["+i+"][product_option_id]']").val();
      product_options[i]["option_id"] = $(this).find("input[name='product_option["+i+"][option_id]']").val();
      product_options[i]["option_name"] = $(this).find("input[name='product_option["+i+"][option_name]']").val();
      product_options[i]["option_type"] = $(this).find("input[name='product_option["+i+"][option_type]']").val();
      product_options[i]["po_is_required"] = $(this).find("select[name='product_option["+i+"][po_is_required]']").val();
      $.each($(this).find(".product_option_value_row"), function(){
        var row_key = $(this).attr("row-key");
        if(!product_options[i]["product_option_values"]) {
            product_options[i]["product_option_values"] = {};        
        }
        if(!product_options[i]["product_option_values"][row_key]) {
            product_options[i]["product_option_values"][row_key] = {};        
        }
        product_options[i]["product_option_values"][row_key]["product_option_value_id"] = $(this).find("input[name='product_option["+i+"][product_option_value]["+row_key+"][product_option_value_id]']").val();
        product_options[i]["product_option_values"][row_key]["option_value_id"] = $(this).find("select[name='product_option["+i+"][product_option_value]["+row_key+"][option_value_id]']").val();
        product_options[i]["product_option_values"][row_key]["pov_quantity"] = $(this).find("input[name='product_option["+i+"][product_option_value]["+row_key+"][pov_quantity]']").val();
        product_options[i]["product_option_values"][row_key]["pov_subtract"] = $(this).find("select[name='product_option["+i+"][product_option_value]["+row_key+"][pov_subtract]']").val();
        product_options[i]["product_option_values"][row_key]["pov_price"] = $(this).find("input[name='product_option["+i+"][product_option_value]["+row_key+"][pov_price]']").val();
        product_options[i]["product_option_values"][row_key]["pov_price_prefix"] = $(this).find("select[name='product_option["+i+"][product_option_value]["+row_key+"][pov_price_prefix]']").val();
        product_options[i]["product_option_values"][row_key]["pov_points"] = $(this).find("input[name='product_option["+i+"][product_option_value]["+row_key+"][pov_points]']").val();
        product_options[i]["product_option_values"][row_key]["pov_points_prefix"] = $(this).find("select[name='product_option["+i+"][product_option_value]["+row_key+"][pov_points_prefix]']").val();
        product_options[i]["product_option_values"][row_key]["pov_weight"] = $(this).find("input[name='product_option["+i+"][product_option_value]["+row_key+"][pov_weight]']").val();
        product_options[i]["product_option_values"][row_key]["pov_weight_prefix"] = $(this).find("select[name='product_option["+i+"][product_option_value]["+row_key+"][pov_weight_prefix]']").val();
        //console.log(product_options[i]["option_id"]);
      });
      //console.log(product_options[i]["option_id"]);
      i++;
  });
  //alert(clicked_tab);return;
  $.ajax({
  url:"/_admin/catalog/ajax/edit/edit-product-options-tab.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_id:product_id,
    product_options:product_options
    }
  }).done(function(){
    
    $("#edit_product").effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_product_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);
    
    GetProductOptions(product_id);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}
      
function GetProductOptions(product_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var language_id = $("#language_id").val();
  var category_id = $("#category_id").val();
  var active_option_tab = $("#product_options_tabs a.active").attr("href");
  //alert(attribute_group_id);return;
  $.ajax({
  url:"/_admin/catalog/ajax/get/get-product-options.php",
  type:"POST",
  data:{
    user_access:user_access,
    language_id:language_id,
    category_id:category_id,
    product_id:product_id
    }
  }).done(function(product_options){
    
    $("#product_options_tab").html(product_options);
    
    $("#product_options_tabs a").removeClass("active");
    $(".product_option_tab").hide();
    $("#product_options_tabs a[href='"+active_option_tab+"']").addClass("active");
    $(active_option_tab).show();
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function AddProductDiscount() {
  var product_discount_row = $(".product_discounts_count").val();

  var html  = '<tbody class="product_discount_row_'+product_discount_row+' product_discount_row" data-id="new_entry" row-key="'+product_discount_row+'">';
  html += '  <tr>';	
  html += '    <td class="text_left">';
  html += '    <select name="product_discount['+product_discount_row+'][customer_group_id]">';
  html += $(".customer_groups_select").html();
  html += '    </select></td>';
  html += '    <td class="text_right"><input type="text" name="product_discount['+product_discount_row+'][pd_quantity]" class="pd_quantity" value="0" style="width: 80px;" /></td>';
  html += '    <td class="text_right"><input type="text" name="product_discount['+product_discount_row+'][pd_priority]" class="pd_priority" value="1" style="width: 80px;" /></td>';
  html += '    <td class="text_right"><input type="text" name="product_discount['+product_discount_row+'][pd_price]" class="pd_price" style="width: auto;" /></td>';
  html += '    <td class="text_left"><input type="text" name="product_discount['+product_discount_row+'][pd_date_start]" class="pd_date_start datepicker" style="width: auto;" /></td>';
  html += '    <td class="text_left"><input type="text" name="product_discount['+product_discount_row+'][pd_date_end]" class="pd_date_end datepicker" style="width: auto;" /></td>';
  html += '    <td><a onclick="RemoveProductDiscount(\''+product_discount_row+'\')" class="button red">'+$("#text_btn_delete").val()+'</a></td>';
  html += '  </tr>';	
  html += '</tbody>';

  $("#product_discounts_tab table tfoot").before(html);
  
  $(".product_discount_row_"+product_discount_row+" .datepicker").datepicker({ dateFormat: "yy-mm-dd" });

  product_discount_row++;
  $("#product_discounts_tab .product_discounts_count").val(product_discount_row);
}
    
function RemoveProductDiscount(product_discount_row) {
  $("#product_discounts_tab .product_discount_row_"+product_discount_row).remove();
}

function EditProductDiscountsTab() {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var product_id = $("#product_id").val();
  var product_discounts = {};
  $.each($(".product_discount_row"), function(){
      var row_key = $(this).attr("row-key");
      if(!product_discounts[row_key]) {
          product_discounts[row_key] = {};  
      }
      product_discounts[row_key]["product_discount_id"] = $(this).attr("data-id");
      product_discounts[row_key]["customer_group_id"] = $(this).find("select[name='product_discount["+row_key+"][customer_group_id]']").val();
      product_discounts[row_key]["pd_quantity"] = $(this).find("input[name='product_discount["+row_key+"][pd_quantity]']").val();
      product_discounts[row_key]["pd_priority"] = $(this).find("input[name='product_discount["+row_key+"][pd_priority]']").val();
      product_discounts[row_key]["pd_price"] = $(this).find("input[name='product_discount["+row_key+"][pd_price]']").val();
      product_discounts[row_key]["pd_date_start"] = $(this).find("input[name='product_discount["+row_key+"][pd_date_start]']").val();
      product_discounts[row_key]["pd_date_end"] = $(this).find("input[name='product_discount["+row_key+"][pd_date_end]']").val();
      //console.log(product_discounts[row_key]["option_id"]);
  });
  //alert(clicked_tab);return;
  $.ajax({
  url:"/_admin/catalog/ajax/edit/edit-product-discounts-tab.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_id:product_id,
    product_discounts:product_discounts
    }
  }).done(function(){
    
    $("#edit_product").effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_product_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);
    
    GetProductDiscounts(product_id);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteProductDiscount(product_discount_id) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-product-discount.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_discount_id:product_discount_id
    }
  }).done(function(){

    $("#modal_confirm_delete_discount").dialog("close");
    $("#product_discount_"+product_discount_id).remove();

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetProductDiscounts(product_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var language_id = $("#language_id").val();
  //alert(attribute_group_id);return;
  $.ajax({
  url:"/_admin/catalog/ajax/get/get-product-discounts.php",
  type:"POST",
  data:{
    user_access:user_access,
    language_id:language_id,
    product_id:product_id
    }
  }).done(function(product_discounts){
    
    $("#product_discounts_tab").html(product_discounts);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteProductOptionValue(product_option_value_id,product_option_id,product_option_value_row) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-product-option-value.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_option_value_id:product_option_value_id,
    product_option_id:product_option_id
    }
  }).done(function(){

    $("#modal_confirm_delete_option_value").dialog("close");
    $(product_option_value_row).remove();

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteProductImage(product_image_id, product_image,type) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var product_id = $("#product_id").val();
  if(type == '1') {
    // default image
    //var product_image = $("#default_image").val();
  }
  if(type == '2') {
    // gallery image
    //var product_image = $("#gallery_image_"+product_image_id).val();
  }
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-product-image.php",
  type:"POST",
  data:{
    user_access:user_access,
    product_image_id:product_image_id,
    product_image:product_image,
    product_id:product_id,
    type:type
    }
  }).done(function(){

    $("#modal_confirm_delete_img").dialog("close");
    $("#gallery_image_"+product_image_id).remove();
    EditProductImagesTab("#product_images_tab");

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function AddCategoryToProduct(category,container_id){
  if(CheckEditRights() === false) return;
  
  var choosable = $("#select_categories option:selected").attr("data-choose");
  if(choosable == "1") {
    alert($("#category_is_not_choosable").val());
    return;
  }
  //alert(choosable);
  var arr = category.split('-');
  var category_id = "";
  var is_selected = "";
  var category_id = arr[0];
  var category_name = arr[1];
  if (category_id == '0') {
      return;
  }
  var category_ids = $(container_id).val(); // category_id contains the category id that the user has selected
  is_selected = category_ids.search(category_id+","); // the method search() returns -1 if no match was found
  if(is_selected != '-1') {
    alert($("#choosen_category_already").val());
  }
  else {
    $(container_id).val(category_id + "," + category_ids);    
    $("#categories_list").append("<li id='"+category_id+"'><b>-"+category_name+"</b> (<a onclick='RemoveCategoryFromProduct(\""+category_id+"\",\""+container_id+"\")' style='display:inline-block;color:red;'>x</a>)</li>");
    $("#select_categories").val("0").attr("selected",true);
  }
}

function RemoveCategoryFromProduct(id,container_id){
  var category_ids = $(container_id).val();
  var new_category_ids = category_ids.replace(id+",","");
  $(container_id).val(new_category_ids);
  $('#categories_list #'+id).remove();
}

function MoveAttributeGroupForwardBackward(attribute_group_id, ag_sort_order, action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(attribute_group_id);return;
  $.ajax({
  url:"/_admin/catalog/ajax/move-attribute-group-forward-backward.php",
  type:"POST",
  data:{
    user_access:user_access,
    attribute_group_id:attribute_group_id,
    ag_sort_order:ag_sort_order,
    action:action
    }
  }).done(function(attribute_groups){
    
    $("#attributes_groups_list").html(attribute_groups);
    $("#tr_"+attribute_group_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteAttributeGroup() {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var attribute_group_id = $(".delete_attribute_group_link.active").attr("data-id");
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-attribute-group.php",
  type:"POST",
  data:{
    user_access:user_access,
    attribute_group_id:attribute_group_id
    }
  }).done(function(attribute_groups){

    $("#modal_confirm").dialog("close");
    $("#attributes_groups_list").html(attribute_groups);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetAttributesByGroup(attribute_group) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
    $("#choose_attribute_group td").removeClass("active");
  $(attribute_group).parent().addClass("active");
  var attribute_group_id = $(attribute_group).attr("data-id")
  //alert(attribute_group_id);return;
  $.ajax({
  url:"/_admin/catalog/ajax/get/get-attributes-by-attribute-group.php",
  type:"POST",
  data:{
    user_access:user_access,
    attribute_group_id:attribute_group_id
    }
  }).done(function(attributes){
    
    $("#right_column").show();
    $("#attributes_list").html(attributes);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function MoveAttributeForwardBackward(attribute_id,attribute_group_id,attribute_sort_order,action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(attribute_id);return;
  $.ajax({
  url:"/_admin/catalog/ajax/move-attribute-forward-backward.php",
  type:"POST",
  data:{
    user_access:user_access,
    attribute_group_id:attribute_group_id,
    attribute_id:attribute_id,
    attribute_sort_order:attribute_sort_order,
    action:action
    }
  }).done(function(attributes){
    
    $("#attributes_list").html(attributes);
    $("#tr_"+attribute_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteAttribute(step) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var attribute_id = $(".delete_attribute_link.active").attr("data-id");
  var attribute_group_id = $(".delete_attribute_link.active").attr("data-parent");
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-attribute.php",
  type:"POST",
  data:{
    user_access:user_access,
    attribute_id:attribute_id,
    attribute_group_id:attribute_group_id,
    step:step
    }
  }).done(function(attributes){

    if(step == "first") {
      $("#modal_confirm").dialog("close");
    }
    else {
      $("#modal_confirm_delete_attribute").dialog("close");
    }
    $("#attributes_list").html(attributes);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteCategoryFromAttribute(category_id,attribute_id) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-category-to-attribute.php",
  type:"POST",
  data:{
    user_access:user_access,
    category_id:category_id,
    attribute_id:attribute_id
    }
  }).done(function(){
    
    var old_categories_list = $('#old_categories_list').val();
    var new_category_ids = old_categories_list.replace(category_id+",","");
    $('#old_categories_list').val(new_category_ids);
    
    $("#categories_list li#"+category_id).remove();

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteCategoryFromProduct(category_id,product_id) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-category-to-product.php",
  type:"POST",
  data:{
    user_access:user_access,
    category_id:category_id,
    product_id:product_id
    }
  }).done(function(){
    
    var old_categories_list = $('#old_categories_list').val();
    var new_category_ids = old_categories_list.replace(category_id+",","");
    $('#old_categories_list').val(new_category_ids);
    
    $("#categories_list li#"+category_id).remove();

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function MoveOptionForwardBackward(option_id,option_sort_order,action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(option_id);return;
  $.ajax({
  url:"/_admin/catalog/ajax/move-option-forward-backward.php",
  type:"POST",
  data:{
    user_access:user_access,
    option_id:option_id,
    option_sort_order:option_sort_order,
    action:action
    }
  }).done(function(options){
    
    $("#products_options_list").html(options);
    $("#tr_"+option_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteOption(step) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var option_id = $(".delete_option_link.active").attr("data-id");
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-option.php",
  type:"POST",
  data:{
    user_access:user_access,
    option_id:option_id,
    step:step
    }
  }).done(function(options){

    //jQuery('html, body').animate({scrollTop: 0}, 1000);
    if(step == "first") {
      $("#modal_confirm").dialog("close");
    }
    else {
      $("#modal_confirm_delete_option").dialog("close");
    }
    $("#products_options_list").html(options);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function MoveOptionValueForwardBackward(option_id,option_value_id,ov_sort_order,option_key,action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(option_value_id);return;
  $.ajax({
  url:"/_admin/catalog/ajax/move-option-value-forward-backward.php",
  type:"POST",
  data:{
    user_access:user_access,
    option_id:option_id,
    option_value_id:option_value_id,
    ov_sort_order:ov_sort_order,
    action:action
    }
  }).done(function(option_values){
    
    $("#option_values").html(option_values);
    $("#option_value_row_"+option_key).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteOptionValueImage(option_value_id,option_value_row) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var ov_image_path = $("#option_value_row_"+option_value_row+" .ov_image_path").val();
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-option-value-image.php",
  type:"POST",
  data:{
    user_access:user_access,
    option_value_id:option_value_id,
    ov_image_path:ov_image_path
    }
  }).done(function(){

    $("#option_value_row_"+option_value_row+" .ov_image_div").remove();
    $("#option_value_row_"+option_value_row+" .ov_image_file").removeClass("hidden");

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteOptionValue(step) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var option_value_id = $(".delete_option_value_link.active").attr("data-id");
  var option_value_row = $(".delete_option_value_link.active").attr("data-row");
  $.ajax({
  url:"/_admin/catalog/ajax/delete/delete-option-value.php",
  type:"POST",
  data:{
    user_access:user_access,
    option_value_id:option_value_id,
    step:step
    }
  }).done(function(option_values){

    //$("#delete_result").hide();
    if(step == "first") {
      $("#modal_confirm").dialog("close");
      if(option_values == "") { 

      }
      else {
        $("#delete_result").show();
        $("#delete_result").html(option_values);
      }
    }
    else {
      $("#modal_confirm_delete_option_value").dialog("close");
      $("#option_value_row_"+option_value_row).remove();
    }
      
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function EditOrderStatus(order_id) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var order_status_id = $("#order_"+order_id+" .order_status_id").val();
  //alert(order_status_id);return;
  $.ajax({
  url:"/_admin/sales/ajax/edit/edit-order-status.php",
  type:"POST",
  data:{
    user_access:user_access,
    order_id:order_id,
    order_status_id:order_status_id
    }
  }).done(function(){
    
    if(order_status_id == 5) $("#order_"+order_id+" .order_status_id").addClass("complete");
    else $("#order_"+order_id+" .order_status_id").removeClass("complete");
    
    $("#order_"+order_id).effect("highlight", {}, 3000);
    var massage = $("#orders_list .ajaxmessage").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);
    
    $("#ajax_loaderpadding-left: 21%;").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteOrder(order_id) {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  $.ajax({
  url:"/_admin/sales/ajax/delete/delete-order.php",
  type:"POST",
  data:{
    user_access:user_access,
    order_id:order_id
    }
  }).done(function(){

    window.history.back();
    //window.location = base_url+window.location.pathname;

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetClientImage(client_id) {
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  $.ajax({
  url:"/_admin/clients/ajax/get/get-client-image.php",
  type:"POST",
  data:{
    client_id:client_id
    }
  }).done(function(current_image){

    $("#current_image").html(current_image);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteClient() {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var client_id = $(".delete_client_link.active").attr("data-id");
  $.ajax({
  url:"/_admin/clients/ajax/delete/delete-client.php",
  type:"POST",
  data:{
    user_access:user_access,
    client_id:client_id
    }
  }).done(function(clients_list){

    //jQuery('html, body').animate({scrollTop: 0}, 1000);
  
    $("#modal_confirm").dialog("close");
    $("#clients_list").html(clients_list);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetSliderImage(slider_id) {
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  $.ajax({
  url:"/_admin/slider/ajax/get/get-slider-image.php",
  type:"POST",
  data:{
    slider_id:slider_id
    }
  }).done(function(current_image){

    $("#current_image").html(current_image);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteSlider() {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var slider_id = $(".delete_slider_link.active").attr("data-id");
  $.ajax({
  url:"/_admin/slider/ajax/delete/delete-slider.php",
  type:"POST",
  data:{
    user_access:user_access,
    slider_id:slider_id
    }
  }).done(function(sliders_list){

    //jQuery('html, body').animate({scrollTop: 0}, 1000);
  
    $("#modal_confirm").dialog("close");
    $("#sliders_list").html(sliders_list);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function GetInstructorImage(team_member_id) {
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  $.ajax({
  url:"/_admin/team_members/ajax/get/get-team_member-image.php",
  type:"POST",
  data:{
    team_member_id:team_member_id
    }
  }).done(function(current_image){

    $("#current_image").html(current_image);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function ToggleExpandNewsCategory(news_category_id, action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/news/ajax/edit/toggle-expand-news-category.php",
  type:"POST",
  data:{
    user_access:user_access,
    news_category_id:news_category_id,
    action:action
    }
  }).done(function(news_categories){
    
    $("#news_categories_list").html(news_categories);
    $("#tr_"+news_category_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function MoveNewsCategoryForwardBackward(news_category_id, news_cat_parent_id, news_cat_sort_order, news_cat_hierarchy_level, action) {
  if(CheckEditRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  //alert(friendly_url);return;
  $.ajax({
  url:"/_admin/news/ajax/edit/move-news-category-forward-backward.php",
  type:"POST",
  data:{
    user_access:user_access,
    news_category_id:news_category_id,
    news_cat_parent_id:news_cat_parent_id,
    news_cat_sort_order:news_cat_sort_order,
    news_cat_hierarchy_level:news_cat_hierarchy_level,
    action:action
    }
  }).done(function(news_categories){
    
    $("#news_categories_list").html(news_categories);
    $("#tr_"+news_category_id).effect("highlight", {}, 1000);
    
    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteNewsCategory() {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var news_category_id = $(".delete_news_category_link.active").attr("data-id");
  var news_cat_parent_id = $(".delete_news_category_link.active").attr("data-parent-id");
  $.ajax({
  url:"/_admin/news/ajax/delete/delete-news-category.php",
  type:"POST",
  data:{
    user_access:user_access,
    news_category_id:news_category_id,
    news_cat_parent_id:news_cat_parent_id
    }
  }).done(function(news_categories){

    $("#modal_confirm").dialog("close");
    $("#news_categories_list").html(news_categories);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}

function DeleteNews() {
  if(CheckDeleteRights() === false) return;
  $("#ajax_loader").show();
  setTimeout(function () { $("#ajax_loader").hide(); }, 5000);
  var user_access = GetUserAccess();
  var news_id = $(".delete_news_link.active").attr("data-id");
  $.ajax({
  url:"/_admin/news/ajax/delete/delete-news.php",
  type:"POST",
  data:{
    user_access:user_access,
    news_id:news_id
    }
  }).done(function(news){

    $("#modal_confirm").dialog("close");
    $("#news_list").html(news);

    $("#ajax_loader").hide();
  }).fail(function(error){
    console.log(error);
  })
}