<?php

class WC_PrisjaktOptions{
  

  function __construct(){ 
  }


  function init(){
    $this->__construct();
  }


  function get($Attribute){
    return $this->$Attribute;
  }


  function set($Attribute, $value){
    $this->$Attribute = $value;
  }


  function updateOptions( $type ){
    $method = 'load'.$type."Options";

    foreach ( $this->$method() as $key => $option){
      if ( isset($_POST[ $option['name'] ]) ){
        $post_value = $_POST[ $option['name'] ];

        if ( is_string($post_value) ){
          $post_value = trim( $post_value );
        }

        update_option( $option['name'], $post_value );
      }
      else{
        update_option( $option['name'], null );
      }
    }

    $this->init();
  }


  function getOptions( $type ){
    $method = 'load'.$type.'Options';
    ob_start();
    foreach ( $this->$method() as $key => $option){
      WC_PrisjaktHtmlBuilder::buildOption( $option );
    }

    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }


  function loadGeneralOptions(){
    return array(
       array(
        'name'  => 'woocommerce-prisjakt_disable_auto_save',
        'label' => __('Save csv file automatically', 'wc-prisjakt' ),
        'type'  => 'checkbox',
        'value' => get_option('woocommerce-prisjakt_disable_auto_save'),
        'option'   => 'on',
      )
    );
  }


  function loadLicenceOptions(){
    return array(
       array(
        'name'  => 'woocommerce-prisjakt_licence_key',
        'label' => __('Licence key', 'wc-prisjakt' ),
        'type'  => 'text',
        'value' => get_option('woocommerce-prisjakt_licence_key'),
        'css'   => 'licence-key',
      )
    );
	}

 

   
} // end of class