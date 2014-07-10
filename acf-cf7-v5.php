<?php

class acf_field_cf7 extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/
		
		$this->name = 'cf7';
		
		
		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/
		
		$this->label = __('Contact Form 7', 'acf-cf7');
		
		
		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		
		$this->category = 'relational';
		
		
		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/
		
		$this->defaults = array(
			'allow_null'	=> 0,
			'multiple'		=> 0,
			'disable'		=> ''
		);
		
		
		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('cf7', 'error');
		*/
		
		$this->l10n = array(
			'error'	=> __('Error! Please enter a higher value', 'acf-cf7'),
		);
		
				
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field_settings( $field ) {
		
		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/
		
		acf_render_field_setting( $field, array(
			'label'			=> __('Allow Null?','acf-cf7'),
			'type'			=> 'radio',
			'name'			=> 'allow_null',
			'choices' =>  array(
			  	1 =>  __("Yes", 'acf'),
			  	0 =>  __("No", 'acf'),
			),
			'layout'  =>  'horizontal'
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Select Multiple?','acf-cf7'),
			'type'			=> 'radio',
			'name'			=> 'multiple',
			'choices' =>  array(
			  	1 =>  __("Yes", 'acf'),
			  	0 =>  __("No", 'acf'),
			),
			'layout'  =>  'horizontal'
		));

		//Get form names
		$forms = get_posts(array('post_type' => 'wpcf7_contact_form', 'orderby' => 'id', 'order' => 'ASC', 'posts_per_page' => 999, 'numberposts' => 999));  
		$choices = array();
		$choices[0] = '---';
		$k = 1;
		foreach($forms as $f){
		    $choices[$k] = $f->post_title;
		    $k++;
		} 
		acf_render_field_setting( $field, array(
			'label'			=> __('Disable Forms?','acf-cf7'),
			'instructions'	=> __('User will not be able to select these forms','acf-cf7'),
			'type'			=> 'select',
			'name'			=> 'disable',
			'multiple'		=> '1',
			'allow_null'  =>  '0',
			'choices' =>  $choices
		));

	}
	
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field ) {
		$field['multiple'] = isset($field['multiple']) ? $field['multiple'] : false;
		$field['disable'] = isset($field['disable']) ? $field['disable'] : false;
		    
		// Add multiple select functionality as required
		$multiple = '';
		if($field['multiple'] == '1'){
		    $multiple = ' multiple="multiple" size="5" ';
		    $field['name'] .= '[]';
		} 
		
		// Begin HTML select field
		echo '<select id="' . $field['name'] . '" class="' . $field['class'] . ' cf7-select" name="' . $field['name'] . '" ' . $multiple . ' >';
		
		// Add null value as required
		if($field['allow_null'] == '1'){
		    echo '<option value="null"> - Select - </option>';
		}
		

		// Display all contact form 7 forms
		$forms = get_posts(array('post_type' => 'wpcf7_contact_form', 'orderby' => 'id', 'order' => 'ASC', 'posts_per_page' => -1, 'numberposts' => -1));       
		if($forms){  
		    foreach($forms as $k => $form){
		      	$key = $form->ID;
		      	$value = $form->post_title; 
		      	$selected = '';
		    
		      	// Mark form as selected as required
		      	if(is_array($field['value'])){
		        	// If the value is an array (multiple select), loop through values and check if it is selected
		        	if(in_array($key, $field['value'])){
		            	$selected = 'selected="selected"';
		          	}
		          	//Disable form selection as required
		          	if(in_array(($k+1), $field['disable'])){
		            	$selected = 'disabled="disabled"';
		          	}
		      	}else{
		          	// If not a multiple select, just check normaly
		          	if($key == $field['value']){
		            	$selected = 'selected="selected"';
		          	}
		          	if(in_array(($k+1), $field['disable'])){
		            	$selected = 'disabled="disabled"';
		          	}
		      	}
		      	echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
		    } 
		}       

		echo '</select>';
	}
	
		
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function input_admin_enqueue_scripts() {
		
		$dir = plugin_dir_url( __FILE__ );
		
		
		// register & include JS
		wp_register_script( 'acf-input-cf7', "{$dir}js/input.js" );
		wp_enqueue_script('acf-input-cf7');
		
		
		// register & include CSS
		wp_register_style( 'acf-input-cf7', "{$dir}css/input.css" ); 
		wp_enqueue_style('acf-input-cf7');
		
		
	}
	
	*/
	
	
	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function input_admin_head() {
	?>
		<script>
			jQuery(document).ready(function($) { 
				$(".cf7-select").select2({
					width: '100%'
				}); 
			});
		</script>
	<?php	
	}
	
	
	/*
   	*  input_form_data()
   	*
   	*  This function is called once on the 'input' page between the head and footer
   	*  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and 
   	*  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
   	*  seen on comments / user edit forms on the front end. This function will always be called, and includes
   	*  $args that related to the current screen such as $args['post_id']
   	*
   	*  @type	function
   	*  @date	6/03/2014
   	*  @since	5.0.0
   	*
   	*  @param	$args (array)
   	*  @return	n/a
   	*/
   	
   	/*
   	
   	function input_form_data( $args ) {
	   	
		
	
   	}
   	
   	*/
	
	
	/*
	*  input_admin_footer()
	*
	*  This action is called in the admin_footer action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_footer)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
		
	function input_admin_footer() {
	
		
		
	}
	
	*/
	
	
	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function field_group_admin_enqueue_scripts() {
		
	}
	
	*/

	
	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	
	function field_group_admin_head() {
	
	}
	
	*/


	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	
	/*
	
	function load_value( $value, $post_id, $field ) {
		
		return $value;
		
	}
	
	*/
	
	
	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	
	/*
	
	function update_value( $value, $post_id, $field ) {
		
		return $value;
		
	}
	
	*/
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
		
	
	function format_value( $value, $post_id, $field ) {
		if(!$value || $value == 'null'){
		    return false;
		}
		
		//If there are multiple forms, construct and return an array of form markup
		if(is_array($value)){
		    foreach($value as $k => $v){
		      	$form = get_post($v);
		      	$f = do_shortcode('[contact-form-7 id="'.$form->ID.'" title="'.$form->post_title.'"]');
		      	$value[$k] = array();
		      	$value[$k] = $f;
		    }
		//Else return single form markup
		}else{
		    $form = get_post($value);
		    $value = do_shortcode('[contact-form-7 id="'.$form->ID.'" title="'.$form->post_title.'"]');
		}

		return $value;
	}

	
	
	/*
	*  validate_value()
	*
	*  This filter is used to perform validation on the value prior to saving.
	*  All values are validated regardless of the field's required setting. This allows you to validate and return
	*  messages to the user if the value is not correct
	*
	*  @type	filter
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$valid (boolean) validation status based on the value and the field's required setting
	*  @param	$value (mixed) the $_POST value
	*  @param	$field (array) the field array holding all the field options
	*  @param	$input (string) the corresponding input name for $_POST value
	*  @return	$valid
	*/
	
	/*
	
	function validate_value( $valid, $value, $field, $input ){
		
		// Basic usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = false;
		}
		
		
		// Advanced usage
		if( $value < $field['custom_minimum_setting'] )
		{
			$valid = __('The value is too little!','acf-cf7'),
		}
		
		
		// return
		return $valid;
		
	}
	
	*/
	
	
	/*
	*  delete_value()
	*
	*  This action is fired after a value has been deleted from the db.
	*  Please note that saving a blank value is treated as an update, not a delete
	*
	*  @type	action
	*  @date	6/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (mixed) the $post_id from which the value was deleted
	*  @param	$key (string) the $meta_key which the value was deleted
	*  @return	n/a
	*/
	
	/*
	
	function delete_value( $post_id, $key ) {
		
		
		
	}
	
	*/
	
	
	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0	
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/
	
	/*
	
	function load_field( $field ) {
		
		return $field;
		
	}	
	
	*/
	
	
	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/
	
	/*
	
	function update_field( $field ) {
		
		return $field;
		
	}	
	
	*/
	
	
	/*
	*  delete_field()
	*
	*  This action is fired after a field is deleted from the database
	*
	*  @type	action
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	n/a
	*/
	
	/*
	
	function delete_field( $field ) {
		
		
		
	}	
	
	*/
	
	
}


// create field
new acf_field_cf7();

?>
