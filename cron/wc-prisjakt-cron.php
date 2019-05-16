<?php
header('Content-type: text/html; charset=UTF-8');
ini_set('memory_limit', '-1'); 
ini_set('max_execution_time', 1000);


if ( $_SERVER['SCRIPT_FILENAME'] == 'wc-prisjakt-cron.php'){
  include_once(  '../../../../wp-load.php' );

  if ( class_exists('Woocommerce_Prisjakt_Admin') ){
    $Admin = new Woocommerce_Prisjakt_Admin( WCP_SLUG, WCP_VERSION );
    $Admin->create_csv_file();
  }
  else{
    echo 'class Woocommerce_Prisjakt_Admin not available ';
  }

} 