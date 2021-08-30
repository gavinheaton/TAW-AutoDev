<?php
	// Check whether form has been submitted
	if(isset($_POST['submit'])){
		echo "This request has been closed";
		//Sleep for five seconds.
		//sleep(5);
		//Redirect using the Location header.
		header('Location: /mentors-close');
	} 

	// Define config being used from Airpress
	define("CONFIG_NAME","DemoMentors");
	
	// Query AirPress - AirpressQuery($tableName, CONFIG_NAME)


	// Get the mentor list
	$query = new AirpressQuery("Mentors", CONFIG_NAME);
	// Get the mentor request list
	$mentorQuery = new AirpressQuery("Mentor requests", CONFIG_NAME);
	$mentorQuery -> addFilter("{Complete} != 1");
	// Clear the cache so that the query runs quickly
	$query -> setExpireAfter(0);
	$mentorQuery -> setExpireAfter(0);
	
	$mentors = new AirpressCollection($query);
	$openRequests = new AirpressCollection ($mentorQuery);

	//echo "<h1>Assign a mentor</h1>";
	if(is_airpress_empty($openRequests)){
		echo "<p>There are no mentor requests ready to close.</p>";
	} else {
	echo "<form action='../mentors-close' method='post'>
	   	<p>
             <label>Select a request to close</label>
             <select id = 'requestList' name='assigned'>";			
		foreach($openRequests as $mentorRequest) {
			echo "<option value = '".$mentorRequest['Help needed']."'>".$mentorRequest['Help needed']."</option>";
		}
	echo "</select>
          </p>
       		<input name='submit' type='submit' value='Submit'>
    	</form>";
	}

	$helpNeeded = $_POST["assigned"];
	//echo "<p>Help needed: ". $helpNeeded;
	$assignQuery = new AirpressQuery("Mentor requests", CONFIG_NAME);
	$assignQuery -> addFilter("{Help needed} = \"$helpNeeded\"");
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
			// Set status to "in progress"
			$fields_to_update["In progress"] = "1";
			$fields_to_update["Complete"] = "1";
			$mentorAss->update($fields_to_update); 
		} 
	} 


?>