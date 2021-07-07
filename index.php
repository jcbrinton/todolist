<!DOCTYPE html>
<html lang="en">
<head>
  <title>To Do List</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> 
  <link rel="stylesheet" href="todo.css">
  <script src="todo.js"></script> 
</head>

<body>

<div class="container" id="maincontainer">
  </br>
  <!-- header -->
  <h2><strong>To Do List</strong></h2>
  <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#createTask">
    Create New Task
  </button>
  <label class="noselect checkbox-inline"><input id='pendingcheckbox' type="checkbox" value="" checked>Pending</label>
  <label class="noselect checkbox-inline"><input id='completecheckbox' type="checkbox" value="" checked>Complete</label>
  </br>
  </br>
  
<?php

// Get credentials

$ini = parse_ini_file('todo.ini');

$servername = $ini[db_server];
$username = $ini[db_user];
$password = $ini[db_password];
$dbname = $ini[db_name];

// Establish connection

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn)
  die("Connection failed: " . mysqli_connect_error());

// SQL query to read tasks

$sql = "SELECT id, description, duedate, createdate, status FROM items ORDER BY duedate ASC";
$result = mysqli_query($conn, $sql);

// Create table and column headers
	
  echo "<table class='table table-condensed' >
		<col style='width:5%'>
		<col style='width:40%'>
		<col style='width:5%'>
		<col style='width:10%'>
		<col style='width:5%'>
		<col style='width:5%'>
		<col style='width:5%'>
		<thead>
		  <tr>
			<th>Created</th>
			<th>Task</th>
			<th>Due</th>
			<th>Status</th>
			<th>Update</th>
			<th>Subtask</th>
			<th>Delete</th>
		  </tr>
		</thead>
		<tbody>";
	
mysqli_close($conn);
	
// Establish connection for subtask
$subconn = mysqli_connect($servername, $username, $password, $dbname);
	
if (!$subconn)
	die("Connection failed: " . mysqli_connect_error());
	
// SQL query to read subtasks
	  
$subsql = "SELECT id, itemid, description FROM subitems ORDER BY itemid, id";
$subresult = mysqli_query($subconn, $subsql);

$subrows = mysqli_fetch_all($subresult, MYSQLI_ASSOC);
$idArray = array();

foreach ($subrows as $subrow) {
		
	//create rows for subtable
	
	$idArray[$subrow["itemid"]] = $idArray[$subrow["itemid"]] . "
			<tr>
				<td>". $subrow["description"]."
				</td>
				<td style='text-align: center; padding: 0px;'>
					<button style='padding: 1px 6px 4px 6px;' value='removesubtask' id='".$subrow["id"]."' class='delete btn'>
						<i class='fa fa-close'></i>
					</button>
				</td>	
			</tr>";
}


// Display each of the results and subtasks
while($row = mysqli_fetch_assoc($result)) {
	  
	$rowid = $row["id"];
	  
	// Determine styling for status column
	
	$classstatus = 'alert-warning';
	if ( strcmp($row["status"],"Complete")==0)
		$classstatus = 'alert-success';
		
	//full html for subtable
	
	$subtaskrows =    "<a href='' data-toggle='collapse' data-target='#collapse".$rowid."'>
					<small>subtasks</small>
			    </a>
			    <div id='collapse".$rowid."' class='collapse in'>
					<div>
						<table class='subtable' >
							<tbody>" . $idArray[$rowid] . "
							</tbody>
						</table>
					</div>
				</div>";
		
	//for empty subtask table
	
	if(!array_key_exists($rowid, $idArray))
	  $subtaskrows ="<em><small class='text-muted'>No subtasks</small></em>";

	
	//rows for main table
	
    echo
	"<tr class='rows' data-status='".$row["status"]."'>
		<td>". date_format(date_create(explode(" ", $row["createdate"])[0]),"m/d")."</td>
		<td id='task'><strong>". $row["description"]."</strong> ".$subtaskrows."</td>
		<td>". date_format(date_create($row["duedate"]),"m/d")."</td>
		<td>
		  <div class='".$classstatus."'>
		    <strong>". $row["status"]."</strong>
		  </div>
		</td>
		<td style='text-align: center; '>
		  <button data-id='".$rowid."' data-description='".$row["description"]."' data-duedate='".$row["duedate"]."' data-status='".$row["status"]."' data-toggle='modal' data-target='#updateTask' class='update btn btn-primary'>
		    <i class='fa fa-arrow-up'></i>
		  </button>
		</td>
		<td style='text-align: center;'>
		  <button data-id='".$rowid."' class='addSubtask btn btn-success' data-toggle='modal' data-target='#addSubtask'>
		    <i class='fa fa-plus'></i>
		  </button>
		</td>	
		<td style='text-align: center;'>
		  <button value='delete' id='".$rowid."' class='delete btn btn-danger'>
		    <i class='fa fa-close'></i>
		  </button>
		</td>	
	  </tr>";
	
  }
  
  //closing tags for main table
  
  echo "</tbody>
        </table>";	
		
?>

	<!-- modal for creating a new task -->
	<div class="modal" id="createTask">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Create a To Do List Item</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button> 
          </div>
          <div class="modal-body">
            <div class="container">
		      <form action="" method="POST">
			    <div class="form-group">
			      <label>Description:</label>
			      <input type="text" class="form-control" id="description" placeholder="Enter description" name="newdescription" required>
			    </div>
			    <div class="form-group">
				  <label class="control-label" for="date">Due Date</label>
				  <input type="date" class="form-control" id="date" name="newduedate"  required/>
			    </div>
			    <button name="submit" type="submit" class="btn btn-success" >Submit</button>
		      </form>
		    </div>
		  </div>
		</div>
      </div>
    </div>
  
    <!-- modal for updating a task -->
	<div class="modal" id="updateTask">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h4 class="modal-title">Update a To Do List Item</h4>
			<button type="button" class="close" data-dismiss="modal">&times;</button> 
		  </div>
		  <div class="modal-body">
			<div class="container">
			  <form action="" method="POST">
				<div class="form-group">
				  <input class="form-control" id="identry"  name="updateid" value="" required >
				</div>
				<div class="form-group">
				  <label for="description">Description:</label>
				  <input class="form-control" id="descriptionentry"  placeholder="Enter description" name="updatedescription" value="" required>
				</div>
				<div class="form-group">
				  <label for="duedate">Due Date:</label>
				  <input type="date" class="form-control" id="duedateentry"  name="updateduedate" value="" required >
				</div>
				<div class="form-group">
				  <label for="sel1" >Status:</label>
				  <select class="form-control" id="statusselect" name="updatestatus" value="" >
					<option value="Pending">Pending</option>
					<option value="Complete">Complete</option>
				  </select>
				</div>
				<button name="submit" type="submit"  class="btn btn-success" >Submit</button>
			  </form>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  
  	<!-- modal for creating a new subtask -->
	<div class="modal" id="addSubtask">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h4 class="modal-title">Create a Subtask</h4>
			  <button type="button" class="close" data-dismiss="modal">&times;</button>
		  </div>
		  <div class="modal-body">
			<div class="container">
			  <form action="" method="POST">
				<div class="form-group">
				  <input class="form-control" id="subtaskentry"  name="supertaskid" value="" >
				</div>
				<div class="form-group">
				  <label for="description">Description:</label>
				  <input type="text" class="form-control" id="subtaskdescription" placeholder="Enter description" name="subtaskdescription" required>
				</div>
				<button name="submit" type="submit" class="btn btn-success" >Submit</button>
			  </form>
			</div>
		  </div>
		</div>
	  </div>
	</div>

</div>

</body>
</html>

