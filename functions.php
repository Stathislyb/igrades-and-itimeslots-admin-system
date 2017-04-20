<?php
include("../schedule/database.php"); 
if(!isset($_SESSION)) {
     session_start();
}



if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_POST['id_for_delete']) && $_POST['id_for_delete']>=0) {
		$id = mysql_escape_string(filter_var($_POST['id_for_delete'], FILTER_SANITIZE_NUMBER_INT));
		$query="DELETE FROM users WHERE id = ".$id;
		$result=mysql_query($query,$conn);
		$query="DELETE FROM studentslots WHERE user_id = ".$id;
		$result=mysql_query($query,$conn);
		$query="DELETE FROM igrades WHERE user_id = ".$id;
		$result=mysql_query($query,$conn);
		$query="DELETE FROM holdclass WHERE userid = ".$id;
		$result=mysql_query($query,$conn);
		//echo "<!-- ".mysql_error()." -->";
	}
}


// Returns the table with the academic id logs
function academic_id_logs($quantity){
	global $conn;
	
	$query = "SELECT accademicid_log.modification_date, accademicid_log.academicid, accademicid_log.Remote_ADDR, users.aem, users.last_name, users.first_name FROM accademicid_log INNER JOIN users ON accademicid_log.userid=users.id ORDER BY accademicid_log.modification_date DESC LIMIT ".$quantity;
	$results = mysql_query($query,$conn);
	if (mysql_num_rows($results)==0){
		echo "There are no academic ID entries.";
	}else{
		echo "<table cellpadding='5' cellspacing='5' class='centered'>";
		echo "<tr><th>Last Name</th> <th>First Name</th> <th>A.E.M.</th> <th>Academic ID</th> <th>Modification Date</th> <th>IP</th></tr>";
		while($rows = mysql_fetch_array($results)){
			echo "<tr><td>".$rows['last_name']."</td> <td>".$rows['first_name']."</td> <td>".$rows['aem']."</td> <td>".$rows['academicid']."</td> <td>".$rows['modification_date']."</td> <td>".$rows['Remote_ADDR']."</td> </tr>";
		}
		echo "</table>";
	}
}
if(isset($_GET['show_last_academicid_logs'])){
	$logs_num=mysql_escape_string(filter_var($_GET['logs_num'], FILTER_SANITIZE_NUMBER_INT));
	if($logs_num == $_GET['logs_num']){
		academic_id_logs($logs_num);
	}
}



// Make a table with the users that will be removed
function show_users_for_cleanup(){
	global $conn;
	
	$query_user = "SELECT * FROM users WHERE activated='0'";
	$results_user = mysql_query($query_user,$conn);
	if (mysql_num_rows($results_user)==0){
		echo "There are inactive users to remove.";
	}else{
		echo "<table cellpadding='5' cellspacing='5' class='centered'>" ;
		echo "<tr> <th>Last Name</th> <th>First Name</th> <th>ΑΕΜ</th> <th>e-mail</th></tr>";
		while ($rows_user = mysql_fetch_array($results_user)){
			echo "<tr> <td>".$rows_user['last_name']."</td> <td>".$rows_user['first_name']."</td> <td>".$rows_user['aem']."</td> <td>".$rows_user['email']."</td></tr>";	
		}
		echo "</table><br/>";
	}
}
if(isset($_GET['show_inactive_users_to_remove'])){
	show_users_for_cleanup();
}



// Make a table with the user to edit
function edit_user_table($user_id){
	global $conn;
	
	$query_user = "SELECT * FROM users WHERE id='".$user_id."'";
	$results_user = mysql_query($query_user,$conn);
	if (mysql_num_rows($results_user)==0){
		echo "The user was not found. Possible bug.";
	}else{
		$rows_user = mysql_fetch_array($results_user);
		
		
		if($rows_user['activated']==1){
			$activated = "<select id='edit_user_activated'><option value='1' selected>E-mail Activated</option><option value='0'>Inactive User</option></option><option value='2'>Retired User</option></select> ";
		}else{
			if($rows_user['activated']==2){
				$activated = "<select id='edit_user_activated'><option value='1'>E-mail Activated</option><option value='0'>Inactive User</option><option value='2' selected>Retired User</option></select> ";
			}else{
				$activated = "<select id='edit_user_activated'><option value='1'>E-mail Activated</option><option value='0' selected>Inactive User</option><option value='2'>Retired User</option></select> ";
			}
		}
		
		if($rows_user['type']==1){
			$type = "<select id='edit_user_type'><option value='0'>Student</option><option value='1' selected>Professor</option></select> ";
		}else{
			$type = "<select id='edit_user_type'><option value='0' selected>Student</option><option value='1'>Professor</option></select> ";
		}
		
		echo "<table align='left' border='0' cellspacing='0' cellpadding='3' class='centered_alt_35'>";
		echo "<tr><td>Username <br />(latin characters): </td><td><input type='text' id='edit_user_username' maxlength='25' value='".$rows_user['username']."'/></td></tr>";
		echo "<tr><td>Password <br />(leave empty to stay unchanged): </td><td><input type='password' id='edit_user_password' maxlength='25' value=''/></td></tr>";
		echo "<tr><td>First Name <br />(greek characters): </td><td><input type='text' id='edit_user_first_name' maxlength='25' value='".$rows_user['first_name']."'/></td></tr>";
		echo "<tr><td>Last Name <br />(greek characters): </td><td><input type='text' id='edit_user_last_name' maxlength='25' value='".$rows_user['last_name']."'/></td></tr>";
		echo "<tr><td>A.E.M. <br />(numbers): </td><td><input type='text' id='edit_user_aem' maxlength='8' value='".$rows_user['aem']."'/></td></tr>";
		echo "<tr><td>E-Mail <br />(latin characters): </td><td><input type='text' id='edit_user_mail' value='".$rows_user['email']."'/></td></tr>";
		echo "<tr><td>Academic ID : </td><td><input type='text' id='edit_user_academicid' maxlength='10'  value='".$rows_user['academicid']."'/></td></tr>";
		echo "<tr><td>Phone number <br />(numbers) : </td><td><input type='text' id='edit_user_telephone' maxlength='10'  value='".$rows_user['telephone']."'/></td></tr>";
		echo "<tr><td>User Status :</td><td>".$activated."</td></tr>";
		echo "<tr><td>Type :</td><td>".$type."</td></tr>";
		echo "<tr><td>Department :</td><td><select id='edit_user_department'>";
		$query_dep = "SELECT * FROM departments";
		$rslt_dep = mysql_query($query_dep,$conn);
		while ($departments= mysql_fetch_array($rslt_dep)){
			$selected="";
			if($departments['id'] == $rows_user['departmentid']){
				$selected = "selected";
			}
			echo "<option value='".$departments['id']."' ".$selected.">".$departments['shortname']."</option>";
		}
		echo "</select></td></tr>";
		echo "<tr class='filler_row'></tr><tr><td align='center'><input class='retire_user_btn' type='submit' name='retire_user' value='Retire User' onclick='retire_user(".$user_id.");'/></td></td>";
		echo "<td align='center'><input type='submit' class='edit_user_btn' name='subadduser' value='Submit' onclick='edit_user_alter_values(".$user_id.");'/></td></tr>";
		echo "</table>";
	}
}
if(isset($_GET['edit_user_table'])){
	$user_id = mysql_escape_string(filter_var($_GET['user_id'], FILTER_SANITIZE_NUMBER_INT));
	if($user_id == $_GET['user_id']){
		edit_user_table($user_id);
	}
}



