/* Menu Functions */

/*  when ready, load the correct function based on the side menu options */
$(function() { 
	var side_menu;
	side_menu= $.urlParam('side_menu');
	if(side_menu==0){
		fill_main("home");
	}else{
		$('#'+side_menu).addClass("side_menu_selected");
		fill_main(side_menu);
	}
});

/*  Get url parameters for side menu */
$.urlParam = function(name, url) {
    if (!url) {
     url = window.location.href;
    }
    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(url);
    if (!results) { 
        return 0; 
    }
    return results[1] || 0;
}

/* Call the requested function */
function fill_main(side_menu_selected) {
$.ajax({
type: "GET",
url: "functions.php",
data: "side_menu_selected=" + side_menu_selected,
dataType: "html",
success: function(html){       $("#main").html(html);     }
}); 
}





/* Dom manipulation and choices Functions */

// Hide and show the form's inputs depending on user type to add
function add_new_user_selection(type){
	if(type==0){
		$("#add_professor_option").prop("checked", false);
		$("#aem_tr").show();
		$("#add_user_type").val('0');
	}else{
		$("#add_student_option").prop("checked", false);
		$("#aem_tr").hide();
		$("#add_user_type").val('1');
	}
}

// scroll big tables on classes overview
function move_class_table(direction, id){
	if(direction==0){
		$('#table_class_'+id).animate({'margin-left': '-=150'});
	}else{
		$('#table_class_'+id).animate({'margin-left': '+=150'});
	}

}

// change page on view all users
function change_page_all_users(page){
	var current_page=$('#current_page').val();
	
	if(current_page != page){
		$('#current_page').val(page);
		
		$('#page_'+current_page).toggleClass( "underline" );
		$('#page_'+page).toggleClass( "underline" );
		
		$('#table_users_'+current_page).hide();
		$('#table_users_'+page).show();
	}
}

/* Back to user search results */
function backto_user_result() {
	$('#search_user').toggleClass( "hidden" );
	$('#edit_user').toggleClass( "hidden" );
}



/* Main Operations Functions */


/* Ajax to show the last academic id changes */
function show_last_academicid_logs() {
	var logs_num = $("#show_last_academicid_logs_num").val();
	$.ajax({
		type: "GET",
		url: "functions.php",
		data: "logs_num=" + logs_num + "&show_last_academicid_logs=1",
		dataType: "html",
		success: function(html){  
			$("#academic_id_log_entries").html(html);
		}
	}); 
}


/* Ajax to add new user */
function add_new_user() {
	$("#pop_up").html("Adding new user. Please wait...");
	$("#pop_up").show();
	var username = $("#add_user_username").val();
	var pass = $("#add_user_pass").val();
	var fname = $("#add_user_fname").val();
	var lname = $("#add_user_lname").val();
	var type = $("#add_user_type").val();
	var aem = $("#add_user_aem").val();
	var email = $("#add_user_mail").val();
	var phone = $("#add_user_phone").val();
	var department = $("#add_user_department").val();
	
	var data_query="add_new_user_byadmin=1&user="+username+"&pass="+pass+"&first_name="+fname+"&last_name="+lname+"&telephone="+phone+"&department="+department+"&email="+email;
	if(type == 1){
		data_query = data_query+"&type="+type;
	}else{
		data_query = data_query+"&type="+type+"&aem="+aem;
	}
	
	$.ajax({
		type: "GET",
		url: "functions.php",
		data: data_query,
		dataType: "html",
		success: function(html){  
			$("#pop_up").html(html);
			$("#pop_up").show().delay(5000).fadeOut();
		}
	}); 
}


/* Ajax to search for user */
function search_for_user() {
	var data_query="searchuser=1";
	
	var clean_inputs = 1;
	
	var fname = $("#search_user_fname").val();
	if(fname!='' && fname!=' '){
		data_query=data_query+"&fname="+fname;
		clean_inputs=0;
	}
	var lname = $("#search_user_lname").val();
	if(lname!='' && lname!=' '){
		data_query=data_query+"&lname="+lname;
		clean_inputs=0;
	}
	var aem = $("#search_user_aem").val();
	if(aem!='' && aem!=' '){
		data_query=data_query+"&aem="+aem;
		clean_inputs=0;
	}
	
	if(clean_inputs==0){
		$.ajax({
			type: "GET",
			url: "functions.php",
			data: data_query,
			dataType: "html",
			success: function(html){  
				$("#search_user_results").html(html);
			}
		}); 
	}else{
		view_all_for_user('aem','ASC');
	}
}


/* Ajax to request view for all user */
function view_all_for_user(order,direction){	
	$.ajax({
		type: "GET",
		url: "functions.php",
		data: "show_all_users=1&orderby="+order+"&direction="+direction,
		dataType: "html",
		success: function(html){  
			$("#search_user_results").html(html);
		}
	}); 
}


