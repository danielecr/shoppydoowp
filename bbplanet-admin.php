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
		$curversion = 0;
		if($options['tmpl_version'] >=1) {
			$curversion = $options['tmpl_version'];
		}
		$options['ida'] = $_POST['ida'];
		$options['head'] = stripslashes($_POST['head']);
		$options['tail'] = stripslashes($_POST['tail']);
		$options['duration'] = stripslashes($_POST['duration']);

		$advance_ver = false;
		$newsnipp = stripslashes($_POST['snippet']);
		if($newsnipp != $options['snippet']) {
			$options['snippet'] = $newsnipp;
			$advance_ver = true;
		}
		if(isset($_POST['lang'])) {
			$options['lang'] = stripslashes($_POST['lang']);
			$advance_ver = true;
		}
		if($advance_ver) {
			$storer = new bbPlanetStorer();
			$storer->expireAllVersion($curversion);
			$options['tmpl_version'] = $curversion+1;
		} else {
			$options['tmpl_version'] = $curversion;
		}
		update_option('bbplanetwp_options',$options);
	}
	echo '<div class="wrap">';
	$options = get_option('bbplanetwp_options');
	?>
<div>
<h3>Come va usato</h3>
<p>
	Inserire nel testo degli articoli un tag come questo:
	<br />
	<strong>[[bbplanet:Lecce|cat:Albergo]]</strong>
</p>
<p>
	Rispettare maiuscole e minuscole, si possono indicare più citta:
<br/>
	<strong>[[bbplanet:Taviano,Gallipoli|cat:BB]]</strong>
</p>
<p>
	o più categorie:
<br />
	<strong>[[bbplanet:Gallipoli|cat:Albergo,BB]]</strong>
</p>
<p>
	o entrambe:
<br />
	<strong>[[bbplanet:Gallipoli,Taviano|cat:Albergo,BB]]</strong>
</p>
<p>
	Non indicando la categoria verranno inserite tutti i tipi di strutture
<br />
	<strong>[[bbplanet:Gallipoli,Taviano]]</strong>
</p>
	<p>Elenco categorie strutture:
<strong><?php echo implode('</strong>, <strong>',bbPlanetStru::$STRUCT_TYPES); ?></strong>
<p>
	È anche possibile indicare una intera regione o provincia, ma il numero di risultati sarà alto
</p>
	
</div>
<h3>Setup</h3>
<form action="" method="post">
<div>
	IDA:	<input type="text" name="ida" value="<?=$options['ida']?>" /><br />
Identificativo associate program
</div>
<div>
	Lingua:
<select name="lang">
<?php
	$avail_lang = array('IT', 'EN', 'ES', 'DE', 'PT', 'SE', 'RU');
	foreach($avail_lang as $l) {
	if($options['lang'] == $l) $s = ' selected="selected"';
	else $s = '';
	?>
<option value="<?=$l?>"<?=$s?>><?=$l?></option>
<?php
}
	?>
</select>
	(* cambiare lingua invalida la cache)
</div>
<div>
	HEADER:	<input type="text" name="head" value="<?=$options['head']?>" /><br />
</div>
<div>
	BODY:	<textarea  cols="50" rows="10" name="snippet"><?=$options['snippet']?></textarea>

<p>Tag disponibili:
<?php	echo '[[<strong>'. implode('</strong>]], [[<strong>',bbParsedStru::$elements) . '</strong>]]'; ?>
</p>
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
	
	echo '</div>';
}

