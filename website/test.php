<!-- this is a tester page 
 how to use this
 1. go to this page 
 2. check my account drop down menu

if user admin panel will not be visible option
if admin panel will be visible --> 

<?php
session_start();

// Simulate a login
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'TestUser';
$_SESSION['role'] = 'admin'; // or 'user'

header("Location: index.php");
exit;
?>
