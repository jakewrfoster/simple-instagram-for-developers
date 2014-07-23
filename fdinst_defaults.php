<?php
$fdinst_options = array(
	'user'		   		=> '367274847',
	'client_id'    		=> '077beab5bdd2442a92d723d888cbe337',
	'count'        		=> 5,
	'use_flex_slider'	=> 1
);
add_option('fdinst_user', $fdinst_options["user"]);
add_option('fdinst_client_id', $fdinst_options["client_id"]);
add_option('fdinst_count', $fdinst_options["count"]);
add_option('fdinst_flex', $fdinst_options["use_flex_slider"]);