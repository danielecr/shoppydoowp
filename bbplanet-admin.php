<?php

require_once 'bbplanetwp.php';

if(function_exists('add_action')) {
	add_action( 'admin_menu', 'bbplanet_menu' );
}

/** Step 1. */
function bbplanet_menu() {
	add_options_page( 'BBPlanet options', 'BBPlanetWP', 'manage_options', 'bbplanet-options', 'bbplanet_options' );
}

/** Step 3. */
function bbplanet_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$options = get_option('bbplanetwp_options');
	if ( isset( $_POST['Submit'] ) ) {
		$options['ida'] = $_POST['ida'];
		$options['head'] = stripslashes($_POST['head']);
		$options['tail'] = stripslashes($_POST['tail']);
		$options['snippet'] = stripslashes($_POST['snippet']);
		if($options['tmpl_version'] >=1) {
			$options['tmpl_version'] +=1;
		} else {
			$options['tmpl_version'] = 1;
		}
		update_option('bbplanetwp_options',$options);
	}
	echo '<div class="wrap">';
	$options = get_option('bbplanetwp_options');
	?>
<form action="" method="post">
<div>
	IDA:	<input type="text" name="ida" value="<?=$options['ida']?>" /><br />
</div>
<div>
	HEAD:	<input type="text" name="head" value="<?=$options['head']?>" /><br />
</div>
<div>
	SNIPPET:	<textarea  cols="50" rows="10" name="snippet"><?=$options['snippet']?></textarea>
</div>
<div>
	TAIL:	<input type="text" name="tail" value="<?=$options['tail']?>" /><br />
</div>

<div>
	<input type="submit" name="Submit" /><br />
</div>
	</form>
<?php
	
	echo '<p>Tag disponibili:Here is where the form would go if I actually had options.</p>';
	echo "<p>";
	echo '[['. implode(']], [[',bbParsedStru::$elements) . ']]';
	echo "</p>";
	echo '</div>';
}

