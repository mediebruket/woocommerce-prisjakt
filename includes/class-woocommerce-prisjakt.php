<?php



class Woocommerce_Prisjakt {

  protected $loader;
  protected $woocommerce_prisjakt;
  protected $version;
  protected $Options;


  public function __construct() {
    add_action( 'admin_menu', array($this, 'createSubmenu' ) );

    $this->woocommerce_prisjakt = WCP_SLUG;
    $this->version = WCP_VERSION;

    $this->load_dependencies();
    $this->define_admin_hooks();
  }


  public function createSubmenu() {
    $page = add_submenu_page(
        'woocommerce',
        __( 'Prisjakt', 'wc-prisjakt' ),
        __( 'Prisjakt', 'wc-prisjakt' ),
        'read',
        WC_Prisjakt_Admin,
        array( $this, 'adminPage')
      );
  }


  public function getTabs(){
    return array(
      'generalPage'       => __('General', 'wc-prisjakt'),
      'licencePage'       => __('Licence', 'wc-prisjakt'),
      );
  }

  public function adminPage() {
    global $woocommerce;
    $this->Options = new WC_PrisjaktOptions();

    $tab =  ( isset($_GET['tab'] ) && $_GET['tab'] ) ? $_GET['tab'] : 'generalPage';
    ?>
    <div class="wrap woocommerce">
      <div class="icon32" id="icon-woocommerce-importer"><br></div>
      <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
      <?php foreach ($this->getTabs() as $key => $text) {
        printf('<a href="%s" class="nav-tab %s">%s</a>', admin_url('admin.php?page='.WC_Prisjakt_Admin.'&tab='.$key) , (($tab == $key) ? 'nav-tab-active' : null), $text );
      }
      ?>
      </h2>
      <?php self::$tab(); ?>
    </div>
    <?php
  }


  public function generalPage(){
    if ( isset($_POST['update']) ){
      $this->Options->updateOptions( 'General' );
      $this->showUpdateMessage ( __( 'General settings updated', 'wc-prisjakt' ) );
    }

    if ( isset($_POST['update-csv']) ){
      $Admin = new Woocommerce_Prisjakt_Admin( WCP_SLUG, WCP_VERSION );
      $Admin->create_csv_file();
    }

    $this->showToolBox( __('General settings', 'wc-prisjakt'), $this->Options->getOptions('General') );

    echo '<hr/>';
    echo WC_PrisjaktHtmlBuilder::openForm($method="POST", $action='', $class='new-csv-file');
    echo WC_PrisjaktHtmlBuilder::buildInput( array('id' => 'update-csv', 'name' => 'update-csv', 'value' => '1', 'type'=>'hidden' ) );
    echo WC_PrisjaktHtmlBuilder::buildDesc( __('Create new csv file', 'wc-prisjakt' ), $css = 'mb-field-desc' );
    echo WC_PrisjaktHtmlBuilder::buildSubmitButton( __('create', 'wc-prisjakt') );
    echo WC_PrisjaktHtmlBuilder::closeForm();
  }



  public function licencePage(){
    if ( isset($_POST['update']) ){
      $this->Options->updateOptions( 'Licence' );
      $this->showUpdateMessage ( __( 'Licence updated', 'wc-prisjakt' ) );
    }

    $this->showToolBox( __('Licence settings', 'wc-prisjakt'), $this->Options->getOptions('Licence') );

    global $plugin_file;
    $Plugin = new WC_PrisjaktUpdater($plugin_file);
    $valid = $Plugin->checkLicenceKey();
    $color = ( $valid  == '1' ) ? '#009933' : '#cc0000';
    set_transient( $Plugin->Slug.'_last_check', $valid, 0 );
    printf('<style type="text/css">.licence-key{border:2px solid %s !important; }</style>', $color );
  }


  function showToolBox( $title, $options=null ){ ?>
    <div class="tool-box">
      <form action="" method="POST">
        <h3 class="title"><?php echo $title; ?></h3>
        <input type="hidden" name="update" value="1" />
        <?php echo $options; ?>
        <p>
          <input type="submit" class="button save" value="<?php _e('update', 'wc-prisjakt' ); ?>">
        </p>
      </form>
    </div>
  <?php
  }


  function showUpdateMessage( $text ){ ?>
     <div id="message" class="updated woocommerce-message wc-connect">
      <div class="squeezer">
        <h4><strong><?php echo $text; ?></strong></h4>
      </div>
    </div>
    <?php
  }



  private function load_dependencies() {
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-prisjakt-loader.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-prisjakt-admin.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-prisjakt-admin.php';
    $this->loader = new Woocommerce_Prisjakt_Loader();
  }


  private function define_admin_hooks() {
    if ( get_option( 'woocommerce-prisjakt_disable_auto_save' ) ){
      $plugin_admin = new Woocommerce_Prisjakt_Admin( $this->get_woocommerce_prisjakt(), $this->get_version() );
      $heartbeat = ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'heartbeat' ) ? true : false;
      $batch     = ( isset($_REQUEST['post_status']) && $_REQUEST['post_status'] == 'all' && $_REQUEST['post_type'] == 'product' ) ? true : false;

      if ( !$heartbeat && !$batch ){
        $this->loader->add_action( 'save_post_product', $plugin_admin, 'create_csv_file' );  
      }      
    }
  }
  

  public function run() {
    $this->loader->run();
  }

  public function get_woocommerce_prisjakt() {
    return $this->woocommerce_prisjakt;
  }

  public function get_loader() {
    return $this->loader;
  }

  public function get_version() {
    return $this->version;
  }

}