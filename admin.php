<?
if(!isset($_SESSION)) {
     session_start();
}
$database_already_here=1;
include("functions.php"); 

if(isset($_SESSION['type'])){
 if($_SESSION['type']==1){
	include("header.php");
?>
<div class="side_menu">


<?php
$rc=system("/usr/local/bin/sudo /usr/local/www/clone_users_table.sh");
if ($rc) { echo "SYNCHED"; }
?>

 <div class="side_menu_category">
	<div class="side_menu_header">Users</div>
	<a href="?side_menu=add_user"><span class="side_menu_option" id="add_user">Add User</span></a>
	<a href="?side_menu=view_users"><span class="side_menu_option" id="view_users">View Users</span></a>
	<a href="?side_menu=cleanup_users"><span class="side_menu_option" id="cleanup_users">Remove Inactive Users</span></a>
	<a href="?side_menu=cleanup_grades"><span class="side_menu_option" id="cleanup_grades">Remove Invalid Grades</span></a>
 </div>
 <div class="side_menu_category">
	<div class="side_menu_header">Academic ID Log</div>
	<a href="?side_menu=view_acc_id"><span class="side_menu_option" id="view_acc_id">View Academic ID logs</span></a>
 </div>
 <div class="side_menu_category">
	<div class="side_menu_header">Schedules Overview</div>
	<a href="?side_menu=view_sched"><span class="side_menu_option" id="view_sched">View running schedules</span></a>
 </div>
 <div class="side_menu_category">
	<div class="side_menu_header">Classes Overview</div>
	<a href="?side_menu=view_classes"><span class="side_menu_option" id="view_classes">View active classes</span></a>
 </div>

 <div class="side_menu_category">
	<div class="side_menu_header">Other Actions</div>
	<a href="?side_menu=bug_report"><span class="side_menu_option" id="bug_report">Send Bug Report</span></a>
 </div>

 <div class="side_menu_category">
	<div class="side_menu_header">Websites</div>
	<a href="https://arch.icte.uowm.gr/ippower/"><span class="side_menu_option" id="ippower"> IP Power </span></a>
	<a href="http://bigb5.vlsi.gr/munin/"><span class="side_menu_option"> Munin Stats</span></a>
	<a href="http://vlsi.gr/smokepingwww/"><span class="side_menu_option"> Smokeping Stats</span></a>
	<a href="https://arch.icte.uowm.gr/awstats/"><span class="side_menu_option"> AWstats</span></a>
	<a href="http://stats.it.auth.gr/network/packet_loss/index.html"><span class="side_menu_option"> Packet Loss</span></a>
	<a href="http://vlsi.gr/voip/"><span class="side_menu_option"> Voip PBX Overrides </span></a>

 </div>

</div>

<div id="main">

</div>
<div class="hidden" id="pop_up"></div>
<?
	include("footer.php");
 }else{
	echo "You don't have administration rights.";
 }
}else{
	echo "<script>alert('Please log in first.');</script> ";
	header ("location: ../schedule/index.php");
}

?>
