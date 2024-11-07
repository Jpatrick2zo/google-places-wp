<?php
/**
 * Register custom fields / options for map settings and place type filters.
 */

$field_settings = [
	'google_places_field_apikey' => [
		'label' => 'API Key',
		'settings_group' => 'google_places_settings_group1',
		'settings_section' => 'google_map_settings_section',
		'field_args' => [
			'uid' => 'google_places_field_apikey',
			'class' => 'google_places_row',
			'google_places_custom_data' => 'custom',
			'type' => 'text',
			'description' => 'Google Maps API Key',
			'default' => ''
		]
	],
	/**
	 * Start Section 2 Settings 
	*/
	'google_places_field_radius_all' => [
		'label' => 'Use same radius for all places',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' 	 => 'google_places_field_radius_all',
			'parent' => true,
			'class'  => "google_places_row gp_50",
			'type' 	 => 'conditional_checkbox_text',
			'description' => 'Check this to use the same radius for every place type.',
			'default' => '',
			'default_radius' => 500
		]
	],
	
	'google_places_field_radius_all_radius' => [
		'label' => 'Default Radius',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'parent_id' => 'google_places_field_radius_all',
			'uid' => 'google_places_field_radius'
		]
	],

	'google_places_field_ptype_selectall' => [
		'label' => 'Select All Place Types',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_selectall',
			'parent_id' => 'google_places_field_radius_all',
			'class' => 'google_places_row gp-50',
			'type' => 'checkbox',
			'description' => 'Select all the available places to search for.',
			'default' => ''
		]
	],

	'google_places_field_ptype_hospital' => [
		'label' => 'Hospital',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_hospital',
			'class' => 'google_places_row',
			'type' => 'conditional_checkbox_text',
			'description' => 'Check this to include this type of place in your search.',
			'default' => '',
			'default_radius' => 500
		]
	],
	'google_places_field_ptype_hospital_radius' => [
		'label' => 'Hospital Radius',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_hospital_radius'
		]
	],

	'google_places_field_ptype_library' => [
		'label' => 'Library',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_library',
			'class' => 'google_places_row',
			'type' => 'conditional_checkbox_text',
			'description' => 'Check this to include this type of place in your search.',
			'default' => '',
			'default_radius' => 500
		]
	],

	'google_places_field_ptype_library_radius' => [
		'label' => 'Library Radius',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_library_radius'
		]
	],

	'google_places_field_ptype_restaurant' => [
		'label' => 'Restaurant',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_restaurant',
			'class' => 'google_places_row',
			'google_places_custom_data' => 'custom',
			'type' => 'conditional_checkbox_text',
			'description' => 'Check this to include this type of place in your search.',
			'default' => '',
			'default_radius' => 500
		]
	],

	'google_places_field_ptype_restaurant_radius' => [
		'label' => 'Restaurant Radius',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_restaurant_radius'
		]
	],

	'google_places_field_ptype_pharmacy' => [
		'label' => 'Pharmacy',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_pharmacy',
			'class' => 'google_places_row',
			'type' => 'conditional_checkbox_text',
			'description' => 'Check this to include this type of place in your search.',
			'default' => '',
			'default_radius' => 500
		]
	],

	'google_places_field_ptype_pharmacy_radius' => [
		'label' => 'Pharmacy Radius',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_pharmacy_radius'
		]
	],

	'google_places_field_ptype_school' => [
		'label' => 'School',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_school',
			'class' => 'google_places_row',
			'type' => 'conditional_checkbox_text',
			'description' => 'Check this to include this type of place in your search.',
			'default' => '',
			'default_radius' => 500
		]
	],

	'google_places_field_ptype_school_radius' => [
		'label' => 'School Radius',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_school_radius'
		]
	],

	'google_places_field_ptype_college' => [
		'label' => 'College',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_college',
			'class' => 'google_places_row',
			'type' => 'conditional_checkbox_text',
			'description' => 'Check this to include this type of place in your search.',
			'default' => '',
			'default_radius' => 500
		]
	],

	'google_places_field_ptype_college_radius' => [
		'label' => 'College Radius',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_college_radius'
		]
	],

	'google_places_field_ptype_park' => [
		'label' => 'Park',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_park',
			'class' => 'google_places_row',
			'type' => 'conditional_checkbox_text',
			'description' => 'Check this to include this type of place in your search.',
			'default' => '',
			'default_radius' => 500
		]
	],
	'google_places_field_ptype_park_radius' => [
		'label' => 'Park Radius',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_park_radius'
		]
	],

	'google_places_field_ptype_shopping' => [
		'label' => 'Shopping',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_shopping',
			'class' => 'google_places_row',
			'type' => 'conditional_checkbox_text',
			'description' => 'Check this to include this type of place in your search.',
			'default' => '',
			'default_radius' => 500
		]
	],

	'google_places_field_ptype_shopping_radius' => [
		'label' => 'Shopping Radius',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_shopping_radius'
		]
	],

	'google_places_field_ptype_entertainment' => [
		'label' => 'Entertainment',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_entertainment',
			'class' => 'google_places_row',
			'type' => 'conditional_checkbox_text',
			'description' => 'Check this to include this type of place in your search.',
			'default' => '',
			'default_radius' => 500
		]
	],
	'google_places_field_ptype_entertainment_radius' => [
		'label' => 'Entertainment Radius',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_entertainment_radius'
		]
	],
	'google_places_field_ptype_train_station' => [
		'label' => 'Train Station',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_train_station',
			'class' => 'google_places_row',
			'type' => 'conditional_checkbox_text',
			'description' => 'Check this to include this type of place in your search.',
			'default' => '',
			'default_radius' => 500
		]
	],

	'google_places_field_ptype_train_station_radius' => [
		'label' => 'Train Station Radius',
		'settings_group' => 'google_places_settings_group2',
		'settings_section' => 'google_places_ptype_section',
		'field_args' => [
			'uid' => 'google_places_field_ptype_train_station_radius'
		]
	]
];
	// API Key Dependant fields 
	
	$api_key_dep_fields = [
		'google_places_field_placename' => [
			'label' => 'Target Location',
			'settings_group' => 'google_places_settings_group1',
			'settings_section' => 'google_map_settings_section',
			'field_args' => [
				'uid' => 'google_places_field_placename',
				'class' => 'google_places_row',
				'google_places_custom_data' => 'custom',
				'type' => 'text',
				'description' => 'Set target location to search for nearby places.',
				'default' => ''
			]
		],

		'google_places_field_latlong' => [
			'label' => 'Location Coordinates',
			'settings_group' => 'google_places_settings_group1',
			'settings_section' => 'google_map_settings_section',
			'field_args' => [
				'uid' => 'google_places_field_latlong',
				'class' => 'google_places_row',
				'google_places_custom_data' => 'custom',
				'type' => 'text',
				'description' => "Manually enter the target location's coordinates in the format: lat,lng<br/>This field gets autopopulated when using autocomplete above.",
				'default' => ''
			]
		],
		'google_places_field_maptitle' => [
		'label' => 'Map Title',
		'settings_group' => 'google_places_settings_group1',
		'settings_section' => 'google_map_settings_section',
		'field_args' => [
			'uid' => 'google_places_field_maptitle',
			'class' => 'google_places_row',
			'google_places_custom_data' => 'custom',
			'type' => 'text',
			'description' => 'Map heading text.',
			'default' => ''
			]
		]
	];

	// Only show the API dependant fields when key is set.
	if(get_option('google_places_field_apikey')) {
		$field_settings = array_merge($field_settings, $api_key_dep_fields);
	}

	foreach($field_settings as $field_id => $settings) {
		if ($settings['settings_group'] == 'google_places_settings_group1') {
			$callback = [ $this, 'google_places_fields_cb'];
		} else {
			$callback = [ $this, 'google_places_custom_fields'];
		}
		
		// Skip conditional fields that only need to be registered not rendered by the section since they'll be output by their checkbox control field. 
		if(count($settings['field_args']) > 3) {
			$args = [
				'uid'         => $field_id,
				'class'       => 'google_places_row',
				'type'        => $settings['field_args']['type'],
				'description' => $settings['field_args']['description'],
				'default'     => $settings['field_args']['default']
			];

			if(isset($settings['field_args']['parent'])) {
				$args['parent'] = true;
			}

			if(isset($settings['field_args']['parent_id'])) {
				$args['parent_id'] = $settings['field_args']['parent_id'];
			}

			if(isset($settings['field_args']['default_radius'])) {
				$args['default_radius'] = $settings['field_args']['default_radius'];
			}

			add_settings_field(
				$field_id, 
				__( $settings['label'].':', 'google-places-wp' ),
				$callback,
				$settings['settings_group'],
				$settings['settings_section'],
				$args
			);
		}

		register_setting( $settings['settings_group'], $field_id ); 	
	}