<?php
defined('ABSPATH') || die();

/**
 * Add Matomo's e-commerce tracking script (JS) to product pages
 */
function tracktastic_add_matomo_ecommerce_tracking_to_product_pages()
{
    if (is_product()) {
        global $product;
        global $post;
        
        $_product = $product;
        
        if(!is_object($product)) {    
            $_product = new WC_Product($post->ID);
        }
        
        if(!is_object($_product)) {
          return;
        }

        $sku = $_product->get_sku();
        if(empty($sku)) {
            $sku= $post->ID;
        }
        $name = $_product->get_name();
        $categories = wp_get_post_terms($_product->get_id(), 'product_cat');
        $category_names = array_map(function ($term) {
            return $term->name;
        }, $categories);
        $category_list = implode(', ', $category_names);
        $price = $_product->get_price();

        wp_enqueue_script('tracktastic-product-tracking', plugins_url('/js/matomo-product-tracking.js', __FILE__), array('jquery'), null, true);

        wp_localize_script('tracktastic-product-tracking', 'tracktasticProductData', array(
            'sku' => esc_js($sku),
            'name' => esc_js($name),
            'categoryList' => esc_js($category_list),
            'price' => esc_js($price),
        ));
    }
}
add_action('wp_head', 'tracktastic_add_matomo_ecommerce_tracking_to_product_pages', 999);

/**
 * Add Matomo's e-commerce tracking script (JS) to category pages
 */
function tracktastic_add_matomo_ecommerce_tracking_to_category_pages()
{
    if (is_product_category()) {
        $category = get_queried_object();
        $category_name = $category->name;

        wp_enqueue_script('tracktastic-category-tracking', plugins_url('/js/matomo-category-tracking.js', __FILE__), array('jquery'), null, true);

        wp_localize_script('tracktastic-category-tracking', 'tracktasticCategoryData', array(
            'categoryName' => esc_html($category_name),
        ));
    }
}
add_action('wp_head', 'tracktastic_add_matomo_ecommerce_tracking_to_category_pages', 999);

/**
 * Add Matomo's e-commerce tracking script (JS) to cart page
 */
function tracktastic_add_matomo_tracking_to_cart_page()
{
    if (is_cart()) {
        wp_enqueue_script('tracktastic-matomo-cart-tracking', plugins_url('/js/matomo-cart-tracking.js', __FILE__), array('jquery'), null, true);

        $items = array();
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            if (!is_a($_product, 'WC_Product')) continue;

            $_category = wp_get_post_terms($_product->get_id(), 'product_cat');
            $_category_name = !empty($_category) ? esc_html($_category[0]->name) : '';

            $sku = $_product->get_sku();
            if (empty($sku)) {
                $sku = $_product->get_id();
            }

            $items[] = array(
                'sku' => esc_attr($sku),
                'name' => esc_html($_product->get_name()),
                'category' => $_category_name,
                'price' => esc_attr($_product->get_price()),
                'quantity' => intval($cart_item['quantity']),
            );
        }

        wp_localize_script('tracktastic-matomo-cart-tracking', 'tracktasticCartData', array(
            'items' => $items,
            'cartTotal' => WC()->cart->total,
        ));
    }
}
add_action('wp_head', 'tracktastic_add_matomo_tracking_to_cart_page', 999);

/**
 * Add Matomo's e-commerce tracking script (JS) to order received page
 */
function tracktastic_add_matomo_tracking_to_order_received_page($order_id)
{
    $order = wc_get_order($order_id);

    if (!$order) {
        return;
    }

    wp_enqueue_script('tracktastic-order-tracking', plugins_url('/js/matomo-order-tracking.js', __FILE__), array('jquery'), null, true);

    $items_data = array();
    foreach ($order->get_items() as $item_id => $item) {
        $_product = $item->get_product();
        $_categories = wp_get_post_terms($_product->get_id(), 'product_cat');
        $category_name = !empty($_categories) ? esc_html($_categories[0]->name) : '';

        $sku = $_product->get_sku();
        if (empty($sku)) {
            $sku = $_product->get_id();
        }

        $items_data[] = array(
            'sku' => esc_html($sku),
            'name' => esc_html($item->get_name()),
            'category_name' => $category_name,
            'price' => esc_html($item->get_subtotal()),
            'quantity' => esc_html($item->get_quantity()),
        );
    }

    $order_data = array(
        'order_number' => esc_html($order->get_order_number()),
        'total' => esc_html($order->get_total()),
        'subtotal' => esc_html($order->get_subtotal()),
        'total_tax' => esc_html($order->get_total_tax()),
        'shipping_total' => esc_html($order->get_shipping_total()),
        'items' => $items_data,
    );

    wp_localize_script('tracktastic-order-tracking', 'tracktasticOrderData', $order_data);
}
add_action('woocommerce_thankyou', 'tracktastic_add_matomo_tracking_to_order_received_page', 999);