<?

class HT_Testimonials_Widget extends WP_Widget {

/*--------------------------------------------------*/
/* Constructor
/*--------------------------------------------------*/

/**
* Specifies the classname and description, instantiates the widget,
* loads localization files, and includes necessary stylesheets and JavaScript.
*/
public function __construct() {

// REMEMBER: update classname and description
parent::__construct(
'ht-testimonials-widget',
__( 'Custom Testimonials Widget', 'framework' ),
array(
'classname'	=>	'ht_testimonials_widget',
'description'	=>	__( 'A widget for displaying testimonials.', 'framework' )
)
);

      $this->testimonial_client_name_key = 'testimonial_client_name';
      $this->testimonial_client_byline_key = 'testimonial_client_byline';
      $this->testimonial_client_url_key = 'testimonial_client_url';
      $this->testimonial_client_image_key = 'testimonial_client_image';

} // end constructor

/*--------------------------------------------------*/
/* Widget API Functions
/*--------------------------------------------------*/

/**
* Outputs the content of the widget.
*
* @param array args The array of form elements
* @param array instance The current instance of the widget
*/
public function widget( $args, $instance ) {

extract( $args, EXTR_SKIP );

$title = $instance['title'];
$number_to_display = $instance['number_to_display'];
$number_to_display_int = intval($number_to_display);
$category = $instance['category'];



echo $before_widget;

if ( $title )
echo $before_title . $title . $after_title; ?>

<?php

//get post in categories
$args = array(
    'post_type'         =>    'ht_testimonials',
    'order'             =>    'DESC',
    'posts_per_page'   =>     $number_to_display_int
  );

$testimonials_posts = get_posts($args);

foreach ($testimonials_posts as $testimonial) {
  $testimonial_title = $testimonial->post_title;
  $testimonial_text = $testimonial->post_content;
  $testimonial_client_name = get_post_meta($testimonial->ID, $this->testimonial_client_name_key, true);
  $testimonial_byline = get_post_meta($testimonial->ID, $this->testimonial_client_byline_key, true);
  $testimonial_url = get_post_meta($testimonial->ID, $this->testimonial_client_url_key, true);
  $testimonial_image = get_post_meta($testimonial->ID, $this->testimonial_client_image_key, true);

?>
  <div class="ht-testimonial clearfix" itemscope itemtype="http://schema.org/Review">
  <div class="ht-testimonial-body clearfix" itemprop="reviewBody"><i class="fa fa-quote-left"></i><p><?php echo $testimonial_text; ?></p><i class="fa fa-quote-right"></i></div>
  <div class="ht-testimonial-author" itemprop="author" itemscope itemtype="http://schema.org/Person">
  <img src="<?php echo $testimonial_image; ?>" alt="" />
  <span itemprop="name"><?php echo $testimonial_client_name; ?></span>
  </div>
  </div>
<?php
}
	
?>

<?php 
echo $after_widget;

} // end widget

/**
* Processes the widget's options to be saved.
*
* @param array new_instance The previous instance of values before the update.
* @param array old_instance The new instance of values to be generated via the update.
*/
public function update( $new_instance, $old_instance ) {

$instance = $old_instance;

// REMEMBER: Here is where you update your widget's old values with the new, incoming values
$instance['title'] = strip_tags( $new_instance['title'] );
$instance['number_to_display'] = strip_tags( $new_instance['number_to_display'] );
$instance['category'] = strip_tags( $new_instance['category'] );

return $instance;

} // end widget

/**
* Generates the administration form for the widget.
*
* @param array instance The array of keys and values for the widget.
*/
public function form( $instance ) {

// REMEMBER: Define default values for your variables
$defaults = array(
	'title' => 'Testimonials',
	'number_to_display' => '3',
	'category' => '',
);
$instance = wp_parse_args((array) $instance, $defaults);

// Store the values of the widget in their own variable
$title = strip_tags($instance['title']);
?>
<p>
<label for="<?php echo $this->get_field_id("title"); ?>">
  <?php _e( 'Title', 'framework' ); ?>
  :
  <input class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
</label>
</p>
<p>
<label for="<?php echo $this->get_field_id("number_to_display"); ?>">
  <?php _e( 'Number to display', 'framework' ); ?>
  :</label>
  <select class="" id="<?php echo $this->get_field_id("number_to_display"); ?>" name="<?php echo $this->get_field_name("number_to_display"); ?>" >
  	<?php
  		$current_val = esc_attr($instance["number_to_display"]);
  		$current_val = empty($current_val) ? $defaults['number_to_display'] : $current_val;
	  	for ($i=1; $i < 11; $i++) { 
	  		$selected =  (strval($i) == $current_val) ? 'selected="selected"' : '';
	  		echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
	  	}
  	?>
  </select>

</p>
<p>
<label for="<?php echo $this->get_field_id("category"); ?>">
  <?php _e( 'Category', 'framework' ); ?>
  :
  <select class="widefat" id="<?php echo $this->get_field_id("category"); ?>" name="<?php echo $this->get_field_name("category"); ?>" >
  	<?php
  		$categories = get_terms( 'ht_testimonials_category', array(
			'orderby'    => 'count',
			'hide_empty' => 0
		) );
  		$current_val = esc_attr($instance["category"]);
  		$current_val = empty($current_val) ? $defaults['category'] : $current_val;
	  	foreach($categories as $category){
	  		$selected =  ($category->term_id == $current_val) ? 'selected="selected"' : '';
	  		echo '<option value="' . $category->term_id . '" ' . $selected . '>' .  $category->name . '</option>';
	  	}
  	?>
  </select>
</label>
</p>
<?php } // end form


} // end class

// REMEMBER: Remember to change 'Widget_Name' to match the class name definition
add_action( 'widgets_init', create_function( '', 'register_widget("HT_Testimonials_Widget");' ) );