// Edit User
function edit_user_byadmin($user, $id, $aem, $first_name, $last_name, $email, $activated, $telephone, $type, $academicid, $password, $department){
   global $conn;
   $msg_name = $last_name." ".$first_name;
   
   if($user=='' || $user==' '){
		$user= '';
		echo "Username can not be empty so it will remain the same <br/>";
   }else{
		$user= "username='".$user."', ";
   }
   
   if($aem=='' || $aem==' '){
		echo "A.E.M. can not be empty so it will remain the same <br/>";
		$aem= '';
   }else{
		$aem= "aem='".$aem."', ";
   }
   
   if($first_name=='' || $first_name==' '){
		$first_name= "first_name=NULL, ";
   }else{
		$first_name= "first_name='".$first_name."', ";
   }
   
   if($last_name=='' || $last_name==' '){
		$last_name= "last_name=NULL, ";
   }else{
		$last_name= "last_name='".$last_name."', ";
   }
   
   if($email=='' || $email==' '){
		echo "E-mail can not be empty so it has remain the same <br/>";
		$email= '';
   }else{
		$email= "email='".$email."', ";
   }
   
   if($telephone=='' || $telephone==' '){
		$telephone= "telephone=NULL, ";
   }else{
		$telephone= "telephone='".$telephone."', ";
   }
   
   if($academicid=='' || $academicid==' '){
		$academicid= "academicid=NULL, ";
   }else{
		$academicid= "academicid='".$academicid."', ";
   }
   
   if($password==''){
		$q = "UPDATE users SET  ".$user.$aem.$first_name.$last_name.$email.$telephone.$academicid." activated='".$activated."', type='".$type."', departmentid='".$department."' WHERE id='".$id."' ";
   }else{
	   $password=md5($password);
	   $q = "UPDATE users SET  ".$user.$aem.$first_name.$last_name.$email.$telephone.$academicid." activated='".$activated."', type='".$type."', password='".$password."', departmentid='".$department."' WHERE id='".$id."' ";
   }
	
   $result=mysql_query($q,$conn);
   
   echo "User ".$msg_name." was edited successfully.";
   
   return $result;

}
if(isset($_GET['edit_user_byadmin'])){
	$user = mysql_escape_string(filter_var($_GET['user'], FILTER_SANITIZE_STRING));
	$password = mysql_escape_string(filter_var($_GET['password'], FILTER_SANITIZE_STRING));
	$id = mysql_escape_string(filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT));
	$aem = mysql_escape_string(filter_var($_GET['aem'], FILTER_SANITIZE_NUMBER_INT));
	$first_name = mysql_escape_string(filter_var($_GET['first_name'], FILTER_SANITIZE_STRING));
	$last_name = mysql_escape_string(filter_var($_GET['last_name'], FILTER_SANITIZE_STRING));
	$email = mysql_escape_string(filter_var($_GET['email'], FILTER_SANITIZE_STRING));
	$activated = mysql_escape_string(filter_var($_GET['activated'], FILTER_SANITIZE_NUMBER_INT));
	$telephone = mysql_escape_string(filter_var($_GET['telephone'], FILTER_SANITIZE_NUMBER_INT));
	$type = mysql_escape_string(filter_var($_GET['type'], FILTER_SANITIZE_NUMBER_INT));
	$academicid = mysql_escape_string(filter_var($_GET['academicid'], FILTER_SANITIZE_STRING));
	$department = mysql_escape_string(filter_var($_GET['department'], FILTER_SANITIZE_STRING));
	
	if($user==$_GET['user'] && $id==$_GET['id'] && $aem==$_GET['aem'] && $first_name==$_GET['first_name'] && $last_name==$_GET['last_name'] && $email==$_GET['email'] && $activated==$_GET['activated'] && $telephone==$_GET['telephone'] && $type==$_GET['type'] && $academicid==$_GET['academicid'] && $password==$_GET['password'] && $department==$_GET['department']){
		edit_user_byadmin($user, $id, $aem, $first_name, $last_name, $email, $activated, $telephone, $type, $academicid, $password,$department);
	}else{
		echo "Some of the data were not valid.";
	}
}



// Edit User
function retire_user_byadmin($id, $first_name, $last_name){
    global $conn;

	$q = "UPDATE users SET activated='2' WHERE id='".$id."' ";
    $result=mysql_query($q,$conn);
	
    echo "User ".$last_name." ".$first_name." retired successfully.";
   
    return $result;

}
if(isset($_GET['retire_user_byadmin'])){
	$id = mysql_escape_string(filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT));
	$first_name = mysql_escape_string(filter_var($_GET['first_name'], FILTER_SANITIZE_STRING));
	$last_name = mysql_escape_string(filter_var($_GET['last_name'], FILTER_SANITIZE_STRING));
	
	if($id==$_GET['id'] && $first_name==$_GET['first_name'] && $last_name==$_GET['last_name'] ){
		retire_user_byadmin($id, $first_name, $last_name);
	}else{
		echo "Invalid user data.";
	}
}



// Remove Inactive Users
function remove_inactive_users(){
	global $conn;
	
	$query_del_users = "DELETE FROM users WHERE activated='0'";
	$results_del_users = mysql_query($query_del_users,$conn);
}
if(isset($_GET['remove_inactive_users'])){
	remove_inactive_users();
}



// Show Invalid Grades
function show_invalid_grades(){
	global $conn;
	$q = "SELECT users.last_name, users.first_name, users.aem, iclasses.name, igrades.lab_id, igrades.id, igrades.grade FROM igrades JOIN users ON igrades.user_id = users.id JOIN iclasses ON igrades.class_id = iclasses.id LEFT JOIN ilabs ON ilabs.id = igrades.lab_id WHERE ilabs.id IS NULL";
	$results= mysql_query($q,$conn);
	$found = 0;
	
	while ($rows = mysql_fetch_array($results)){	
		$found++;
		echo "<br/>".$found.".";
		echo "<br/>Laboratory with ID: ".$rows['lab_id'].", does not exist.";
		echo "<br/>Class: ".$rows['name'].".";
		echo "<br/>Grade: ".$rows['grade'].".";
		echo "<br/>".$rows['last_name']." ".$rows['first_name'].", AEM : ".$rows['aem'].".<br/>";
	}
	
	$q = "SELECT users.last_name, users.first_name, users.aem, iclasses.name, ilabs.name as lab_name, igrades.*, COUNT(*) c, GROUP_CONCAT(igrades.grade), GROUP_CONCAT(igrades.id) FROM igrades JOIN users ON igrades.user_id = users.id JOIN iclasses ON igrades.class_id = iclasses.id JOIN ilabs ON igrades.lab_id = ilabs.id GROUP BY igrades.user_id,igrades.lab_id HAVING c > 1";
	$results= mysql_query($q,$conn);
	while ($rows = mysql_fetch_array($results)){	
			
		$found++;
		$dublicated_grades_id = explode(",", $rows['GROUP_CONCAT(igrades.id)']);
		$dublicated_grades = explode(",", $rows['GROUP_CONCAT(igrades.grade)']);
		$grade_id = max($dublicated_grades_id);
		$grade = $dublicated_grades[array_search($grade_id,$dublicated_grades_id)];

		echo "<br/>".$found.".";
		echo "<br/>Duplicate Grade on : ".$rows['lab_name'].".";
		echo "<br/>Class: ".$rows['name'].".";
		echo "<br/>Grade: ".$grade.".";
		echo "<br/>".$rows['last_name']." ".$rows['first_name'].", AEM : ".$rows['aem'].".<br/>";
		
	}
	
	$q = "SELECT users.last_name, users.first_name, users.aem, ilabs.name as lab_name,igrades.id,igrades.grade,iclasses.name,iclasses2.name AS name2 FROM igrades INNER JOIN ilabs ON ilabs.id=igrades.lab_id INNER JOIN users ON users.id=igrades.user_id INNER JOIN iclasses ON igrades.class_id=iclasses.id INNER JOIN iclasses AS iclasses2 ON iclasses2.id=ilabs.class_id WHERE iclasses.name <> iclasses2.name;";
	$results= mysql_query($q,$conn);
	while ($rows = mysql_fetch_array($results)){	
			
		$found++;
		echo "<br/>".$found.".";
		echo "<br/>Missmatched Laboratory : ".$rows['lab_name'].".";
		echo "<br/>Faulty Class : ".$rows['name'].".";
		echo "<br/>Actual Class: ".$rows['name2'].".";
		echo "<br/>Grade: ".$rows['grade'].".";
		echo "<br/>".$rows['last_name']." ".$rows['first_name'].", AEM : ".$rows['aem'].".<br/>";
		
	}
	echo "<br/><br/>Search finished.<br/>".$found." invalid grades found.";
}

