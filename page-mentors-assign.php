<?php
	// Setup stylesheet
echo "<head><style>
/* Airtable Mentor Requests */
body {
			font-family: 'Raleway';font-size: 14px;
		}
h5 {
color: #3c3c3c;
margin-top: 6px;
margin-bottom: 6px;
}

table.customTable {
  width: 100%;
  background-color: #FFFFFF;
  border-collapse: collapse;
  border-width: 2px;
  border-color: #cccccc;
  border-style: solid;
  padding: 2px;
  color: #000000;
}

table.customTable td {
  border-width: 2px;
  border-color: #cccccc;
  border-style: solid;
  padding: 5px;
  color:white;
  background-color: red;	
}

table.customTable td, tableHead {
  background-color: #AFE1F2;
}
		
table.customTable .selector {
  background-color: white;
  padding: 10px;
		}
</style>
</head>";
	// Check whether form has been submitted
	if(isset($_POST['submit'])){
		echo "We have assigned ".$_POST["mentorAssigned"]." to request #".$_POST["assigned"];
		//Sleep for five seconds.
		//sleep(5);
		//Redirect using the Location header.
		header('Location: /mentors-assign');
		// run javascript to reload the Airtable
		echo "<script>parent.document.getElementById('requests').contentDocument.location.reload(true);</script>";
	} 

	// Define config being used from Airpress
	define("CONFIG_NAME","DemoMentors");
	
	// Query AirPress - AirpressQuery($tableName, CONFIG_NAME)


	// Get the mentor list
	$query = new AirpressQuery("Mentors", CONFIG_NAME);
	// Get the mentor request list
	$mentorQuery = new AirpressQuery("Mentor requests", CONFIG_NAME);
	$mentorQuery -> addFilter("{In progress} != 1");
	// Clear the cache so that the query runs quickly
	$query -> setExpireAfter(0);
	$mentorQuery -> setExpireAfter(0);
	
	$mentors = new AirpressCollection($query);
	$openRequests = new AirpressCollection ($mentorQuery);

	//echo "<h1>Assign a mentor</h1>";
	if(is_airpress_empty($openRequests)){
		echo "<p>There are no mentor requests at this time</p>";
	} else {
	echo "<form action='../mentors-assign' method='post'>
	   	<table class='customTable' width='200' border='1' cellspacing='1' cellpadding='1'>
	   	  <tbody>
	   	    <tr>
	   	      <td class='tableHead'><h5>Select a mentor request</h5></td>
   	        </tr>
	   	    <tr>
	   	      <td class='selector'><select id = 'requestList' name='assigned'>
	   	        ";			
		foreach($openRequests as $mentorRequest) {
			echo "
	   	        <option value = '".$mentorRequest['help needed']."'>".$mentorRequest['Help needed']."</option>
	   	        ";
		}
	echo "
   	          </select></td>
   	        </tr>
			  <tr>
	   	    <td>
				<h5>Assign a mentor</h5></td></tr>
	   	    <tr>
	   	      <td class='selector'><select name = 'mentorAssigned' id = 'mentorList'>
	   	        ";
				foreach($mentors as $mentor) {
					echo "
	   	        <option value = '".$mentor['mentor name']."'>".$mentor['Mentor name']."</option>
	   	        ";
				}
	echo "
   	          </select></td>
   	        </tr>
	   	    <tr>
	   	      <td class='selector'><input class='small-button smallsilver' name='submit' type='submit' value='Submit'></td>
   	        </tr>
   	      </tbody>
  </table>

</form>";
	}
	$helpNeeded = $_POST["assigned"];
	//echo "<p>Help needed: ". $helpNeeded;
	$assignQuery = new AirpressQuery("Mentor requests", CONFIG_NAME);
	$assignQuery -> addFilter("{Help needed} = \"$helpNeeded\"");
	//$assignQuery -> addFilter("{Help needed} = 'Another one'");
	$assignQuery -> setExpireAfter(0);
	$assignMentors = new AirpressCollection($assignQuery);
	
	if(!is_airpress_empty($assignMentors)){
		//echo "<p>Updating ...</p>";	
		//$string = $assignQuery->toString();
		//echo $string;
		foreach($assignMentors as $mentorAss) {
			$fields_to_update = array();
			//echo "<p>Updating record ID: ".$mentorAss["MRno"]." ".$helpNeeded."</p>";
			// Assign the mentor
			$fields_to_update["Assigned"] = $_POST["mentorAssigned"];
			// Set status to "in progress"
			$fields_to_update["In progress"] = "1";
			$mentorAss->update($fields_to_update); 
		} 
	} 

?>