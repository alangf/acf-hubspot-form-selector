<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

include dirname(__DIR__) . '/hubspot-helper.php';


// check if class already exists
if( !class_exists('my_acf_field_hubspot_form_selector') ) :


class my_acf_field_hubspot_form_selector extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	10/19/2020
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct( $settings ) {
		
		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/
		
		$this->name = 'hubspot_form_selector';
		
		
		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/
		
		$this->label = __('Hubspot form selector', 'TEXTDOMAIN');
		
		
		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		
		$this->category = 'basic';
		
		
		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/
		
		$this->defaults = array();
		
		
		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('FIELD_NAME', 'error');
		*/
		
		$this->l10n = array(
			'error'	=> __('Error! Please enter a higher value', 'TEXTDOMAIN'),
		);
		
		
		/*
		*  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		*/
		
		$this->settings = $settings;

 		$hubspot_access_token = get_field('hubspot_access_token', 'option');

		$this->helper = new hubspot_helper($hubspot_access_token);
		
		// do not delete!
    	parent::__construct();
    	
	}
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	2.0.0
	*  @date	2023/12/06
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field ) {

		try {
			// Get all forms from account.
			$forms = $this->helper->get_forms();
			
			/*
			*  Create a simple text input using the 'font_size' setting.
			*/
			echo sprintf('<select name="%s" value="%s">', esc_attr($field['name']), esc_attr($field['value']));
			echo '	<option value=""></option>';

			foreach ($forms as $form) :
				$value = sprintf("%s_%s", $form->guid, $form->portalId);
				echo sprintf('	<option value="%s" %s>%s</option>', $value, $field['value'] === $value ? 'selected' : '', $form->name);
			endforeach;

			echo '</select>';
		}
		catch (Exception $e) {
			$current_page = http_build_query($_GET);
			$set_message = $current_page !== "page=acf-options" 
			 ? "<p>Set the token in the <a href='" . admin_url('admin.php?page=acf-options') . "'>options page</a>.</p>"
			 : "";
			echo "<div style='color: red'>" . $e->getMessage() . $set_message . "</div>";
		}
	}
	

	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	2.0.0
	*  @date	2023/12/06
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	

	
	function load_value( $value, $post_id, $field ) {
		
		return $value;
		
	}

	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	2.0.0
	*  @date	2023/12/06
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
	
	function format_value( $value, $post_id, $field ) {

		// bail early if no value
		if( empty($value) || !strpos($value, '_') ) {
			return $value;
		}

		$values = explode("_", $value);
		
		return hubspot_helper::get_form_embed_html($values[0], $values[1]);
	}
	
}


// initialize
new my_acf_field_hubspot_form_selector( $this->settings );


// class_exists check
endif;

?>