/* Ajax to remove inactive users and remake the table with users that will be removed */
function remove_inactive_users() {
	//var logs_num = $("#show_last_academicid_logs_num").val();
	$.ajax({
		type: "GET",
		url: "functions.php",
		data: "remove_inactive_users=1",
		dataType: "html",
		success: function(html){  
		}
	}); 
	$.ajax({
		type: "GET",
		url: "functions.php",
		data: "show_inactive_users_to_remove=1",
		dataType: "html",
		success: function(html){ 
			$("#inactive_user_table").html(html);
		}
	});
}


/* Ajax to remove invalid grades and show again the removed grades */
function remove_invalid_grades() {
	//var logs_num = $("#show_last_academicid_logs_num").val();
	$.ajax({
		type: "GET",
		url: "functions.php",
		data: "remove_invalid_grades=1",
		dataType: "html",
		success: function(html){  
			$("#inactive_grades_table").html(html);
		}
	});
}


/* Ajax to open the user edit panel */
function edit_user_table(user_id) {
	$('#search_user').toggleClass( "hidden" );
	$('#edit_user').toggleClass( "hidden" );
	
	$.ajax({
		type: "GET",
		url: "functions.php",
		data: "edit_user_table=1&user_id="+user_id,
		dataType: "html",
		success: function(html){  
			$('#edit_user_results').html(html);
		}
	}); 
}


/* Ajax to remove the user in edit panel */
function remove_user(user_id) {
	var lname = $('#last_name_for_delete_'+user_id).val();
	var fname = $('#first_name_for_delete_'+user_id).val();
	if (confirm("Are you sure you want to remove the user "+fname+" "+lname+" ?")){
		$('#delete_user_form_'+user_id).submit();
	}
}


/* Ajax to update / edit the user */
function edit_user_alter_values(id) {
	$("#pop_up").html("Updating user. Please wait...");
	$("#pop_up").show();
	var username = $("#edit_user_username").val();
	var pass = $("#edit_user_password").val();
	var activated = $("#edit_user_activated").val();
	var fname = $("#edit_user_first_name").val();
	var lname = $("#edit_user_last_name").val();
	var type = $("#edit_user_type").val();
	var academicid = $("#edit_user_academicid").val();
	var aem = $("#edit_user_aem").val();
	var email = $("#edit_user_mail").val();
	var phone = $("#edit_user_telephone").val();
	var department = $("#edit_user_department").val();

	var data_query="edit_user_byadmin=1&id="+id+"&user="+username+"&activated="+activated+"&first_name="+fname+"&last_name="+lname+"&telephone="+phone+"&type="+type+"&email="+email+"&aem="+aem+"&academicid="+academicid+"&password="+pass+"&department="+department;
	
	$.ajax({
		type: "GET",
		url: "functions.php",
		data: data_query,
		dataType: "html",
		success: function(html){  
			$("#pop_up").html(html);
			$("#pop_up").show().delay(5000).fadeOut();
		}
	}); 
}

/* Ajax to retire the user */
function retire_user(id) {
	var fname = $("#edit_user_first_name").val();
	var lname = $("#edit_user_last_name").val();
	var data_query="retire_user_byadmin=1&id="+id+"&first_name="+fname+"&last_name="+lname;
	
	$.ajax({
		type: "GET",
		url: "functions.php",
		data: data_query,
		dataType: "html",
		success: function(html){  
			$("#pop_up").html(html);
			$("#pop_up").show().delay(5000).fadeOut();
		}
	}); 
}


/* Ajax to send bug report */
function send_bug_report() {
	var user_related,sch_related,reg_related,other_related,grades_related,grades_misc_related;
	$("#pop_up").html("Collecting data to send in the report...");
	$("#pop_up").show();
	
	if ($('#user_related').is(":checked")){
		user_related=1;
	}else{
		user_related=0;
	}
	if ($('#sch_related').is(":checked")){
		sch_related=1;
	}else{
		sch_related=0;
	}
	if ($('#reg_related').is(":checked")){
		reg_related=1;
	}else{
		reg_related=0;
	}
	if ($('#other_related').is(":checked")){
		other_related=1;
	}else{
		other_related=0;
	}
	if ($('#grades_related').is(":checked")){
		grades_related=1;
	}else{
		grades_related=0;
	}
	if ($('#grades_misc_related').is(":checked")){
		grades_misc_related=1;
	}else{
		grades_misc_related=0;
	}
	
	var bug_description = $("#bug_description").val();
	var sub_date = $("#sub_date").val();
	
	var data_query="bug_report=1&user_related="+user_related+"&sch_related="+sch_related+"&reg_related="+reg_related+"&other_related="+other_related+"&grades_related="+grades_related+"&grades_misc_related="+grades_misc_related+"&bug_description="+bug_description+"&sub_date="+sub_date;
	
	$.ajax({
		type: "GET",
		url: "functions.php",
		data: data_query,
		dataType: "html",
		success: function(html){  
			$("#pop_up").html("Bug report send successfully.");
			$("#pop_up").show().delay(5000).fadeOut();
		}
	}); 
}

