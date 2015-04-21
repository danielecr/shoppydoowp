<?php

require_once 'bbplanetwp.php';

if(function_exists('add_action')) {
	add_action( 'admin_menu', 'bbplanet_menu' );
}

/** Step 1. */
function bbplanet_menu() {
	add_options_page( 'BBPlanet options', 'BBPlanetWP', 'manage_options', 'bbplanet-options', 'bbplanet_options' );
}

function bbplanet_first_option_setup()
{
	if(function_exists('update_option') && function_exists('get_option')) {
		$options = get_option('bbplanetwp_options');
		if(!$options && !$options['head']) {
			$options['head'] = '<ul>';
			$options['tail'] = '<li>
<a href="[[linkstruttura]]">[[nomestruttura]]</a> [[tipologiaestesa]]
[[descrizione]]<br />
Posti Letto: <strong>[[npostiletto]]</strong>
</li>';
			$options['snippet'] = '</ul>';
			$options['duration'] = 3600;
			$options['ida'] = '10463';
			update_option('bbplanetwp_options',$options);
		}
	}
}

/** Step 3. */
function bbplanet_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$options = get_option('bbplanetwp_options');
	if ( isset( $_POST['Expire'] ) ) {
		$storer = new bbPlanetStorer();
		$storer->expireAllVersion($options['tmpl_version']);
	}
	if ( isset( $_POST['Submit'] ) ) {
		$options['ida'] = $_POST['ida'];
		$options['head'] = stripslashes($_POST['head']);
		$options['tail'] = stripslashes($_POST['tail']);
		$options['snippet'] = stripslashes($_POST['snippet']);
		$options['duration'] = stripslashes($_POST['duration']);
		if($options['tmpl_version'] >=1) {
			$storer = new bbPlanetStorer();
			$storer->expireAllVersion($options['tmpl_version']);
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
<p>Identificativo associate program</p>
</div>
<div>
	HEADER:	<input type="text" name="head" value="<?=$options['head']?>" /><br />
</div>
<div>
	BODY:	<textarea  cols="50" rows="10" name="snippet"><?=$options['snippet']?></textarea>
<p>Aggiungi tutti i campi che verranno sostituiti per ogni struttura.</p>
	<p><strong>Attenzione:</strong> modificando il template o cambiando i parametri si invaliderà la cache attuale delle strutture</p>
</div>
<div>
	TAIL:	<input type="text" name="tail" value="<?=$options['tail']?>" /><br />
</div>

<div>
	Durata:
<select name="duration">
	<?php
	$val_arr = array('3600'=>'1 Ora',
			 '86400'=>'1 Giorno',
			 '604800'=>'1 Settimana',
		);
	foreach($val_arr as $v => $txt) {
		if($options['duration'] == $v) {
			$s = ' selected="selected"';
		} else {
			$s  ='';
		}
		?>
		<option value="<?=$v?>"<?=$s?>><?=$txt?></option>
		<?php
	}
	?>
</select>

	<input type="submit" name="Expire" value="Pulisci cache" /><br />

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

