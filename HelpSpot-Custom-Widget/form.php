<?php
//------------------------------------------------------------------------------
// Variables, arrays, etc., that we will need
//------------------------------------------------------------------------------
$options = array();
$errors = array();

//------------------------------------------------------------------------------
// form field values
//------------------------------------------------------------------------------
$first_name = '';
$last_name = '';
$email = '';
$phone = '';
$department = '';
$message = '';
$read_faqs = false;

//------------------------------------------------------------------------------
// load the default options (passed in from our jQuery plugin)
//------------------------------------------------------------------------------
foreach( $_GET as $k => $v ) $options[$k] = urldecode($v);

//------------------------------------------------------------------------------	
// Now, let's set the default form field values (if there were any)
//------------------------------------------------------------------------------
$first_name =  $options['default_first_name']  ? $options['default_first_name'] : '';
$last_name =  $options['default_last_name']  ? $options['default_last_name'] : '';
$email = $options['default_email'] ? $options['default_email'] : '';
$phone = $options['default_phone'] ? $options['default_phone'] : '';
$department = $options['default_department'] ? $options['default_department'] : '';
$message = '';
$read_faqs = false;


//------------------------------------------------------------------------------
// Use the HelpSpot API to Get the support categories 
// We use this for the the "departments" dropdown box
//------------------------------------------------------------------------------
$url = $options['host'] . 'api/index.php?method=request.getCategories&output=json';
$resp = @file_get_contents($url);
$cats = $resp ? json_decode($resp) : array();
//var_dump($cats);



//------------------------------------------------------------------------------
//
// Finally, start printing out the page
//
//------------------------------------------------------------------------------
?>

<?php
//------------------------------------------------------------------------------
// If we want to use AJAX Validation then we need to include the script 
//------------------------------------------------------------------------------
?>
<?php if( isset($options['ajax_validation']) && $options['ajax_validation'] == 'true' ): /* notice that 'true' is a string, not boolean! */ ?>
	<script type="text/javascript" src="<?php echo $options['base']; ?>assets/jquery.validate.min.js"></script>
<?php endif; ?>