// Remove Invalid Grades
function remove_invalid_grades(){
	global $conn;
	
	$q = "SELECT users.last_name, users.first_name, users.aem, iclasses.name, igrades.lab_id, igrades.id, igrades.grade FROM igrades JOIN users ON igrades.user_id = users.id JOIN iclasses ON igrades.class_id = iclasses.id LEFT JOIN ilabs ON ilabs.id = igrades.lab_id WHERE ilabs.id IS NULL";
	$results= mysql_query($q,$conn);
	$found = 0;
	
	while ($rows = mysql_fetch_array($results)){	
	
		$found++;
		$error_msg = "<br/>".$found.".";
		$error_msg .= "<br/>Laboratory with ID: ".$rows['lab_id'].", does not exist.";
		$error_msg .= "<br/>Class: ".$rows['name'].".";
		$error_msg .= "<br/>Grade: ".$rows['grade'].".";
		$error_msg .= "<br/>".$rows['last_name']." ".$rows['first_name'].", AEM : ".$rows['aem'].".";
		echo $error_msg;
		
		$query_delete = "DELETE FROM igrades WHERE id='".$rows['id']."'";
		$result = mysql_query($query_delete,$conn);
		if($result){
			echo "<br/> -- Grade Removed -- <br/>";
			notification_mail_grade_ivalid($rows['user_id'],$rows['class_id'],$rows['lab_id'],$rows['grade'],$error_msg);
		}else{
			echo "<br/> -- Failed to Remove the Grade -- <br/>";
		}
	
	}
	
	$q = "SELECT users.last_name, users.first_name, users.aem, iclasses.name, ilabs.name as lab_name, igrades.*, COUNT(*) c, GROUP_CONCAT(igrades.grade), GROUP_CONCAT(igrades.id) FROM igrades JOIN users ON igrades.user_id = users.id JOIN iclasses ON igrades.class_id = iclasses.id JOIN ilabs ON igrades.lab_id = ilabs.id GROUP BY igrades.user_id,igrades.lab_id HAVING c > 1 ORDER BY  igrades.id DESC";
	$results= mysql_query($q,$conn);
	while ($rows = mysql_fetch_array($results)){	
			
		$found++;
		$dublicated_grades_id = explode(",", $rows['GROUP_CONCAT(igrades.id)']);
		$dublicated_grades = explode(",", $rows['GROUP_CONCAT(igrades.grade)']);
		$grade_id = max($dublicated_grades_id);
		$grade = $dublicated_grades[array_search($grade_id,$dublicated_grades_id)];
		
		$error_msg = "<br/>".$found.". (".$rows['id'].")";
		$error_msg .= "<br/>Duplicate Grade on : ".$rows['lab_name'].".";
		$error_msg .= "<br/>Class: ".$rows['name'].".";
		$error_msg .= "<br/>Grade: ".$grade.".";
		$error_msg .= "<br/>".$rows['last_name']." ".$rows['first_name'].", AEM : ".$rows['aem'].".";
		echo $error_msg;
		
		$query_delete = "DELETE FROM igrades WHERE id='".$grade_id."'";
		$result = mysql_query($query_delete,$conn);
		if($result){
			echo "<br/> -- Grade Removed -- <br/>";
			notification_mail_grade_ivalid($rows['user_id'],$rows['class_id'],$rows['lab_id'],$rows['grade'],$error_msg);
		}else{
			echo "<br/> -- Failed to Remove the Grade -- <br/>";
		}
	}
	
	$q = "SELECT users.last_name, users.first_name, users.aem, ilabs.name as lab_name,igrades.user_id,igrades.id,igrades.grade,iclasses.name,iclasses2.name AS name2 FROM igrades INNER JOIN ilabs ON ilabs.id=igrades.lab_id INNER JOIN users ON users.id=igrades.user_id INNER JOIN iclasses ON igrades.class_id=iclasses.id INNER JOIN iclasses AS iclasses2 ON iclasses2.id=ilabs.class_id WHERE iclasses.name <> iclasses2.name;";
	$results= mysql_query($q,$conn);
	while ($rows = mysql_fetch_array($results)){	
			
		$found++;
		$error_msg = "<br/>".$found.".";
		$error_msg .= "<br/>Missmatched Laboratory : ".$rows['lab_name'].".";
		$error_msg .= "<br/>Faulty Class : ".$rows['name'].".";
		$error_msg .= "<br/>Actual Class: ".$rows['name2'].".";
		$error_msg .= "<br/>Grade: ".$rows['grade'].".";
		$error_msg .= "<br/>".$rows['last_name']." ".$rows['first_name'].", AEM : ".$rows['aem'].".<br/>";
		$query_delete = "DELETE FROM igrades WHERE id='".$rows['id']."'";
		$result = mysql_query($query_delete,$conn);
		if($result){
			echo "<br/> -- Grade Removed -- <br/>";
			notification_mail_grade_ivalid($rows['user_id'],$rows['class_id'],$rows['lab_id'],$rows['grade'],$error_msg);
		}else{
			echo "<br/> -- Failed to Remove the Grade -- <br/>";
		}
	}
	echo "<br/><br/>Search finished.<br/>".$found." invalid grades found and removed.";
}
if(isset($_GET['remove_invalid_grades'])){
	remove_invalid_grades();
}

function notification_mail_grade_ivalid($user_id,$class_id,$lab_id,$grade,$error_msg){
	global $conn;
	$query_user = "SELECT * FROM users WHERE id='$user_id' ";
	$results_user = mysql_query($query_user,$conn);
	$rows_user = mysql_fetch_array($results_user);
	$aem=$rows_user['aem'];
	$fname=$rows_user['first_name'];
	$lname=$rows_user['last_name'];

	$staem= (string)$aem;
	$length= strlen($staem);
	if($length ==4){
	 $aemnew = $aem;
	}
	if($length ==3){
	 $aemnew = "0".$aem;
	}
	if($length ==2){
	 $aemnew = "00".$aem;
	}
	if($length ==1){
	 $aemnew = "000".$aem;
	}
	$date=date('l jS \of F Y h:i:s A');
	$crlf = chr(13) . chr(10);
	$to      = "st".$aemnew."@icte.uowm.gr ";
	$subject = "[IGrades] Grade Change Notification";
	$message = "Αυτοματοποιημένο μήνυμα i-grades".$crlf.$crlf."Προς: [ ".$fname." ".$lname." ], AEM [".$aem."] ".$crlf.$crlf.
	"Λόγω ασυνέπειας της βάσης δεδομένων του arch, μπορεί να έχει απομακρυνθεί αυτόματα μια ή παραπάνω βαθμολογία από το igrades που".
	"συνδέεται με το λογαριασμό σας. Συνδεθείτε στο igrades και επιβεβαιώστε την ορθότητα των βαθμολογιών σας. Αν έχουν απομακρυνθεί".
	"βαθμολογίες που δε μπορείτε να ξανα καταχωρίσετε (γιατί είναι κλειδωμένες), επικοινωνήστε με το διαχειριστή για να τις καταχωρήσει αυτός,".
	"αφού του αναφέρετε ποια είναι η βαθμολογία και που πρέπει να τοποθετηθεί.".$crlf."Ακολουθούν διαγνωστικά μηνύματα:".$crlf.$crlf.$error_msg;
	
	$headers = 'From: noreply@spam.vlsi.gr'."\r\n".'Reply-To: noreply@spam.vlsi.gr'."\r\n".'Content-Type: text/plain; charset=UTF-8' . "\r\n" .'MIME-Version: 1.0' . "\r\n" .'Content-Transfer-Encoding: quoted-printable' . "\r\n" .'X-Mailer: PHP/'.phpversion();
	mail($to, $subject, $message, $headers);
}


