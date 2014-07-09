<?php

class acf_field_cf7 extends acf_field {
	
	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options
		
		
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'cf7';
		$this->label = __('Contact Form 7');
		$this->category = __("Relational",'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			'allow_null'	=> 0,
			'multiple'		=> 0,
			'disable'		=> ''
		);
		
		
		// do not delete!
    	parent::__construct();
    	
    	
    	// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);

	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like below) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
	{
		// vars
		$defaults = array(
		  	'multiple'    =>  0,
		  	'allow_null'  =>  0,
		  	'default_value' => '',
		  	'choices'   =>  '',
		  	'disable'   => ''
		);
		
		$field = array_merge($defaults, $field);
		$key = $field['name'];
		?>
	  	<tr class="field_option field_option_<?php echo $this->name; ?>">
	      	<td class="label">
	        	<label><?php _e("Allow Null?",'acf'); ?></label>
	      	</td>
	      	<td>
			    <?php 
			    do_action('acf/create_field', array(
			      	'type'  =>  'radio',
			      	'name'  =>  'fields['.$key.'][allow_null]',
			      	'value' =>  $field['allow_null'],
			      	'choices' =>  array(
			        	1 =>  __("Yes",'acf'),
			        	0 =>  __("No",'acf'),
			      	),
			      	'layout'  =>  'horizontal',
			    ));
			    ?>
	      	</td>
	  	</tr>
	  	<tr class="field_option field_option_<?php echo $this->name; ?>">
	      	<td class="label">
	        	<label><?php _e("Select Multiple?",'acf'); ?></label>
	      	</td>
	      	<td>
			    <?php 
			    do_action('acf/create_field', array(
			      	'type'  =>  'radio',
			      	'name'  =>  'fields['.$key.'][multiple]',
			      	'value' =>  $field['multiple'],
			      	'choices' =>  array(
			        	1 =>  __("Yes",'acf'),
			        	0 =>  __("No",'acf'),
			      	),
			      	'layout'  =>  'horizontal',
			    ));
			    ?>
	      	</td>
	  	</tr>
	  	<tr class="field_option field_option_<?php echo $this->name; ?>">
	      	<td class="label">
	        	<label><?php _e("Disable Forms?",'acf'); ?></label>
	        	<p class="description"><?php _e("User will not be able to select these forms",'acf'); ?></p>
	      	</td>
	      	<td>
			    <?php 
			    //Get form names
			    $forms = get_posts(array('post_type' => 'wpcf7_contact_form', 'orderby' => 'id', 'order' => 'ASC', 'posts_per_page' => -1, 'numberposts' => -1));  
			    $choices = array();
			    $choices[0] = '---';
			    $k = 1;
			    foreach($forms as $f){
			        $choices[$k] = $f->post_title;
			        $k++;
			    } 
			    do_action('acf/create_field', array(
			      	'type'  =>  'select',
			      	'name'  =>  'fields['.$key.'][disable]',
			      	'value' =>  $field['disable'],
			      	'multiple'    =>  '1',
			        'allow_null'  =>  '0',
			        'choices' =>  $choices,
			      	'layout'  =>  'horizontal',
			    ));
			    ?>
	      	</td>
	  	</tr>
		<?php
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		$field['multiple'] = isset($field['multiple']) ? $field['multiple'] : false;
		$field['disable'] = isset($field['disable']) ? $field['disable'] : false;
		    
		// Add multiple select functionality as required
		$multiple = '';
		if($field['multiple'] == '1'){
		    $multiple = ' multiple="multiple" size="5" ';
		    $field['name'] .= '[]';
		} 
		
		// Begin HTML select field
		echo '<select id="' . $field['name'] . '" class="' . $field['class'] . '" name="' . $field['name'] . '" ' . $multiple . ' >';
		
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
	*  Use this action to add CSS + JavaScript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

/*
	function input_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
		
		
		// register ACF scripts
		wp_register_script( 'acf-input-cf7', $this->settings['dir'] . 'js/input.js', array('acf-input'), $this->settings['version'] );
		wp_register_style( 'acf-input-cf7', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version'] ); 
		
		
		// scripts
		wp_enqueue_script(array(
			'acf-input-cf7',	
		));

		// styles
		wp_enqueue_style(array(
			'acf-input-cf7',	
		));
		
		
	}
*/
	
	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your create_field() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_head()
	{
		// Note: This function can be removed if not used
	}
	
	
	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your create_field_options() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
	}

	
	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your create_field_options() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_head()
	{
		// Note: This function can be removed if not used
	}


	/*
	*  load_value()
	*
		*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value found in the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the value to be saved in the database
	*/
	
	function load_value( $value, $post_id, $field )
	{
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field )
	{
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value( $value, $post_id, $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/
		
		// perhaps use $field['preview_size'] to alter the $value?
		
		
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed back to the API functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{
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
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/
	
	function load_field( $field )
	{
		// Note: This function can be removed if not used
		return $field;
	}
	
	
	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = acf)
	*
	*  @return	$field - the modified field
	*/

	function update_field( $field, $post_id )
	{
		// Note: This function can be removed if not used
		return $field;
	}

	
}


// create field
new acf_field_cf7();

?>
