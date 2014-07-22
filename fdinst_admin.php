<?php
if($_POST['oscimp_hidden'] == 'Y') {
	//Form data sent
	$fdinst_options["user"] = $_POST['fdinst_user'];
	update_option('fdinst_user', $fdinst_options["user"]);

	$fdinst_options["client_id"] = $_POST['fdinst_client_id'];
	update_option('fdinst_client_id', $fdinst_options["client_id"]);

	$fdinst_options["count"] = $_POST['fdinst_count'];
	update_option('fdinst_count', $fdinst_options["count"]);

	$fdinst_options["use_flex_slider"] = $_POST['fdinst_flex'];
	update_option('fdinst_flex', $fdinst_options["flex"]);
?>
<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
<?php
} else {
	// no form post
	$dbhost = get_option('oscimp_dbhost');
	$dbname = get_option('oscimp_dbname');
	$dbuser = get_option('oscimp_dbuser');
	$dbpwd = get_option('oscimp_dbpwd');
	$prod_img_folder = get_option('oscimp_prod_img_folder');
	$store_url = get_option('oscimp_store_url');
}
?>

<div class="wrap">
	<?php    echo "<h2>" . __( 'Simple Instagram Options', 'fdinst_simple' ) . "</h2>"; ?>

	<form name="fdinst_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="fdinst_hidden" value="Y">
		<?php    echo "<h4>" . __( 'OSCommerce Database Settings', 'fdinst_simple' ) . "</h4>"; ?>
		<p><?php _e("User: " ); ?><input type="text" name="fdinst_user" value="<?php echo $fdinst_options["user"]; ?>" size="20"><?php _e(" example: 077beab5bdd2442a92d723d888cbe337" ); ?></p>
		<p><?php _e("Client ID : " ); ?><input type="text" name="fdinst_client_id" value="<?php echo $fdinst_options["client_id"]; ?>" size="20"><?php _e(" default: 367274847" ); ?></p>
		<p><?php _e("Number of posts to display: " ); ?><input type="text" name="fdinst_count" value="<?php echo $fdinst_options["count"]; ?>" size="20"><?php _e(" default: 5" ); ?></p>
		<p><?php _e("Use Flex Slider? " ); ?><select> <option value="<?php echo $fdinst_options['flex']; ?>">Yes</option><option value="<?php echo $fdinst_options['flex']; ?>">Yes</option> </select></p>
		<hr />
		<?php    echo "<h4>" . __( 'OSCommerce Store Settings', 'fdinst_simple' ) . "</h4>"; ?>
		<p><?php _e("Store URL: " ); ?><input type="text" name="oscimp_store_url" value="<?php echo $store_url; ?>" size="20"><?php _e(" ex: http://www.yourstore.com/" ); ?></p>
		<p><?php _e("Product image folder: " ); ?><input type="text" name="oscimp_prod_img_folder" value="<?php echo $prod_img_folder; ?>" size="20"><?php _e(" ex: http://www.yourstore.com/images/" ); ?></p>


		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Options', 'fdinst_simple' ) ?>" />
		</p>
	</form>
</div>