// Create an quick overview of the classes
function classes_overview(){
	global $conn;
	$first_class=1;
	
	$query = "SELECT * FROM iclasses WHERE visible='1' ORDER BY id DESC ";
	$results = mysql_query($query,$conn);
	if (mysql_num_rows($results)==0){
		echo "There are no classes.";
	}else{
		while($rows = mysql_fetch_array($results)){
		
			if($first_class==1){
				$first_class=0;
			}else{
				echo "<div class='elements_seperator'></div>";
			}
			echo "<div class='element_name'>".$rows['name']."</div>";
			echo "<ul class='element_details_ul'>";
			if($rows['min_theory'] != NULL ){
				echo "<li>Limit to pass the theory : ".$rows['min_theory']."</li>";
			}
			if($rows['min_lab'] != NULL ){
				echo "<li>Limit to pass the laboratory : ".$rows['min_lab']."</li>";
			}
			if($rows['min_total'] != NULL ){
				echo "<li>Limit to pass the class : ".$rows['min_total']."</li>";
			}
			echo "</ul><br/>";
			
			$query_labs = "SELECT * FROM ilabs WHERE class_id='".$rows['id']."' ORDER BY id ASC ";
			$results_labs = mysql_query($query_labs,$conn);
			if (mysql_num_rows($results_labs)==0){
				echo "There are no laboratories / theories in this class.";
			}else{
				echo "Laboratories / Theories in this class : <br/>";
				echo "<span class='pointer scroll_arrow_left' onclick='move_class_table(0,".$rows['id'].");'><----(scroll table left)</span><span class='pointer scroll_arrow_right' onclick='move_class_table(1,".$rows['id'].");'>(scroll table right)----></span><br/><div class='contain_table'>";
				echo "<table cellpadding='5' cellspacing='5' class='centered' id='table_class_".$rows['id']."'>";
				$headers="<th></th>";
				$details_row1="<th class='align_left_text'>Grade Type </th>";
				$details_row2="<th class='align_left_text'>Included in Total </th>";
				$details_row3="<th class='align_left_text'>Locked State </th>";
				$details_row4="<th class='align_left_text'>Grade Multiplier </th>";
				while($rows_labs = mysql_fetch_array($results_labs)){
					$headers .= "<th>".$rows_labs['name']."</th>";
					if($rows_labs['type']==0){
						$details_row1 .= "<td>Laboratory</td>";
					}else{
						$details_row1 .=  "<td>Theory</td>";
					}
					if($rows_labs['include_total']==0){
						$details_row2 .= "<td>Included</td>";
					}else{
						$details_row2 .=  "<td>Not Included</td>";
					}
					if($rows_labs['lock']==0){
						$details_row3 .= "<td>Unlocked</td>";
					}else{
						$details_row3 .=  "<td>Locked</td>";
					}
					$details_row4 .= "<td>".$rows_labs['multiplier']."</td>";					
				}
				echo "<tr>".$headers."</tr><tr>".$details_row1."</tr><tr>".$details_row2."</tr><tr>".$details_row3."</tr><tr>".$details_row4."</table>";
				echo "</div>";
			}
			
		}
	}
	echo "<script type='text/javascript'> $('#main').css({'width': 'auto'});</script>";
}



