<?php
/**
 * Plugin Name: Blue Robotics Watertight Enclosure (WTE) Configurator
 * Plugin URI: http://bluerobotics.com
 * Description: Adds functionality for watertight enclosure configuration.
 * Author: Rustom Jehangir
 * Author URI: http://rstm.io
 * Version: 1.0.0
 *
 * Copyright: (c) 2018 Rustom Jehangir
 *
 * @author    Rustom Jehangir
 * @copyright Copyright (c) 2018, Rustom Jehangir
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// First Register the Tab by hooking into the 'woocommerce_product_data_tabs' filter
add_filter( 'woocommerce_product_data_tabs', 'add_my_custom_product_data_tab', 10, 1 );
function add_my_custom_product_data_tab( $product_data_tabs ) {
	global $post;
	$terms = wp_get_post_terms( $post->ID, 'product_cat' );
	foreach ( $terms as $term ) $categories[] = $term->slug;

	$wte_category_slugs = array('2-series','3-series','4-series','6-series','8-series','penetrators');

	foreach ( $wte_category_slugs as $slug ) {
		if ( in_array( $slug, $categories ) ) {
		    $product_data_tabs['wte-tab'] = array(
		        'label' => __( 'BR WTE Configuration', 'woocommerce' ),
		        'target' => 'wte_data',
		        'class'     => array( '' ),
		    );
		    return $product_data_tabs;
		}
	}
	return $product_data_tabs;
}

// functions you can call to output text boxes, select boxes, etc.
add_action('woocommerce_product_data_panels', 'wte_product_data_fields');

function wte_product_data_fields() {
    global $post;

    // Note the 'id' attribute needs to match the 'target' parameter set above
    ?> <div id = 'wte_data'
    class = 'panel woocommerce_options_panel' > <?php
        ?> <div class = 'options_group' > <?php
  // Material
  woocommerce_wp_text_input(
    array(
      'id' => '_wte_material',
      'label' => __( 'Material Type', 'woocommerce' ),
      'placeholder' => 'Aluminum 6061-T6',
      'desc_tip' => 'true',
      'description' => __( 'Enter the material name, such as Aluminum 6061-T6.', 'woocommerce' ),
    )
  );

  // Surface finish
  woocommerce_wp_text_input(
    array(
      'id' => '_wte_surface_finish',
      'label' => __( 'Surface Finish', 'woocommerce' ),
      'placeholder' => 'Hard Anodized Black',
      'desc_tip' => 'true',
      'description' => __( 'Enter the surface finish type, if applicable.', 'woocommerce' ),
    )
  );

  // Weight in Air
  woocommerce_wp_text_input(
    array(
      'id' => '_wte_weight_in_air_grams',
      'label' => __( 'Weight in Air (g)', 'woocommerce' ),
      'placeholder' => '',
      'desc_tip' => 'true',
      'description' => __( 'Enter the enclosure part\'s weight in grams here.', 'woocommerce' ),
      'type' => 'number',
    )
  );

  // Installed Displacement
  woocommerce_wp_text_input(
    array(
      'id' => '_wte_installed_displacement_mm3',
      'label' => __( 'Installed Displacement (mm<sup>3</sup>)', 'woocommerce' ),
      'placeholder' => '',
      'desc_tip' => 'true',
      'description' => __( 'Enter the component\'s installed displacement volume here', 'woocommerce' ),
      'type' => 'number',
    )
  );

  // Depth Rating
  woocommerce_wp_text_input(
    array(
      'id' => '_wte_depth_rating_meters',
      'label' => __( 'Depth Rating (m)', 'woocommerce' ),
      'placeholder' => '',
      'desc_tip' => 'true',
      'description' => __( 'Enter the depth rating in meters.', 'woocommerce' ),
      'type' => 'number',
    )
  );

  // Depth Rating Type
  woocommerce_wp_select(
    array(
      'id' => '_wte_depth_rating_type',
      'label' => __( 'Depth Rating Type', 'woocommerce' ),
      'options' => array(
         'none' => __( 'No depth rating', 'woocommerce' ),
         'theoretical' => __( 'Theoretical', 'woocommerce' ),
         'empirical' => __( 'Empirical', 'woocommerce' ),
      )
    )
  );
        ?> </div>

    </div><?php
}

/** Hook callback function to save custom fields information */
function wte_save_proddata_custom_fields($post_id) {
    // Save fields
    $wte_material = $_POST['_wte_material'];
    if (!empty($wte_material)) {
        update_post_meta($post_id, '_wte_material', esc_attr($wte_material));
    }

    $wte_surface_finish = $_POST['_wte_surface_finish'];
    if (!empty($wte_surface_finish)) {
        update_post_meta($post_id, '_wte_surface_finish', esc_attr($wte_surface_finish));
    }

    $wte_weight_in_air = $_POST['_wte_weight_in_air_grams'];
    if (!empty($wte_weight_in_air)) {
        update_post_meta($post_id, '_wte_weight_in_air_grams', esc_attr($wte_weight_in_air));
    }

    $_wte_installed_displacement = $_POST['_wte_installed_displacement_mm3'];
    if (!empty($_wte_installed_displacement)) {
        update_post_meta($post_id, '_wte_installed_displacement_mm3', esc_attr($_wte_installed_displacement));
    }

    $wte_depth_rating = $_POST['_wte_depth_rating_meters'];
    if (!empty($wte_depth_rating)) {
        update_post_meta($post_id, '_wte_depth_rating_meters', esc_html($wte_depth_rating));
    }

    $wte_depth_rating_type = $_POST['_wte_depth_rating_type'];
    if (!empty($wte_depth_rating_type)) {
        update_post_meta($post_id, '_wte_depth_rating_type', esc_attr($wte_depth_rating_type));
    }
}
add_action( 'woocommerce_process_product_meta_simple', 'wte_save_proddata_custom_fields'  );

