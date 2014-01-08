<?php
/*
*	Plugin Name: Heroic Testimonials
*	Plugin URI:  http://wordpress.org/plugins/hero-themes-testimonials/
*	Description: Adds Testimonials
*	Author: Hero Themes
*	Version: 1.0
*	Author URI: http://www.herothemes.com/
*	Text Domain: ht-testimonials
*/


if( !class_exists( 'HT_Testimonials' ) ){
	class HT_Testimonials {
		//Constructor
		function __construct(){
			add_action( 'init', array( $this,  'register_ht_testimonials_cpt' ) );
			add_action( 'init', array( $this,  'register_ht_testimonials_category_taxonomy' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_ht_testimonials_scripts_and_styles' ) );

			add_action( 'add_meta_boxes', array( $this, 'add_ht_testimonials_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'ht_testimonials_meta_save' ) );

			include_once('ht-testimonials-widget.php');

			$this->testimonial_client_name_key = 'testimonial_client_name';
			$this->testimonial_client_byline_key = 'testimonial_client_byline';
			$this->testimonial_client_url_key = 'testimonial_client_url';
			$this->testimonial_client_image_key = 'testimonial_client_image';
		}

		/**
		* Registers the ht_testimonials_post category taxonomy
		*/
		function register_ht_testimonials_category_taxonomy()  {

			$singular_item = __('Testimonial', 'ht-testimonials-widget');
			$rewrite = get_post_format_string($singular_item);

			$labels = array(
				'name'                       => _x( 'Testimonials Category', 'Taxonomy General Name', 'ht-testimonials-widget' ),
				'singular_name'              => _x( 'Testimonials Category', 'Taxonomy Singular Name', 'ht-testimonials-widget' ),
				'menu_name'                  => __( 'Testimonials Categories', 'ht-testimonials-widget' ),
				'all_items'                  => __( 'All Testimonials Categories', 'ht-testimonials-widget' ),
				'parent_item'                => __( 'Parent Testimonials Category', 'ht-testimonials-widget' ),
				'parent_item_colon'          => __( 'Parent Testimonials Category:', 'ht-testimonials-widget' ),
				'new_item_name'              => __( 'New Testimonials Category', 'ht-testimonials-widget' ),
				'add_new_item'               => __( 'Add New Testimonials Category', 'ht-testimonials-widget' ),
				'edit_item'                  => __( 'Edit Testimonials Category', 'ht-testimonials-widget' ),
				'update_item'                => __( 'Update Testimonials Category', 'ht-testimonials-widget' ),
				'separate_items_with_commas' => __( 'Separate Testimonials Categories with commas', 'ht-testimonials-widget' ),
				'search_items'               => __( 'Search Testimonials Categories', 'ht-testimonials-widget' ),
				'add_or_remove_items'        => __( 'Add or remove categories', 'ht-testimonials-widget' ),
				'choose_from_most_used'      => __( 'Choose from the most used categories', 'ht-testimonials-widget' ),
			);
			$args = array(
				'labels'                     => $labels,
				'hierarchical'               => true,
				'rewrite'            		 => array( 'slug' => $rewrite ),
				'public'                     => true,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => true,
				'show_tagcloud'              => true,
			);
			register_taxonomy( 'ht_testimonials_category', 'ht_testimonials', $args );

		}

		/**
		* Registers the ht_testimonials_post custom post type
		*/
		function register_ht_testimonials_cpt() {
			$singular_item = __('Heroic Testimonial', 'ht-gallery-manager');
			$plural_item = __('Testimonials', 'ht-gallery-manager');
			$rewrite = get_post_format_string($plural_item);

		  	$labels = array(
			    'name'               =>  $singular_item,
			    'singular_name'      => 'Testimonial',
			    'add_new'            => __('Add New', 'ht-gallery-manager') . ' ' .  $singular_item,
			    'add_new_item'       => __('Add New', 'ht-gallery-manager') . ' ' .  $singular_item,
			    'edit_item'          => __('Edit', 'ht-gallery-manager') . ' ' .  $singular_item,
			    'new_item'           => __('New', 'ht-gallery-manager') . ' ' .  $singular_item,
			    'all_items'          => __('All', 'ht-gallery-manager') . ' ' .  $plural_item,
			    'view_item'          => __('View', 'ht-gallery-manager') . ' ' .  $singular_item,
			    'search_items'       => __('Search', 'ht-gallery-manager') . ' ' .  $plural_item,
			    'not_found'          => sprintf( __( 'No %s found', 'ht-gallery-manager' ), $plural_item ),
			    'not_found_in_trash' => sprintf( __( 'No %s found in trash', 'ht-gallery-manager' ), $plural_item ),
			    'parent_item_colon'  => '',
			    'menu_name'          => $plural_item,
		  	);

			$args = array(
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => $rewrite ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor' )
			);

		  register_post_type( 'ht_testimonials', $args );
		}



		/**
		* Add the meta boxes
		*/
		function add_ht_testimonials_meta_boxes(){
			global $_wp_post_type_features;
			add_meta_box( 'ht_testimonials_meta', __( 'Testimonials', 'ht-testimonials' ), array( $this, 'add_ht_testimonials_meta_callback'  ), 'ht_testimonials' );
				
			if (isset($_wp_post_type_features['ht_testimonials']['editor']) && $_wp_post_type_features['ht_testimonials']['editor']) {
				unset($_wp_post_type_features['ht_testimonials']['editor']);
					add_meta_box(
						'description_section',
						__('Testimonial Text', 'ht-testimonials'),
						array( $this, 'inner_editor_box' ),
						'ht_testimonials', 'normal', 'low'
					);
				}

		}




		/**
		* Meta box callback
		*/
		function add_ht_testimonials_meta_callback($post){
				wp_nonce_field( basename( __FILE__ ), 'ht_testimonials_nonce' );
			    $testimonial_client_name = get_post_meta( $post->ID, $this->testimonial_client_name_key, true );
			    $testimonial_client_byline = get_post_meta( $post->ID, $this->testimonial_client_byline_key, true );
			    $testimonial_client_url = get_post_meta( $post->ID, $this->testimonial_client_url_key, true );
			    $testimonial_client_image = get_post_meta( $post->ID, $this->testimonial_client_image_key, true );

			    ?>
			 
			    <p>
			        <label for="<?php echo $this->testimonial_client_name_key; ?>" class="row-title"><?php _e( 'Client Name', 'ht-testimonials' )?></label>
			        <input type="text" name="<?php echo $this->testimonial_client_name_key; ?>" id="<?php echo $this->testimonial_client_name_key; ?>" value="<?php echo $testimonial_client_name; ?>" />
			    </p>
			    <p>
			        <label for="<?php echo $this->testimonial_client_byline_key; ?>" class="row-title"><?php _e( 'Client Byline', 'ht-testimonials' )?></label>
			        <input type="text" name="<?php echo $this->testimonial_client_byline_key; ?>" id="<?php echo $this->testimonial_client_byline_key; ?>" value="<?php echo $testimonial_client_byline; ?>" />
			    </p>
			    <p>
			        <label for="<?php echo $this->testimonial_client_url_key; ?>" class="row-title"><?php _e( 'Client URL', 'ht-testimonials' )?></label>
			        <input type="text" name="<?php echo $this->testimonial_client_url_key; ?>" id="<?php echo $this->testimonial_client_url_key; ?>" value="<?php echo $testimonial_client_url; ?>" />
			    </p>
			   
			    <p>
				    <label for="<?php echo $this->testimonial_client_image_key; ?>" class="row-title"><?php _e( 'Client Image', 'ht-testimonials' )?></label>
				    <input type="text" name="<?php echo $this->testimonial_client_image_key; ?>" id="<?php echo $this->testimonial_client_image_key; ?>" value="<?php echo $testimonial_client_image; ?>" />
				    <input type="button" id="<?php echo $this->testimonial_client_image_key; ?>-button" class="button" value="<?php _e( 'Choose or Upload an Image', 'ht-testimonials' )?>" />
				</p>
			 
			    <?php			
		}

		/**
		* The inner custom post box for the editor - requires content as name.
		*
		* @param $post The Post object.
		*/
		function inner_editor_box( $post ) {
			wp_editor( $post->post_content, 'content' );
		}


		/**
		 * Saves the custom meta input
		 */
		function ht_testimonials_meta_save( $post_id ) {
		    // Checks save status
		    $is_autosave = wp_is_post_autosave( $post_id );
		    $is_revision = wp_is_post_revision( $post_id );
		    $is_valid_nonce = ( isset( $_POST[ 'ht_testimonials_nonce' ] ) && wp_verify_nonce( $_POST[ 'ht_testimonials_nonce' ], basename( __FILE__ ) ) ) ? true : false;
		 
		    // Exits depending on save status
		    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
		        return;
		    }
		 
		    // save client name
		    if( isset( $_POST[ $this->testimonial_client_name_key ] ) ) {
		        update_post_meta( $post_id, $this->testimonial_client_name_key , sanitize_text_field( $_POST[ $this->testimonial_client_name_key  ] ) );
		    }
		    // save client byline
		    if( isset( $_POST[ $this->testimonial_client_byline_key ] ) ) {
		        update_post_meta( $post_id, $this->testimonial_client_byline_key , sanitize_text_field( $_POST[ $this->testimonial_client_byline_key  ] ) );
		    }
		    // save client url
		    if( isset( $_POST[ $this->testimonial_client_url_key ] ) ) {
		        update_post_meta( $post_id, $this->testimonial_client_url_key , sanitize_text_field( $_POST[ $this->testimonial_client_url_key  ] ) );
		    }
		    // save client image
		    if( isset( $_POST[ $this->testimonial_client_image_key ] ) ) {
		        update_post_meta( $post_id, $this->testimonial_client_image_key , sanitize_text_field( $_POST[ $this->testimonial_client_image_key  ] ) );
		    }
		 
		}

		/**
		 * Loads the image management javascript
		 */
		function enqueue_ht_testimonials_scripts_and_styles() {
		    $screen = get_current_screen();
		    if( $screen->post_type == 'ht_testimonials' && $screen->base == 'post' ) {
		        wp_enqueue_media();
		
		        // Register and enqueue the scripts
		        wp_register_script( 'ht-testimonials-backend-script', plugins_url( 'js/ht-testimonials-backend-script.js', __FILE__ ), array( 'jquery' ) );
		        wp_localize_script( 'ht-testimonials-backend-script', 'meta_image',
		            array(
		                'title' => __( 'Choose or Upload an Image', 'ht-testimonials' ),
		                'button' => __( 'Use this image', 'ht-testimonials' ),
		            )
		        );
		        wp_enqueue_script( 'ht-testimonials-backend-script' );
		    }
		}

	} //end class HT_Testimonials
}//end class exists test

//run the plugin
if( class_exists( 'HT_Testimonials' ) ){
	$ht_testimonials_init = new HT_Testimonials();
}


