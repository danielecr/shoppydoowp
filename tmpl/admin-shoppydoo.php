<?php
$options = get_option('shoppydoowp_options');
?>

	<div class="wrap">
<div>
<h3><?php _e('How it should be used','shoppydoowp');?></h3>
<p>
	<?php _e('Insert into posts a tag like this:','shoppydoowp'); ?>
	<br />
    <strong>[[7pixel:5,7|keywords:nexus,lg]]</strong>
</p>
<p>
	<?php _e('Respect case on the tag:','shoppydoowp'); ?>
<br/>
    <strong class="error"><em>&lt;!-- <?php _e('KO (it does not work)','shoppydoowp');?> --&gt;</em></strong> <strong>[[7PIXEL:5,7|keywords:nexus,lg]]</strong>
    <br />
    <strong><em>&lt;!-- <?php _e('OK (this will work)','shoppydoowp');?> --&gt;</em></strong> <strong>[[7pixel:5,7|keywords:nexus,lg]]</strong>
</p>

	<p>
	<strong><?php _e('Be aware','shoppydoowp'); ?></strong>: <?php _e('To be sure offers link are currectly tracked, offer list must be refresh every 5 ours, at the very least','shoppydoowp'); ?>
		</p>
	
</div>
<h3><?php _e('Setup','shoppydoowp');?></h3>
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

    <p><?php _e('Available Tags','shoppydoowp'); ?>:
    <div id="avail-tags">
    <?php
foreach(parsedXmlSource::$elements as $el) {
    echo "<span><a name=\"[[$el]]\" data-value=\"$el\">[[<strong>$el</strong>]]</a></span> ";
}
    ?>
    </div>
    </p>
</div>
<div>
TAIL:	<input type="text" name="tail" value="<?=$options['tail']?>" /><br />
</div>
<p><strong><?php _e('Warning','shoppydoowp')?>:</strong> <?php _e('Changing template or parameters, do will imply the invalidation of the whole cache of offers','shoppydoowp'); ?></p>

<div>
	<?php _e('Duration','shoppydoowp'); ?>:
<select name="duration">
	<?php
	$val_arr = array(
			 '1200'=>__('20 minutes','shoppydoowp'),
			 '3600'=>__('1 Our','shoppydoowp'),
			 '18000'=>__('5 Ours','shoppydoowp'),
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

	<input type="submit" name="Expire" value="<?php _e('Flush cache','shoppyodoowp'); ?>" /><br />

</div>
<div>
	<input type="submit" name="Submit" value="<?php _e('Submit','shoppyodoowp'); ?>" /><br />
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
    