<script type="text/javascript">
	$(function(){
		
		<?php
		//--------------------------------------------------------------------------
		// Are we using AJAX Validation? If so, this is where we set it up 
		//--------------------------------------------------------------------------
		?>
		<?php if( isset($options['ajax_validation']) && $options['ajax_validation'] == 'true' ): ?>
			
			var validator = $('#frm_cw_form').validate({
				
				// This is the action that gets taken when the form is submitted
				// (that is, if we are using the ajax_validation option)
				submitHandler: function() {
					
					// hide any existing error messages
					$('#cw_errors').slideUp(300, function(){ $(this).html('') });
					
					// Temporarily Hide the submit button 
					$("#<?php echo $options['submit_button_id']; ?>").fadeOut(100, function(){
						
						// Fade out the form while it's being processed
						$('#cw_form_holder').animate({opacity:.2}, 150);
						
						// Show the "submitting..." message in place of the Submit button
						$('#cw_status_text').removeClass('error').addClass('loading').html("<?php echo $options['submitting_text']; ?>").fadeIn(150);	
					});
					
					// Build the query string for our AJAX call
					var qs = '';
					qs += '<?php echo http_build_query($options); ?>';
					qs += '&timestamp=' + $('#cw_timestamp').val();
					qs += '&token=' + $('#cw_token').val();
					
					// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
					// don't forget to add your field names!
					// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
					
					qs += '&sFirstName=' + encodeURIComponent($('#cw_f_name').val());
					qs += '&sLastName=' + encodeURIComponent($('#cw_l_name').val());
					qs += '&sEmail=' + encodeURIComponent($('#cw_email').val());
					qs += '&sPhone=' + encodeURIComponent($('#cw_phone').val());
					qs += '&xCategory=' + encodeURIComponent($('#cw_category').val());
					qs += '&tNote=' + encodeURIComponent($('#cw_message').val());
					qs += '&read_faqs=' + encodeURIComponent($('#cw_read_faq').attr('checked'));
					
					// Now, let's make the AJAX call that submits the form and creates a new ticket
					$.ajax({
						url: "<?php echo $options['base'];?><?php echo $options['ajax']; ?>", 
						data: qs, 
						dataType: 'json',
						success: function(data) {
							
							// success!
							if( data.status == 'success' ) {
								
								// slide the up the form to hide it
								$('#frm_cw_form').slideUp(300, function(){
									
									// Add the AccessKey to the <span> in the "success" message
									$('span#new_access_key').html(data.data.accesskey);
									
									// Append the AccessKey to the "view_ticket" link in the "success" message
									$('a#view_ticket_link').attr('href', $('a#view_ticket_link').attr('href') + (data.data.accesskey));
									
									// Finally, show the "success" message
									$('#cw_success').fadeIn(300);
								});

							// Uh-Oh! There was an error!
							} else {
								
								// create a variable to hold our errors
								var err_html = '<h3>The Following Errors Occurred</h3>';
								
								// add each returned error to our err_html variable
								jQuery.each(data.data, function(i, val) {
  								err_html += "<p>" + val.description + "</p>";
  							});
  							
  							// Show the form again since there were errors
  							$('#cw_form_holder').animate({opacity:1}, 150);
  							
  							// show the error messages at the top of the form
								$('#cw_errors').html(err_html).slideDown(300);
								
								// Hide the "submitting..." text 
								$('#cw_status_text').fadeOut(100, function(){
									
									// Show the submit button again
									$("#<?php echo $options['submit_button_id']; ?>").fadeIn(100);
									
									// Show an "error" message by the submit button
									$(this).removeClass('loading').addClass('error').html('There were problems trying to submit your request').fadeIn(100);
									
								});
							}
						}, 
						error: function() {
							// TODO: add some form of error handler here
						}
					});
					return false;
				}
				<?php
				//----------------------------------------------------------------------
				// If you want custom error messages, etc. then this is the place to 
				// add them. Please see the jQuery Validation page for more info:
				// http://bassistance.de/jquery-plugins/jquery-plugin-validation/
				//----------------------------------------------------------------------
				?>
				
			});
		
		
		<?php 
		//--------------------------------------------------------------------------
		// If we're not using AJAX validation, then the following jQuery function 
		// will fire whenever the form's submit button is pressed 
		// Note: You will need to come up with your own method of validating the
		// data, submitting it to the API, etc...
		//--------------------------------------------------------------------------
		else: ?>
			
			//------------------------------------------------------------------------
			// Action to take when the form's submit button is pressed
			//------------------------------------------------------------------------
			$("#<?php echo $options['submit_button_id']; ?>").click(function(){
				// your code here
			});		
			
		<?php endif; ?>
		
	});	
</script>