// Add shortcodes
function wte_material_func( $atts ) {
	global $post;

	return get_post_meta($post->ID,'_wte_material')[0];
}
add_shortcode( 'wte_material', 'wte_material_func' );

function wte_surface_finish_func( $atts ) {
	global $post;

	return get_post_meta($post->ID,'_wte_surface_finish')[0];
}
add_shortcode( 'wte_surface_finish', 'wte_surface_finish_func' );

function wte_weight_in_air_func( $atts, $content = null, $tag = '' ) {
	global $post;

	// normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);

	$atts = shortcode_atts( array(
		'units' => 'g'
	), $atts, $tag );

	$value = get_post_meta($post->ID,'_wte_weight_in_air_grams')[0];

	if ( $atts['units'] == 'g' ) {
		return sprintf('%d g', $value);	
	}
	if ( $atts['units'] == 'kg' ) {
		return sprintf('%5.2f kg', $value*0.001);
	}
	if ( $atts['units'] == 'lb' ) {
		return sprintf('%5.2f lb', $value*0.0022);	
	}
}
add_shortcode( 'wte_weight_in_air', 'wte_weight_in_air_func' );

function wte_depth_rating_func( $atts, $content = null, $tag = '' ) {
	global $post;

	// normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);

	$atts = shortcode_atts( array(
		'units' => 'm',
		'notes' => 'true'
	), $atts, $tag );

	$value = get_post_meta($post->ID,'_wte_depth_rating_meters')[0];
	$type = get_post_meta($post->ID,'_wte_depth_rating_type')[0];

	if ( $type == 'none' ) {
		$notes = '';
	}
	if ( $type == 'theorectical' ) {
		$notes = ' ';
	}
	if ( $type == 'empirical' ) {
		$notes = ' ';
	}

	if ( $atts['notes'] == 'false' ) {
		$notes = '';
	}

	if ( $atts['units'] == 'm' ) {
		return sprintf('%d m %s', $value, $notes);	
	}
	if ( $atts['units'] == 'ft' ) {
		return sprintf('%d ft %s', $value*3.28084, $notes);
	}
}
add_shortcode( 'wte_depth_rating', 'wte_depth_rating_func' );

?>