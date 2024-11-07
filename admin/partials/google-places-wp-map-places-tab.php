<?php 
$api_key = get_option('google_places_field_apikey'); 

if(!$api_key) { ?>
        <div class="google-maps-wp-apikey-error">
            <?php 
                wp_admin_notice(
                    __('Please provide a Google API Key first before performing a lookup.', 'google-places-wp' ),
                    array(
                        'type'               => 'error',
                        'dismissible'        => true,
                        'additional_classes' => array( 'inline', 'notice-alt' ),
                        'attributes'         => array( 'data-slug' => 'google-places-wp' )
                    )
                );
            ?>
        </div>
    <?php } ?>

<div class="google-places-wp-map-places">

    <div class="google-places-wp-places-types">
        <form id="google-places-wp-place-types-form" method="post" action="<?php echo admin_url('admin-ajax.php'); ?> ">
            <?php $this->do_conditional_settings_section('google_places_settings_group2'); ?>
            <div class="google-places-wp-places settings-buttons">
                <button class="button button-primary google-places-wp-ptype-save-settings" disabled>Save Search Settings</button>
                
                <button class="button button-primary google-places-wp-find-places" <?php echo (!$api_key) ? 'disabled' : ''; ?>>Start Search</button>
            </div>
        </form>        
    </div>

    <div class="places-results">
        <div class="google-places-wp-results">

            <div class="google-places-wp-map-container">
                <div id="admin-map" style="width: 100%; height: 500px;">
                    <?php if(!$api_key) { ?>
                        <div class="google-places-wp-map-noload">
                            <div class="missing-link"></div>
                            <p><?php echo __('API Key Required', 'google-places-wp'); ?></p>
                        </div>
                    <?php } ?>
                </div>

                <div class="google-places-wp-results-drawer">
                    <div class="opener">
                        <div class="opener-chevron">
                            <div class="left"></div>
                            <div class="right"></div>
                        </div>
                    </div>
                    <div class="google-places-wp-places-results-container">
                        <table class="google-places-wp-results wp-list-table widefat fixed striped table-view-list">
                            <thead>
                                <th>Name</th>
                                <th>Address</th>
                                <th>URL</th>
                                <th>Type</th>
                                <th></th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="google-places-wp-places-controls">
                <div>
                    <button class="button button-primary google-places-wp-save-results" disabled>Save Results</button>
                </div>
            </div>
        </div>
    </div>    
</div>
					
