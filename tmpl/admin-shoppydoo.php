<?php
$options = get_option('shoppydoowp_options');
?>

	<div class="wrap">
<div>
<h3>Come va usato</h3>
<p>
	Inserire nel testo degli articoli un tag come questo:
	<br />
    <strong>[[7pixel:5,7|keywords:nexus,lg]]</strong>
</p>
<p>
	Rispettare maiuscole e minuscole per la parte sinistra del tag:
<br/>
    <strong><em>NO</em></strong> <strong>[[7PIXEL:5,7|keywords:nexus,lg]]</strong>
    <br />
    <strong><em>SÌ</em></strong> <strong>[[7pixel:5,7|keywords:nexus,lg]]</strong>
</p>

	<p>
	<strong>ATTENZIONE</strong>: Per garantire il tracciamento click delle offerte è necessario
 aggiornare le offerte ogni 5 ore come minimo.
		</p>
	
</div>
<h3>Setup</h3>
<form action="" method="post">
<div>
	PartnerId:	<input type="text" name="partnerid" value="<?=$options['partnerid']?>" /><br />
Identificativo associate program
</div>
<div>
	HEADER:	<input type="text" name="head" value="<?=$options['head']?>" /><br />
</div>
<div>
	BODY:	<textarea id="snippet-textarea" cols="50" rows="10" name="snippet"><?=$options['snippet']?></textarea>

    <p>Tag disponibili:
    <div id="avail-tags">
    <?php
foreach(parsedXmlSource::$elements as $el) {
    echo "<span><a name=\"[[$el]]\" data-value=\"$el\">[[<strong>$el</strong>]]</a></span> ";
}
    ?>
    </div>
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
	$val_arr = array(
			 '1200'=>'20 minuti',
			 '3600'=>'1 Ora',
			 '18000'=>'5 Ore',
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
			       
</div>
    <script>
    (function($)
     {
	 function fieldInjector(p)
	 {
	     var self = this;
	     this.taPos = 0;
	     $(p.textarea).on('blur',function(){ self.taPos = $(p.textarea).prop("selectionStart");});
	     $(p.aSelection).on('click',function(e){
		 var val = $(this).prop(p.prop),
		 cursorPos = self.taPos,
		 v = $(p.textarea).val(),
		 textBefore = v.substring(0,  cursorPos ),
		 textAfter  = v.substring( cursorPos, v.length );
		 console.log('cliccked',p.prop, val, v,cursorPos, "resulting.......",textBefore+ val +textAfter );
		 $(p.textarea).html("");
		 $(p.textarea).val( textBefore+ val +textAfter )
		     .prop("selectionStart",cursorPos+val.length)
		     .prop("selectionEnd",cursorPos+val.length)
		     .focus();
	     });
	     
	 }
	 $(function() {
	     fieldInjector({'textarea': "#snippet-textarea",'aSelection':'#avail-tags a','prop':'name'});
	 });
     })(jQuery);
</script>
    