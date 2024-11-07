<form method="post" id="google_places_settings" action="options.php"> 
    <?php 
        settings_fields( 'google_places_settings_group1'); 
        do_settings_sections( 'google_places_settings_group1' ); 
    ?>

    <input type="hidden" name="google_places_field_placename_short" id="google_places_field_placename_short" value="<?php echo (!empty(get_option('google_places_field_placename_short'))) ? get_option('google_places_field_placename_short') : ''  ?>">

    <input type="hidden" name="google_places_field_streetaddress" id="google_places_field_streetaddress" value="<?php echo (!empty(get_option('google_places_field_streetaddress'))) ? get_option('google_places_field_streetaddress') : ''  ?>">

    <input type="hidden" name="google_places_field_city" id="google_places_field_city" value="<?php echo (!empty(get_option('google_places_field_city'))) ? get_option('google_places_field_city') : ''  ?>">

    <input type="hidden" name="google_places_field_url" id="google_places_field_url" value="<?php echo (!empty(get_option('google_places_field_url'))) ? get_option('google_places_field_url') : ''  ?>">

    <?php submit_button('Save Settings'); ?>
   
</form>
