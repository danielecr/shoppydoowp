<h4><?php _e('Create your Shoppydoo tag','earnfromsd');?></h4>
  <div>
  <?php _e('Category selection','earnfromsd'); ?>: 
    <select class="category"></select>
    <div class="hint">
    <h6 style="margin-bottom: 2px;"><?php _e('Path','earnfromsd');?>:</h6>
    <p style="margin-top: 2px;" id="catpath"></p>
    </div><!-- end hint -->
    
    <button class="add-category"><?php _e('Add','earnfromsd');?></button>
    <button id="resetpath"><?php _e('Reset selector','earnfromsd');?></button>

    <div id="category-list">
    </div>

</div>
  <div>
    <?php _e('Add keyword','earnfromsd');?>:
<input id="keywords-sel" />
  </div>

  <div>
    <?php _e('Numberr of links','earnfromsd');?>:
    <select id="selection-limit">
    <option value="TH">2</option>
    <option value="ZF">5</option>
    <option value="TN" selected="selected">10</option>
    <option value="FT">15</option>
    <option value="TW">20</option>
    <option value="TF">25</option>
    <option value="TT">30</option>
    </select>
  </div>

  <div><?php _e('Tag','earnfromsd');?>: 
      <input id="shoppy-the-tag" size="40" class="resulting-tag" name="the_tag" readonly="readonly" />
  </div>
  <div>
    <button id="clearcatlist"><?php _e('Reset categories','earnfromsd');?></button>
    <button id="sdwp-insert-btn"><?php _e('Insert','earnfromsd');?></button> (<?php _e('into this post','earnfromsd');?>)
  </div>