<div id="cw_form_container" class="cw_form">
	
	<?php
	//----------------------------------------------------------------------------
	// This is where we'll display any errors returned from the API
	//----------------------------------------------------------------------------
	?>	
	<div id="cw_errors" style="display:none;"></div>
		
	<?php
	//----------------------------------------------------------------------------
	// This is the message area that is displayed upon success
	//----------------------------------------------------------------------------
	//
	// The key things to note here are: 
	//
	// 	- there is a <span> with an id of "new_access_key" 
	//		this is where the user's accesskey gets display
	//
	//  - there is an <a> with an ID of "view_ticket_link"
	//		the user's accesskey is APPENDED to this URL 
	//
	//----------------------------------------------------------------------------
	// You can style the message however you'd like, just please be sure to keep
	// the above mentioned items in tact so that the code will work correctly.
	//----------------------------------------------------------------------------
	?>	
	<div id="cw_success" style="display:none;">
		<h2><?php echo stripslashes($options['success_headline_text']); ?></h2>
		<p><?php echo stripslashes($options['success_body_text']); ?></p>
	
		<p id="view_ticket">
			Your <i>Access Key</i> (used to view this ticket) is: <span id="new_access_key" class="access_key"></span><br /><br />
			You may view this ticket here: 
			<a id="view_ticket_link" target="_blank" href="<?php echo $options['host'] ;?>index.php?pg=request.check&id=">
				View this Ticket
			</a>
		</p>
	</div>
	<?php
	//----------------------------------------------------------------------------
	// This is the end of the "sucess" message area
	//----------------------------------------------------------------------------
	?>
	
	
	<?php
	//----------------------------------------------------------------------------
	// Print out the HTML form
	//----------------------------------------------------------------------------
	?>
	
	<form id="frm_cw_form" action="" method="post">
		
		<div id="cw_form_holder">
			<!-- First Name -->
			<div style="width: 48%; float:left;">
				<p>
					<label class="desc" for="cw_f_name">First Name</label>
					<input id="cw_f_name" class="text full required" type="text" name="sFirstName" value="<?php echo strip_tags($first_name); ?>" />
				</p>
			</div>
			
			<!-- Last Name -->
			<div style="width: 48%; float:right;">
				<p>
					<label class="desc" for="cw_l_name">Last Name</label>
					<input id="cw_l_name" class="text full required" type="text" name="sLastName" value="<?php echo strip_tags($last_name); ?>" />
				</p>
			</div>
			
			<div style="clear:both; margin: 0; padding: 0;"></div>
			
			<!-- Email Address -->
			<p>
				<label class="desc" for="cw_email">Email Address</label>
				<input id="cw_email" class="text full email required" type="text" name="sEmail" value="<?php echo strip_tags($email); ?>" />
			</p>
			
			<!-- Phone Number -->
			<p>
				<label class="desc" for="cw_phone">Phone Number</label>
				<input id="cw_phone" class="text full" type="text" name="sPhone" value="<?php echo strip_tags($phone); ?>" />
			</p>
			
			<!-- Department/Category -->
			<p>
				<label class="desc" for="cw_email">Please choose a department</label>
    	
				<?php if( $options['demo'] == 'true' ): ?>
					<small>Not available in demo mode</small>
    	
				<?php else: ?>
				
				<select name="xCategory" id="cw_category" class="text auto required">
					<option></option>
					<?php if( !empty($cats) ): ?>
						<?php foreach( $cats->category as $cat  ): ?>
							<option <?php if($department == $cat->xCategory) echo 'selected="selected"'; ?> value="<?php echo $cat->xCategory; ?>"><?php echo $cat->sCategory; ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
				
				<?php endif; ?>
			</p>
			
			<!-- Message -->
			<p>
				<label class="desc" for="cw_message">Message</label>
				<textarea id="cw_message" name="tNote" class="text full required" cols="5" rows="7"><?php echo strip_tags($message); ?></textarea>
			</p>
			
			<!-- Confirm readind FAQs -->
			<p>
				<label class="label" for="cw_read_faq">
					I have read the <a href="<?php echo $options['host']; ?><?php echo $options['faqs']; ?>" target="_blank">FAQs</a> and checked the <a href="<?php echo $options['host']; ?>" target="_blank">Support Center</a> for an answer to my question
				</label>
				<input class="required" id="cw_read_faq" <?php if($read_faqs == true) echo 'checked="checked"'; ?> type="checkbox" name="read_faq" value="1" />  
			</p>
		</div>
		
		<!-- Button(s) and hidden form fields -->		
		<p class="buttons">
			<input id="<?php echo $options['submit_button_id']; ?>" type="submit" class="button" name="btn_submit" value="<?php echo $options['submit_button_text']; ?>" />
			<span id="cw_status_text" style="display:none;"></span>
			<?php foreach($options as $k => $v): ?>
				<input type="hidden" name="options[<?php echo $k; ?>]" value="<?php echo $v; ?>" />
			<?php endforeach; ?>
			<input id="cw_portal" type="hidden" name="xPortal" value="<?php echo $options['portal']; ?>" />
			<input id="cw_timestamp" type="hidden" name="timestamp" value="<?php echo $timestamp = time(); ?>" />
			<input id="cw_token" type="hidden" name="token" value="<?php echo sha1($timestamp.sha1('phr34k')); ?>" />
		</p>
	
	</form>
	
	
</div>
<!-- end custom widget form -->