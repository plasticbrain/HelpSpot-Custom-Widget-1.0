<?php
sleep(5);
//------------------------------------------------------------------------------
// function to return our output messages to the screen
//------------------------------------------------------------------------------
function msg($status, $msg, $data=array() ){
	$m = array( 'status' => $status, 'message' => $msg, 'data' => $data);
	print json_encode($m);
	exit();
}

//------------------------------------------------------------------------------
// Function to check our security token
//------------------------------------------------------------------------------
function check_token($token, $key) {
	if( $token != sha1($key.sha1('phr34k')) ) msg('error', 'invalid token');
}

//------------------------------------------------------------------------------
// Function to check for the data that is required for the API call
// For more info see the HelpSpot page for the 'request.create' API method: 
// http://www.helpspot.com/helpdesk/index.php?pg=kb.page&id=163#request.create
//------------------------------------------------------------------------------
function check_for_required_data($data) {
	$errors = array();
	
	$required_fields = array('tNote');
	foreach( $required_fields as $field ) {
		if( !in_array($field, $data) ) $error[] = "Field \"$field\" is missing!";
	}
	
	// NOTE: In addition to tNote you must also have at least one of the following set: 
	// sFirstName,sLastName,sUserId,sEmail,sPhone.
	if( !isset($data['sFirstName']) && !isset($data['sLastName']) &&
		  !isset($data['sUserId']) && !isset($data['sEmail']) && !isset($data['sPhone']) ) {
		$errors[] = 'At least one of the following fields must be included: sFirstName, sLastName, sUserId, sEmail, sPhone';
	}
	return empty($errors) ? TRUE : msg('error', 'Missing Fields', $errors);
}

//------------------------------------------------------------------------------
// Submit the data to the API using cURL
//------------------------------------------------------------------------------
function submit($data) {
	
	$errors = array();
	$msg = '';
	$msg_data = '';
	
	// The fields this API call accepts:
	$api_fields = array( 
		'tNote', 'xCategory', 'sFirstName', 'sLastName', 
		'sUserId', 'sEmail', 'sPhone', 'fUrgent', 'xPortal', 
	);
	
	// Format the query string
	$post_data = '';
	foreach( $data as $k => $v ) {
		if( in_array($k, $api_fields) && $v != '' ) $post_data .= "$k=" . urlencode($v) . '&';
	}
	$post_data = rtrim($post_data, '&');
	
	// Setup the URL
	$url = $data['host'].'api/index.php?method=request.create&output=json';
	
	// Fire up cURL and submit the API call
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$results = @curl_exec($ch);
	@curl_close($ch);
	
	// No results?	
	if( $results === false ) {
		msg( 'error', 'The URL could not be reached!' );
	}
	
	$results = json_decode($results);
		
	// Errors?
	if( isset($results->error) ) {
		msg('error', 'The following errors occurred', $results->error);
		
	// Success!
	} else {
		msg('success', 'Your ticket was created!', $results);
	}
	
}


//------------------------------------------------------------------------------
// Finally, let's start the actual code
//------------------------------------------------------------------------------

// Step 1: check the token
check_token($_GET['token'], $_GET['timestamp']);

// Step 2: the API call we make requires certain data. Let's make sure it exists
check_for_required_data($_GET);

// Step 3: Submit the to the API using cURL
submit($_GET);
?>