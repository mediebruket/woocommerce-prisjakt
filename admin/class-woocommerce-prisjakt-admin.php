<?php

class Woocommerce_Prisjakt_Admin {

  private $woocommerce_prisjakt;
  private $version;

  public function __construct( $woocommerce_prisjakt, $version ) {
    $this->woocommerce_prisjakt = $woocommerce_prisjakt;
    $this->version = $version;
  }

  public static function getAllProducts(){
    global $wpdb;
    $sql = sprintf("SELECT ID FROM %s WHERE (post_type='product' OR post_type='product_variant') AND post_status='publish'", $wpdb->posts);
    return $wpdb->get_col($sql);
  }


  public function create_csv_file( $post_id=null ) {

    // create CSV-file
    $args = array(
      'post_type' => 'product',
      'nopaging' => true
    );

    //$query = new WP_Query( $args );
    //$products = $query->get_posts();
    $products = Woocommerce_Prisjakt_Admin::getAllProducts();

    $upload_dir = wp_upload_dir();
    $file = $upload_dir["basedir"] . "/prices.csv";

    $fp = fopen($file, 'w');

    // header row
    $header = array("title", "sku", "category", "price", "url", "thumbnail", "fullsize", "stock_status");
    fputcsv($fp, $header);
    foreach ($products as $product) {
      // Produktnamn;Art.nr.;Kategori;Pris inkl.moms;Produkt-URL;Tillverkare;Tillverkar-SKU;Frakt;Bild-URL;Lagerstatus
      $p = new WC_Product_Variable($product);
      $variations = $p->get_children();

      // simple product
      if ( !$variations or is_array($variations) && empty($variations) ){
        if ( $csv_row = $this->build_row($p) ){
          fputcsv($fp, $csv_row);
        }
      }

      // variable product
      if ( $variations and is_array($variations) && !empty($variations) ){
        foreach( $variations as $key => $variation_id ){
          $p = new  WC_Product_Variation($variation_id);
          if ( $csv_row = $this->build_row($p) ){
            fputcsv($fp, $csv_row);
          }
        }
      }

    }

    fclose($fp);
  }


  public function build_row( $WC_Product ){
    $row = null;

    if ( is_object($WC_Product) ){
      $title = $WC_Product->get_name();
      $sku = $WC_Product->get_sku();

      $post_id = $WC_Product->get_id();
      if ( $WC_Product->get_type() == 'variation' ){
        $post_id = $WC_Product->get_parent_id();
      }
      $terms =  wp_get_post_terms( $post_id, 'product_cat', $args = array('orderby' => 'id', 'order' => 'ASC', 'fields' => 'all') );

      $category = "";
      if ( $terms && ! is_wp_error( $terms ) ) {
        $term_names = array();
        foreach ($terms as $key => $term) {
          $term_names[] = $term->name;
        }

        $category = implode(' / ', $term_names);
      }

      $price = null;
      if ( method_exists($WC_Product, 'get_price_including_tax') ){
        $price = wc_get_price_including_tax($WC_Product);
      }

      $url = $WC_Product->get_permalink();
      $thumbnail = wp_get_attachment_image_src($WC_Product->get_image_id(), 'shop_thumbnail')[0];
      $fullsize = wp_get_attachment_image_src($WC_Product->get_image_id(), 'full')[0];
      $stock_status = $WC_Product->get_stock_status();

      $row = array($title, $sku, $category, $price, $url, $thumbnail, $fullsize, $stock_status);
    }

    return $row;
  }


}
