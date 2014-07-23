<?php
if($_POST['fdinst_hidden'] == 'Y') {
	//Form data sent
	$fdinst_options["user"] 		    = $_POST['fdinst_user'];
	$fdinst_options["client_id"] 	    = $_POST['fdinst_client_id'];
	$fdinst_options["count"]		    = $_POST['fdinst_count'];
	$fdinst_options["use_flex_slider"]  = $_POST['fdinst_flex'];

	update_option('fdinst_user', $fdinst_options["user"]);
	update_option('fdinst_client_id', $fdinst_options["client_id"]);
	update_option('fdinst_count', $fdinst_options["count"]);
	update_option('fdinst_flex', $fdinst_options["flex"]);

	if(empty($fdinst_options["user"])) update_option('fdinst_user');

?>
<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
<?php
} else {
	// no form post
	$fdinst_options["user"]            =  get_option('fdinst_user');
	$fdinst_options["client_id"]	   =  get_option('fdinst_client_id');
	$fdinst_options["count"]		   =  get_option('fdinst_count');
	$fdinst_options["use_flex_slider"] =  get_option('fdinst_flex');
}
?>

<div class="wrap">
	<?php    echo "<h2>" . __( 'Simple Instagram Options', 'fdinst_simple' ) . "</h2>"; ?>

	<form name="fdinst_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="fdinst_hidden" value="Y">
		<p><?php _e("User: " ); ?><input type="text" name="fdinst_user" value="<?php echo $fdinst_options["user"]; ?>" size="20"><?php _e(" User ID (ex. 367274847) of the feed you would like to retrieve" ); ?></p>
		<p><?php _e("Client ID : " ); ?><input type="text" name="fdinst_client_id" value="<?php echo $fdinst_options["client_id"]; ?>" size="20"><?php _e(" Retreive this string from the Instagram API" ); ?></p>
		<p><?php _e("Number of posts to display: " ); ?><input type="text" name="fdinst_count" value="<?php echo $fdinst_options["count"]; ?>" size="20"><?php _e(" default: 5" ); ?></p>
		<p><?php _e("Use Flex Slider? " ); ?><select> <option value="<?php echo $fdinst_options['flex']; ?>">Yes</option><option value="<?php echo $fdinst_options['flex']; ?>">Yes</option> </select></p>
		<hr />

		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Options', 'fdinst_simple' ) ?>" />
		</p>
	</form>
</div>