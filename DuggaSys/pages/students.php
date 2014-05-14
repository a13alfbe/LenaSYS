<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
			<link type="text/css" href="css/style.css" rel="stylesheet">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script type="text/javascript" src="js/duggasys.js"></script>
	</head>
<body>
<?php

	include_once(dirname(__FILE__). "/../../../coursesyspw.php");	
	include_once(dirname(__FILE__) . "/../../Shared/basic.php");
    pdoConnect();

?>
	<script type="text/javascript">
		var qs = getUrlVars();
		
		function appendStudents(data){
		   var output = "";
		   // Loopar igenom all data vi från tillbaka ifrån getstudent_ajax.php.
		   $.each(data['entries'], function(){
		      output += "<tr><td>"+this.username+"</td>";
			  output += "<td>"+this.uid+"</td>";
			  output += "<td>FAIL</td>";
		      output += "<td id='deletebox1' style='display:none'><input type='checkbox' name='checkbox[]' value='"+this.uid+"'/></td></tr>";
		   });
		   $("table.list tbody").append(output);
		   
		}
	    getStudents();
		function getStudents(){
		
		  $.ajax({
            type: "POST",
            url: "./ajax/getstudent_ajax.php",
            data: "courseid="+qs.courseid,
			dataType: "JSON",
            success: function(data){
                appendStudents(data);
            },
			error: function() {
                alert("Could not retrieve students");			
			}
          });
		
		}
	</script>
	<div id="content">

	<div id="student-box">
		<div id="student-header">Studentvy</div>
		<button onclick="changeURL('addstudent?courseid=' + qs.courseid)">
			Add students 
		</button>
	<form action="" method="post">
	<div id='students'>
	<table class='list'>
	<thead>
	<tr><th>Name</th>
	<th>UserID</th>
	<th>Dugga</th>
	<th id='deletebox' style='visibility: hidden'>Delete</th></tr>
	</thead>
    <tbody>
	
    <!-- Här hamnar allt från appendStudents() -->
   
	<?php
	/*
	foreach($pdo->query( "SELECT * FROM user, user_course WHERE cid='1' and user.uid=user_course.uid" ) as $row){
		$userid = $row['uid'];
	   echo "<tr><td>".$row['username']."</td>";
	   echo "<td>".$row['uid']."</td>";
	   echo "<td>FAIL</td>";
	   echo "<td id='deletebox1' style='display:none'><input type='checkbox' name='checkbox[]' value='".$userid."'/></td></tr>";
	}
	*/ 
	?>
	
	</tbody>
	</table>

		<input id="hide" type="button" value="Tillbaka" class="submit-button" onclick="javascript:studentDelete('hide');"/>
		<input id="show" type="button" value="Redigera" class="submit-button" onclick="javascript:studentDelete('show');"/>
		<input id="deletebutton" type="submit" class="submit-button" style='visibility: hidden' value="Delete" name="delete"/>


		<?php

 	
		if (isset($_POST['delete'])) {

			if(!empty($_POST['checkbox'])) {
   				foreach($_POST['checkbox'] as $check) {
	    			$pdo->query( "DELETE FROM user_course WHERE uid='$check'" );
	    			header("Location: students.php");
   				}
			}
		}

		?>
		</form>
		</div>
	</div>
	</div>
</body>
</html>
