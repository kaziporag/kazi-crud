<?php
if(isset($_REQUEST['entryid']) && $_REQUEST['entryid']!='') {
  global $wpdb;
  $data = $wpdb->get_row( "SELECT * FROM `wp_crud` WHERE id = '".$_REQUEST['entryid']."'" );
?>
  <div class="wrap wqmain_body">
    <h3 class="wqpage_heading"><?php echo esc_attr( 'Edit Entry', 'kazi-crud' ) ?></h3>
    <div class="wqform_body">
      <form class="kc-form" name="update_form" id="update_form">
        <input type="hidden" name="wqentryid" id="wqentryid" value="<?=$_REQUEST['entryid']?>" />
        <div class="wqlabel"><?php echo esc_attr( 'Your Name', 'kazi-crud' ) ?></div>
        <div class="wqfield">
          <input type="text" class="wqtextfield" name="kc_name" id="kc_name" placeholder="Enter Your Title" value="<?=$data->name?>" />
        </div>
        <div id="kc_name_message" class="wqmessage"></div>
        <div>&nbsp;</div>
        <div class="wqlabel"><?php echo esc_attr( 'Your Email Address', 'kazi-crud' ) ?></div>
        <div class="wqfield">
        <input type="email" class="wqtextfield" name="kc_email" id="kc_email" placeholder="Enter Your Title" value="<?=$data->email?>" />
        </div>
        <div id="kc_email_message" class="wqmessage"></div>
        <div>&nbsp;</div>
        <div><input type="submit" class="wqsubmit_button" id="wqedit" value="Edit" /></div>
        <div>&nbsp;</div>
        <div class="wqsubmit_message"></div>
      </form>
    </div>
  </div>
<?php
} else {
?>
<div class="wrap wqmain_body">
  <h3 class="wqpage_heading"><?php echo esc_attr( 'New Entry', 'kazi-crud' ) ?></h3>
  <div class="wqform_body">
    <form class="kc-form" name="entry_form" id="entry_form">
      <div class="wqlabel"><?php echo esc_attr( 'Your Name', 'kazi-crud' ) ?></div>
      <div class="wqfield">
        <input type="text" class="wqtextfield" name="kc_name" id="kc_name" placeholder="Enter Your Name" value="" />
      </div>
      <div id="kc_name_message" class="wqmessage"></div>
      <div>&nbsp;</div>
      <div class="wqlabel"><?php echo esc_attr( 'Your Email Address', 'kazi-crud' ) ?></div>
      <div class="wqfield">
      <input type="email" class="wqtextfield" name="kc_email" id="kc_email" placeholder="Enter Your Email Address" value="" />
      </div>
      <div id="kc_email_message" class="wqmessage"></div>
      <div>&nbsp;</div>
      <div><input type="submit" class="wqsubmit_button" id="wqadd" value="Add" /></div>
      <div>&nbsp;</div>
      <div class="wqsubmit_message"></div>
    </form>
  </div>
</div>
<?php } ?>