// Create an quick overview of the schedules
function schedules_overview(){
	global $conn;
	$first_schedule=1;
	$week_days=array('','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
	
	$query = "SELECT schedules.*, days.onoff as daysonoff, days.title as daystitles  FROM schedules INNER JOIN days ON days.id=schedules.day+1 ORDER BY day ASC ";
	$results = mysql_query($query,$conn);
	if (mysql_num_rows($results)==0){
		echo "There are no schedules.";
	}else{
		while($rows = mysql_fetch_array($results)){
		
			if($first_schedule==1){
				$first_schedule=0;
			}else{
				echo "<div class='elements_seperator'></div>";
			}
			echo "<div class='element_name'>".$rows['title'];
			if($rows['onoff'] == 1){
				echo " <span class='subtext'>(Invisible schedule)</span>";
			}
			echo "</div>";
			echo "<div class='element_descript'>".$rows['daystitles'];
			if($rows['daysonoff'] == 0){
				echo " <span class='subtext'>(Invisible Tab)</span>";
			}
			echo "</div>";
			echo "<ul class='element_details_ul'>";
			
			echo "<li>Scheduled for : ".$rows['fin_date']."</li>";
			
			if(strlen($rows['start_minute']) == 1){
				$start_minute="0".$rows['start_minute'];
			}else{
				$start_minute=$rows['start_minute'];
			}
			if(strlen($rows['start_hour']) == 1){
				$start_hour="0".$rows['start_hour'];
			}else{
				$start_hour=$rows['start_hour'];
			}
			echo "<li>Schedule starts at ".$start_hour.":".$start_minute;
			if(strlen($rows['fin_minute']) == 1){
				$fin_minute="0".$rows['fin_minute'];
			}else{
				$fin_minute=$rows['fin_minute'];
			}
			if(strlen($rows['fin_hour']) == 1){
				$fin_hour="0".$rows['fin_hour'];
			}else{
				$fin_hour=$rows['fin_hour'];
			}
			echo " and ends at ".$fin_hour.":".$fin_minute;
			if($rows['groupschedule'] ==0){
				echo " with grouped timeslots.</li>";
			}else{
				echo " with individual timeslots.</li>";
			}
			
			
			if($rows['ar_start'] != NULL ){
				echo "<li>Auto recurring enabled from ".$week_days[$rows['ar_start']]." to ".$week_days[$rows['ar_fin']].".</li>";
			}
			
			echo "</ul><br/>";
			
			$query_slots = "SELECT COUNT(*) FROM studentslots WHERE sch_date_id LIKE '".$rows['id']."%' ";
			$results_slots = mysql_query($query_slots,$conn);
			$rows_slots = mysql_fetch_array($results_slots);
			
			if($rows['seconds'] ==30 ){
				$slotduration=$rows['minutes']+0.5;
			}else{
				$slotduration=$rows['minutes'];
			}
			$timeslots =floor( ( ($rows['fin_hour']*60 + $rows['fin_minute']) - ($rows['start_hour']*60 + $rows['start_minute']) ) / $slotduration );
			$remaining_ts = $timeslots - $rows_slots['COUNT(*)'];
			
			echo "Timeslots availability<br/><table cellpadding='5' cellspacing='5' class='centered'>";
			echo "<tr><th class='align_left_text'>Total</th>";
			echo "<th class='align_left_text'>Filled</th>";
			echo "<th class='align_left_text'>Remaining</th></tr>";
			echo "<tr><td>".$timeslots."</td><td>".$rows_slots['COUNT(*)']."</td><td>".$remaining_ts."</td></tr>";
			echo "</table>";
					
					
		}
	}
	echo "<script type='text/javascript'> $('#main').css({'width': 'auto'});</script>";
}



// Perform search for user and return plausible results
function search_user($fname,$lname,$aem){
	global $conn;
	$first_cont=0;
	$i=1;
	$pages_list = "<ul class='pages_list'>";
	$pages_list .= "<li class='pointer underline' id='page_1' onclick='change_page_all_users(1)'>1</li>";
	
	if($lname !="N/A"){
		if($first_cont==0){
			$query_extent_lname=" last_name LIKE '".$lname."%' ";
			$first_cont=1;
		}else{
			$query_extent_lname=" AND last_name LIKE '".$lname."%' ";
		}
	}else{
		$query_extent_lname="";
	}
	
	if($fname !="N/A"){
		if($first_cont==0){
			$query_extent_fname=" first_name LIKE '".$fname."%' ";
			$first_cont=1;
		}else{
			$query_extent_fname=" AND first_name LIKE '".$fname."%' ";
		}
	}else{
		$query_extent_fname="";
	}
	
	if($aem !="N/A"){
		if($first_cont==0){
			$query_extent_aem=" aem LIKE '".$aem."' ";
			$first_cont=1;
		}else{
			$query_extent_aem=" AND aem LIKE '".$aem."' ";
		}
	}else{
		$query_extent_aem="";
	}
	
	if($first_cont!=0){
	
		$query = "SELECT * FROM users WHERE ".$query_extent_lname.$query_extent_fname.$query_extent_aem." ORDER BY aem ASC ";
		$results = mysql_query($query,$conn);
		if (mysql_num_rows($results)==0){
			echo "No user found.";
		}else{
			echo "<table cellpadding='5' cellspacing='5' class='centered' id='table_users_1'>";
			echo "<tr><th>Last Name</th> <th>First Name</th> <th>A.E.M.</th><th>UserID</td> <th>E-mail</th> <th>Telephone</th> <th>Academic ID</th> <th>Access Type</th> <th>User Status</th><th></th></tr>";
			while($rows = mysql_fetch_array($results)){
			
				if( ($i % 30) == 0){
					$page = ($i/30) +1;
					$pages_list .= "<li class='pointer' id='page_".$page."' onclick='change_page_all_users(".$page.")'>".$page."</li>";
			
					echo "</table>";
					echo "<table cellpadding='5' cellspacing='5' class='hidden centered' id='table_users_".$page."'>";
					echo "<tr><th>Last Name</th> <th>First Name</th> <th>A.E.M.</th><th>UserID</th> <th>E-mail</th> <th>Telephone</th> <th>Academic ID</th> <th>Access Type</th> <th>User Status</th></tr>";
				}
				echo "<tr><td>".$rows['last_name']."</td> <td>".$rows['first_name']."</td> <td>".$rows['aem']."</td> <td>".$rows['id']." </td> <td>".$rows['email']."</td> <td>".$rows['telephone']."</td> <td>".$rows['academicid']."</td>";
				if($rows['type']=='1'){
					echo "<td>Admin</td>";
				}elseif($rows['type']=='0'){
					echo "<td>Student</td>";
				}
				if($rows['activated']=='1'){
					echo "<td>E-mail Activated</td>";
				}elseif($rows['activated']=='2'){
					echo "<td>Retired User</td>";
				}else{
					echo "<td>Inactive User</td>";
				}
				
				echo "<td><img onclick='edit_user_table(".$rows['id'].")' src='images/editicon.jpg' width='20' height='20'>
							<form action='' method='POST' id='delete_user_form_".$rows['id']."' style='display: inline;'>
								<img onclick='remove_user(".$rows['id'].")' src='images/deleteicon.png' width='20' height='20'>
								<input type='hidden' id='last_name_for_delete_".$rows['id']."' name='last_name_for_delete' value='".$rows['last_name']."' />
								<input type='hidden' id='first_name_for_delete_".$rows['id']."' name='first_name_for_delete' value='".$rows['first_name']."' />
								<input type='hidden' id='id_for_delete_".$rows['id']."' name='id_for_delete' value='".$rows['id']."' />
							</form></td>";
				echo "</tr>";
				$i++;
			}
			echo "</table> <input type='hidden' value='1' id='current_page'/>";
			echo "<br/>".$pages_list."</ul>";	
			
		}
		
	}
}
if(isset($_GET['searchuser'])){
	$security_check=0;
	
	if(isset($_GET['fname'])){
		$fname = mysql_escape_string(filter_var($_GET['fname'], FILTER_SANITIZE_STRING));
		if($fname != $_GET['fname']){
			$security_check=1;
		}
	}else{
		$fname ="N/A";
	}
	if(isset($_GET['lname'])){
		$lname = mysql_escape_string(filter_var($_GET['lname'], FILTER_SANITIZE_STRING));
		if($lname != $_GET['lname']){
			$security_check=1;
		}
	}else{
		$lname ="N/A";
	}
	if(isset($_GET['aem'])){
		$aem = mysql_escape_string(filter_var($_GET['aem'], FILTER_SANITIZE_NUMBER_INT));
		if($aem != $_GET['aem']){
			$security_check=1;
		}
	}else{
		$aem ="N/A";
	}
	
	if($security_check == 0){
		search_user($fname,$lname,$aem);
	}
}



// Show all users by page
function show_all_users($order, $dir){
	global $conn;
	$i=1;
	$pages_list = "<ul class='pages_list'>";
	$pages_list .= "<li class='pointer underline' id='page_1' onclick='change_page_all_users(1)'>1</li>";
	
	$orderby= $order." ".$dir;
	$query = "SELECT * FROM users ORDER BY ".$orderby;
	$results = mysql_query($query,$conn);
	if (mysql_num_rows($results)==0){
		echo "No users found. Who is making this search ? ! ?";
	}else{
		echo "<table cellpadding='5' cellspacing='5' class='centered' id='table_users_1'>";
		echo "<tr><th><span class='arrow up pointer' id='aup_ln' onclick=\"view_all_for_user('last_name','ASC');\"></span><span class='arrow down pointer' id='ad_ln' onclick=\"view_all_for_user('last_name','DESC');\"></span> Last Name</th> ";
		echo "<th><span class='arrow up pointer' id='aup_fn' onclick=\"view_all_for_user('first_name','ASC');\"></span><span class='arrow down pointer' id='ad_fn' onclick=\"view_all_for_user('first_name','DESC');\"></span> First Name</th> ";
		echo "<th><span class='arrow up pointer' id='aup_aem' onclick=\"view_all_for_user('aem','ASC');\"></span><span class='arrow down pointer' id='ad_aem' onclick=\"view_all_for_user('aem','DESC');\"></span> A.E.M.</th> ";
		echo "<th>E-mail</th> <th>Telephone</th> <th>Academic ID</th> <th>Access Type</th> <th>User Status</th> <th></th></tr>";
		while($rows = mysql_fetch_array($results)){
			
			if( ($i % 30) == 0){
				$page = ($i/30) +1;
				$pages_list .= "<li class='pointer' id='page_".$page."' onclick='change_page_all_users(".$page.")'>".$page."</li>";
				echo "</table>";
				echo "<table cellpadding='5' cellspacing='5' class='hidden centered' id='table_users_".$page."'>";
				echo "<tr><th>Last Name</th> <th>First Name</th> <th>A.E.M.</th> <th>E-mail</th> <th>Telephone</th> <th>Academic ID</th> <th>Access Type</th> <th>User Status</th> </tr>";
			}
			
			echo "<tr><td>".$rows['last_name']."</td> <td>".$rows['first_name']."</td> <td>".$rows['aem']."</td> <td>".$rows['email']."</td> <td>".$rows['telephone']."</td> <td>".$rows['academicid']."</td>";
			if($rows['type']=='1'){
				echo "<td>Admin</td>";
			}elseif($rows['type']=='0'){
				echo "<td>Student</td>";
			}
			if($rows['activated']=='1'){
				echo "<td>E-mail Activated</td>";
			}elseif($rows['activated']=='2'){
				echo "<td>Retired User</td>";
			}else{
				echo "<td>Inactive User</td>";
			}
			
			echo "<td><img onclick='edit_user_table(".$rows['id'].")' src='images/editicon.jpg' width='20' height='20'>
					<form action='' method='POST' id='delete_user_form_".$rows['id']."' style='display: inline;'>
						<img onclick='remove_user(".$rows['id'].")' src='images/deleteicon.png' width='20' height='20'>
						<input type='hidden' id='last_name_for_delete_".$rows['id']."' name='last_name_for_delete' value='".$rows['last_name']."' />
						<input type='hidden' id='first_name_for_delete_".$rows['id']."' name='first_name_for_delete' value='".$rows['first_name']."' />
						<input type='hidden' id='id_for_delete_".$rows['id']."' name='id_for_delete' value='".$rows['id']."' />
					</form></td>";
			echo "</tr>";
			
			$i++;
		}
		echo "</table> <input type='hidden' value='1' id='current_page'/>";
		echo "<br/>".$pages_list."</ul>";		
	}
}
if(isset($_GET['show_all_users'])){
	$order=mysql_escape_string(filter_var($_GET['orderby'], FILTER_SANITIZE_STRING));
	$dir=mysql_escape_string(filter_var($_GET['direction'], FILTER_SANITIZE_STRING));
	if( ($order=='aem' || $order=='first_name' || $order=='last_name') && ($dir=='ASC' || $dir=='DESC' ) ){
		show_all_users($order,$dir);
	}
}



/* ADD USER Functions  START */

/* Check if username is taken.*/

function usernameTaken($username){
   global $conn;
   if(!get_magic_quotes_gpc()){
      $username = addslashes($username);
   }
   $q = "select username from users where username = '$username'";
   $result = mysql_query($q,$conn);
   return (mysql_numrows($result) > 0);
}

/* Check if A.E.M is taken.*/

function aemTaken($aem){
   global $conn;
   if(!get_magic_quotes_gpc()){
      $aem = addslashes($aem);
   }
   $q = "select aem from users where aem = '$aem'";
   $result = mysql_query($q,$conn);
   return (mysql_numrows($result) > 0);
}

/* Check if E-mail is taken.*/

function emailTaken($email){
   global $conn;
   if(!get_magic_quotes_gpc()){
      $email = addslashes($email);
   }
   $q = "select email from users where email = '$email'";
   $result = mysql_query($q,$conn);
   return (mysql_numrows($result) > 0);
}

/* Add student . */

function addNewUser($username1, $password, $first_name1, $last_name1, $aem1, $ran, $telephone, $department, $email){
   $username=stripslashes($username1); 
   $first_name=stripslashes($first_name1);
   $last_name=stripslashes($last_name1); 
   $aem=stripslashes($aem1);
	
   global $conn;
   $q = "INSERT INTO users (username, password, first_name, last_name, aem, email, telephone, act_code, departmentid) VALUES ('$username', '$password', '$first_name', '$last_name', '$aem', '$email', '$telephone', '$ran', '$department')";
   $result=mysql_query($q,$conn);
   if($result){
		registrationMail($email);
		echo "User ".$last_name." ".$first_name." added.";
   }

}

/* Add Professor . */

function addNewProfessor($user, $password, $first_name1,$last_name1,$email,$ran,$telephone,$department){

   $username=stripslashes($user); 
   $first_name=stripslashes($first_name1);
   $last_name=stripslashes($last_name1); 
   global $conn;
   $q = "INSERT INTO users (username, password, first_name, last_name, aem, email, telephone, act_code, type, activated, departmentid) VALUES ('$username', '$password', '$first_name', '$last_name', '-1', '$email', '$telephone', '$ran', '1', '1', '$department')";
   $result=mysql_query($q,$conn);
   echo "User ".$last_name." ".$first_name." added.";
   return $result;

}

/* Send the registration mail.*/

function registrationMail($email){

$to      = $email;
$subject = "Schedule Manager Registration";
$message = "Welcome to our website!\r\rYou, or someone using your email address, has completed registration at index.php . You can complete registration by clicking the following link:\r https://arch.icte.uowm.gr/schedule/functions.php?actcode=".$_SESSION['ran']."\r\r If this is an error, ignore this email and you will be removed from our mailing list.";
$headers = 'From: noreply@spam.vlsi.gr'."\r\n".'Reply-To: noreply@spam.vlsi.gr'."\r\n".'X-Mailer: PHP/'.phpversion();
mail($to, $subject, $message, $headers);
}

if(isset($_GET['add_new_user_byadmin'])){

   /* Make sure all fields were entered */
   if(!$_GET['user'] || !$_GET['pass']){
      die('You didn\'t fill in a required field.');
   }

   /* Spruce up username, check length */
   $user = mysql_escape_string(filter_var(trim($_GET['user']), FILTER_SANITIZE_STRING));
   if(strlen($user) > 25){
      die("<head><title>Registration Page</title><link href='style.css' rel='stylesheet' type='text/css' media='screen'/></head><body><div id='main'><div id='container'>
<center>Sorry, the username is longer than 25 characters, please shorten it.
</center></div></div></body>");
   }

   /* Check if username is already in use */
   if(usernameTaken($user)){
      $use = $_POST['user'];
      die("<head><title>Registration Page</title><link href='style.css' rel='stylesheet' type='text/css' media='screen'/></head><body><div id='main'><div id='container'>
<center>Sorry, the username: <strong>$use</strong> is already taken, please pick another one.
</center></div></div></body>");
   }
   /* Add the new account to the database */
   $ran = mt_rand(0,9999);
   $pass = mysql_escape_string(filter_var(trim($_GET['pass']), FILTER_SANITIZE_STRING));
   $md5pass = md5($pass);
   $first_name = mysql_escape_string(filter_var(trim($_GET['first_name']), FILTER_SANITIZE_STRING));
   $last_name = mysql_escape_string(filter_var(trim($_GET['last_name']), FILTER_SANITIZE_STRING));
   $telephone = mysql_escape_string(filter_var(trim($_GET['telephone']), FILTER_SANITIZE_NUMBER_INT));
   $department = mysql_escape_string(filter_var(trim($_GET['department']), FILTER_SANITIZE_NUMBER_INT));
   $email = $_GET['email'];
   if($_GET['type']==1){
		/* Check if AEM is already in use */
		$_GET['email']=mysql_escape_string(filter_var($_GET['email'], FILTER_SANITIZE_STRING));
		if(emailTaken($_GET['email'])){
		  die("<head><title>Registration Page</title><link href='style.css' rel='stylesheet' type='text/css' media='screen'/></head><body><div id='main'><div id='container'>
	<center>Sorry, the e-mail : <strong>$email</strong> is already in use, if someone else is using yours, contact the administration.
	</center></div></div></body>");
	   }
		$regresul = addNewProfessor($user, $md5pass, $first_name,$last_name,$email,$ran,$telephone,$department);
   }else{
		/* Check if AEM is already in use */
		$_GET['aem']=mysql_escape_string(filter_var($_GET['aem'], FILTER_SANITIZE_NUMBER_INT));
		if(aemTaken($_GET['aem'])){
		  die("<head><title>Registration Page</title><link href='style.css' rel='stylesheet' type='text/css' media='screen'/></head><body><div id='main'><div id='container'>
	<center>Sorry, the A.E.M.: <strong>$aem</strong> is already in use, if someone else is using yours, contact the administration.
	</center></div></div></body>");
	   }
		$aem = $_GET['aem'];
		$regresul = addNewUser($user, $md5pass, $first_name,$last_name,$aem,$ran,$telephone,$department,$email);
   }
}
/* ADD USER Functions  END */




// Send bug report
function send_bug_report($reportdate,$bug_descipt,$other_related,$reg_related,$user_related,$sch_related,$grades_related,$grades_misc_related){
	global $conn;

	$message="<html><head><title>Bug Report ".$reportdate."</title></head><body>";
	$message.="Bug Description : <p>";
	$message.= $bug_descipt."</p><br/>";
	
	if($other_related==1){
		$reg_related=1;
		$user_related=1;
		$sch_related=1;
		$message.= "Days : <br/>";
		$message.= "<table cellspacing='4' cellpadding='4' border='1' align='center'>";
		$message.= "<tr><td>ID </td><td> Title </td><td> On/Off </td><td> Num of Schedules </td></tr>";
		$q = "SELECT * FROM days ";
		$result = mysql_query($q,$conn);
		while($row=mysql_fetch_array($result)){
			$day= $row['id']-1;
			$inquery = "SELECT * FROM schedules WHERE day='".$day."' ";
			$inresult = mysql_query($inquery,$conn);
			$numofsch=mysql_num_rows($inresult);
			
			$message.= "<tr><td>".$row['id']." </td><td> ".$row['title']." </td><td> ".$row['onoff']." </td><td> ".$numofsch."</td></tr>";
		}
		$message.= "</table><br/><br/>";
	}
	
	if($reg_related==1){
		$user_related=1;
		$sch_related=1;
		$message.= "Student Slots : <br/>";
		$message.= "<table cellspacing='4' cellpadding='4' border='1' align='center'>";
		$message.= "<tr><td>ID </td><td> Username </td><td> Sch_Code </td><td> GroupID</td></tr>";
		$q = "SELECT * FROM studentslots ";
		$result = mysql_query($q,$conn);
		while($row=mysql_fetch_array($result)){
			$inquery = "SELECT * FROM users WHERE id='".$row['user_id']."' ";
			$inresult = mysql_query($inquery,$conn);
			$inrow=mysql_fetch_array($inresult);
			
			$message.= "<tr><td>".$row['id']." </td><td> ".$inrow['username']." </td><td> ".$row['sch_date_id']." </td><td> ".$row['gid']."</td></tr>";
		}
		$message.= "</table><br/><br/>";
	}
	
	if($user_related == 1){
		$message.= "Users : <br/>";
		$message.= "<table cellspacing='4' cellpadding='4' border='1' align='center'>";
		$message.= "<tr><td>Username </td><td> AEM </td><td> Activated </td><td> Type </td><td> Last Name</td></tr>";
		$q = "SELECT username,aem,activated,type,last_name FROM users ";
		$result = mysql_query($q,$conn);
		while($row=mysql_fetch_array($result)){
			$message.= "<tr><td>".$row['username']." </td><td> ".$row['aem']." </td><td> ".$row['activated']." </td><td> ".$row['type']." </td><td> ".$row['last_name']."</td></tr>";
		}
		$message.= "</table><br/><br/>";
	}
	
	if($sch_related == 1){
		$message.= "Schedules : <br/>";
		$q = "SELECT * FROM schedules";
		$result = mysql_query($q,$conn);
		while($row=mysql_fetch_array($result)){
			$message.= "<table cellspacing='4' cellpadding='4' border='1' align='center'>";
			$message.= "<tr><td>".$row['title']."</td></tr>		<tr><td>ID: ".$row['id']."</td><td>	GroupID: ".$row['gid']."</td><td> Day: ".$row['day']."</td></tr>";
			$message.= "<tr><td>Made at: ".$row['start_date']."</td><td> Deadline: ".$row['fin_date']."</td><td> On/Off: ".$row['onoff']."</td></tr>";
			$message.= "<tr><td>Starts at: ".$row['start_hour'].":".$row['start_minute']."</td><td> Ends at: ".$row['fin_hour'].":".$row['fin_minute']."</td><td> With step: ".$row['minutes'].":".$row['seconds']."</td></tr>";
			if(isset($row['en_date'])){
				$message.= "<tr><td>Auto enable date: ".$row['en_date']."</td><td>hour:".$row['en_hour']."</td></tr>";
			}else{
				$message.= "<tr><td>No auto enable</td></tr>";
			}
			if(isset($row['ar_start'])){
				$message.= "<tr><td>Auto reccuring day: ".$row['ar_start']."</td><td>Untill:".$row['ar_fin']."</td></tr>";
			}else{
				$message.= "<tr><td>No auto reccuring</td></tr>";
			}
			$message.= "</table><br/>";
		}
	}
	
	if($grades_related == 1){
		$message.= "Grades : <br/>";
		$message.= "<table cellspacing='4' cellpadding='4' border='1' align='center'>";
		$message.= "<tr><td>lab id </td><td> user id </td><td> class id </td><td> grade </td><td> updated time</td><td> lock grade</td><td> id</td></tr>";
		$q = "SELECT * FROM igrades ";
		$result = mysql_query($q,$conn);
		while($row=mysql_fetch_array($result)){
			$message.= "<tr><td>".$row['lab_id']." </td><td> ".$row['user_id']." </td><td> ".$row['class_id']." </td><td> ".$row['grade']." </td><td> ".$row['update_time']."</td><td> ".$row['lock_grade']."</td><td> ".$row['id']."</td></tr>";
		}
		$message.= "</table><br/><br/>";
	}
	
	if($grades_misc_related == 1){
		$message.= "Classes : <br/>";
		$message.= "<table cellspacing='4' cellpadding='4' border='1' align='center'>";
		$message.= "<tr><td>class name</td><td>id </td></tr>";
		$q = "SELECT * FROM iclasses ";
		$result = mysql_query($q,$conn);
		while($row=mysql_fetch_array($result)){
			$message.= "<tr><td>".$row['name']." </td><td> ".$row['id']." </td></tr>";
		}
		$message.= "</table><br/><br/>";
		
		$message.= "Labs : <br/>";
		$message.= "<table cellspacing='4' cellpadding='4' border='1' align='center'>";
		$message.= "<tr><td>lab name</td><td>class id</td><td>id</td><td>lock</td><td>lock date</td><td>lock hour</td><td>lock minutes</td><td>multiplier</td></tr>";
		$q = "SELECT * FROM ilabs ";
		$result = mysql_query($q,$conn);
		while($row=mysql_fetch_array($result)){
			$message.= "<tr><td>".$row['name']." </td><td>".$row['class_id']." </td><td> ".$row['id']." </td><td> ".$row['lock']." </td><td> ".$row['lock_date']." </td><td> ".$row['lock_hour']." </td><td> ".$row['lock_minutes']." </td><td> ".$row['multiplier']." </td></tr>";
		}
		$message.= "</table><br/><br/>";
	}
	
	$message.="</body></html>";
	bugreport($message,$reportdate);
}
function bugreport($message,$reportdate){	
	
	$to = "e.l.d@live.com";
	$subject = "Bug Report ".$reportdate;
	$message = $message;
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	$headers .= 'From: noreply@spam.vlsi.gr' . "\r\n";
	mail($to, $subject, $message, $headers);
}
if(isset($_GET['bug_report'])){
	send_bug_report($_GET['sub_date'],$_GET['bug_description'],$_GET['other_related'],$_GET['reg_related'],$_GET['user_related'],$_GET['sch_related'],$_GET['grades_related'],$_GET['grades_misc_related']);
}



/* Load correct site based on menu */

function function_to_load($side_menu_selected){
	
	if($side_menu_selected == "home" || $side_menu_selected == "add_user"){
// LOAD ADD USER PAGE 	
?>
<div class="admin_generic_container">
	<div class="administration_main_header">Add new User</div>
	Προσθήκη : 
	<label><input type="radio" id="add_professor_option" name="add_professor_option" value="add_professor" onclick="add_new_user_selection(1);" checked="checked" />καθηγητή</label>
	<label><input type="radio" id="add_student_option" name="add_student_option" value="add_student" onclick="add_new_user_selection(0);" />φοιτητή</label>
	<br />
	<div class="admin_generic_margin_top_30" >
		<table align="left" border="0" cellspacing="0" cellpadding="3" class="centered_alt_35">
		<tr><td>Username <br />(latin characters): </td><td><input type="text" name="user" id="add_user_username" maxlength="25" /></td></tr>
		<tr><td>Password <br />(latin/greek/numbers): </td><td><input type="password" name="pass" id="add_user_pass" maxlength="25" /></td></tr>
		<tr><td>First Name <br />(greek characters): </td><td><input type="text" name="first_name" id="add_user_fname" maxlength="25" /></td></tr>
		<tr><td>Last Name <br />(greek characters): </td><td><input type="text" name="last_name" id="add_user_lname" maxlength="25" /></td></tr>
		<tr id="aem_tr" class="hidden"><td>A.E.M. <br/ >(numbers): </td><td><input type="text" name="aem" id="add_user_aem" maxlength="4" /></td></tr>
		<tr><td>E-Mail <br />(latin characters): </td><td><input type="text" name="mail" id="add_user_mail"  /></td></tr>
		<tr><td>Phone number <br />(optional but recommended) : </td><td><input type="text" name="telephone" id="add_user_phone" maxlength="10"  /></td></tr>
		<tr>
			<td>Department :</td><td><select id='add_user_department'>
			<?php
			global $conn;
			$query_dep = "SELECT * FROM departments";
			$rslt_dep = mysql_query($query_dep,$conn);
			while ($departments= mysql_fetch_array($rslt_dep)){
				echo "<option value='".$departments['id']."'>".$departments['shortname']."</option>";
			}
			?>
			</select></td>
		</tr>
		<tr><td colspan="2" align="center">
			<input type="hidden" name="type" value="1" id="add_user_type" />
			<input type="submit" name="subadduser" value="Submit" onclick="add_new_user();"/>
		</td></tr>
		</table>
	</div>
	<div class="admin_generic_margin_top_30" id="add_user_results">
	</div>
</div>
<?php	

	}elseif($side_menu_selected == "view_users"){
// LOAD VIEW USERS PAGE 	
?>
<div class="admin_generic_container" id="search_user">
	<div class="administration_main_header">Users Management</div>
	Search User
	<br /><br/> 
	First Name : <input type="text" id="search_user_fname" onkeyup="setTimeout(search_for_user, 1500);">
	<br />
	Last Name : <input type="text" id="search_user_lname" onkeyup="setTimeout(search_for_user, 1500);">
	<br />
	A.E.M. : <input type="text" size="3" id="search_user_aem" onkeyup="setTimeout(search_for_user, 1500);">
	<div class="admin_generic_margin_top_30" id="search_user_results">
	<?php
		show_all_users('aem','ASC');
	?>
	</div>
</div>
<div class="admin_generic_container hidden" id="edit_user">
	<div class="administration_main_header">Users Management</div> 
	<span class="pointer" onclick="backto_user_result();"> <--- Back to search results</span>
		
	<div class="admin_generic_margin_top_30" id="edit_user_results">
	</div>
</div>
<?php

}elseif($side_menu_selected == "cleanup_users"){
// LOAD REMOVE INACTIVE USERS PAGE 	
?>
<div class="admin_generic_container">
	<div class="administration_main_header">Remove Inactive Users</div>
	<input type='submit' name='cleanup' value='Remove Users' onclick="remove_inactive_users();" />
	<div class="admin_generic_margin_top_30" id="inactive_user_table">
	<?php 
		show_users_for_cleanup();
	?>
	</div>
</div>
<?php

	}elseif($side_menu_selected == "cleanup_grades"){
// LOAD REMOVE INVALID GRADES PAGE 	
?>
<div class="admin_generic_container">
	<div class="administration_main_header">Remove Invalid Grades</div>
	<input type='submit' name='cleanup_grades' value='Remove Grades' onclick="remove_invalid_grades();" />
	<div class="admin_generic_margin_top_30" id="inactive_grades_table">
	<?php 
		show_invalid_grades();
	?>
	</div>
</div>
<?php

	}elseif($side_menu_selected == "view_acc_id"){
// LOAD VIEW ACADEMIC ID LOGS PAGE 	
?>
<div class="admin_generic_container">
	<div class="administration_main_header">ACADEMIC ID LOGS</div>
	Show last <input type="text" size="1" class="administration_small_input" id="show_last_academicid_logs_num" value="5" onkeyup="setTimeout(show_last_academicid_logs, 1500);"> entries : 
	<div class="admin_generic_margin_top_30" id="academic_id_log_entries">
<?php
	academic_id_logs(5);
?>
	</div>
</div>
<?php

	}elseif($side_menu_selected == "view_sched"){
// LOAD VIEW SCHEDULES PAGE 	
?>
<div class="admin_generic_container">
	<div class="administration_main_header">Schedules Overview</div>
	<div>
	<?php
		schedules_overview();
	?>
	</div>
</div>
<?php

	}elseif($side_menu_selected == "view_classes"){
// LOAD VIEW CLASSES PAGE 	
?>
<div class="admin_generic_container">
	<div class="administration_main_header">Classes Overview</div>
	<div>
	<?php
		classes_overview();
	?>
	</div>
</div>
<?php
	}elseif($side_menu_selected == "bug_report"){
// LOAD VIEW CLASSES PAGE 	
?>
<div class="admin_generic_container">
	<div class="administration_main_header">Send Bug Report</div>
	<div>
		Related :<br />
		<div class="admin_inner_200_left">
		<input type="checkbox" id="user_related" value="1">Users<br>
		<input type="checkbox" id="sch_related" value="1">Schedules<br/>
		<input type="checkbox" id="reg_related" value="1">Regester on schedule<br>
		<input type="checkbox" id="other_related" value="1">Other<br>
		<input type="checkbox" id="grades_related" value="1">iGrades grades<br>
		<input type="checkbox" id="grades_misc_related" value="1">iGrades labs and classes<br>
		</div>
		<br />
		Description :<br />
		<textarea rows="4" cols="50" id="bug_description">Describe the bug here.</textarea><br>
		<input type="hidden" id="sub_date" value="<? echo date("d/m/y : H:i:s", time()); ?>" />
		<input type="submit" name="sendreport" value="Send Report" onclick="send_bug_report();" />
	</div>
</div>
<?php
	}
	
}

if(isset($_GET['side_menu_selected'])){
	$side_menu_selected=mysql_escape_string(filter_var($_GET['side_menu_selected'], FILTER_SANITIZE_STRING));
	function_to_load($side_menu_selected);
}


?>
