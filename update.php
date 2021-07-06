<?php

// Get credentials
$ini = parse_ini_file('config/todo.ini');

$servername = "localhost:3306";
$username = $ini[db_user];
$password = $ini[db_password];
$dbname = $ini[db_name];

// Establish connection

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn)
	
	die(mysqli_connect_error());

// SQL query for creating a new task

if (!empty($_POST['newdescription'])) {
	
	$descriptions = $_POST['newdescription'] ; 
	$duedates = $_POST['newduedate'] ; 

	$sql = "INSERT INTO items (description, duedate )
	VALUES ('$descriptions', '$duedates')";
}

// SQL query for updating an existing task

if (!empty($_POST['updateid'])) {
	
	$ids = $_POST['updateid'] ; 
	$statuses = $_POST['updatestatus'] ;
	$descriptions = $_POST['updatedescription'] ;
	$duedates = $_POST['updateduedate'] ;
	$sql = "UPDATE items SET  description='$descriptions' ,duedate='$duedates' ,status='$statuses'  WHERE id='$ids'";
   
}

// SQL query for creating a new subtask

if (!empty($_POST['subtaskdescription'])) {
	
	$descriptions = $_POST['subtaskdescription'] ; 
	$ids = $_POST['supertaskid'] ; 
   
	$sql = "INSERT INTO subitems (itemid, description)
	VALUES ('$ids' ,'$descriptions')";
}

// SQL query for deleting a task and all its subtasks

if (!empty($_POST['delete'])) {
	
	$ids = $_POST['delete'] ;

	$sql = "DELETE FROM items WHERE id='$ids'";

	if (mysqli_query($conn, $sql))
		
		echo "Database updated successfully";
		
	else
		
	  echo "Error: " . $sql . "<br>" . mysqli_error($conn);

	$sql = "DELETE FROM subitems WHERE itemid='$ids'";
	
}

// SQL query for deleting a subtask

if (!empty($_POST['removesubtask'])) {
	
	$ids = $_POST['removesubtask'] ;

	$sql = "DELETE FROM subitems WHERE id='$ids'";
   
}

// Handle errors and close connection

if (mysqli_query($conn, $sql))
	
	echo "Database updated successfully";
  
else
	
	echo "Error: " . $sql . "<br>" . mysqli_error($conn);

mysqli_close($conn);
?>