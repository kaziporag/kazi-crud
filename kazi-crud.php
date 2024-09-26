<?php
/**
 * Plugin Name: Kazi CRUD
 * Plugin URI: https://wordpress.org/plugins/kazi-crud
 * Description: Kazi CRUD ( Create, Read, Update & Delete ) Application Using Ajax & WP List Table
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Kazi Rabiul Islam
 * Author URI: https://wordpress.org/plugins/kazi-crud
 * Version: 1.0.1
 * textdomain: kazi-crud
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Class KaziCRUD
 */
class KaziCRUD {

  /**
   * The table name for the plugin
   * @var string
   */
  private $table_name;

  /**
   * The database version
   * @var string
   */
  private $dbv = '1.3';

  /**
   * Constructor
   */
  public function __construct() {
  
    global $wpdb;
    $this->table_name = "wp_crud";

    define( 'KAZI_CRUD_URL', plugin_dir_url( __FILE__ ) );
    define( 'KAZI_CRUD_PATH', plugin_dir_path( __FILE__ ) );
    
    register_activation_hook( __FILE__, [ $this, 'activate_crud_plugin_function' ] ); 
    register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

    add_action( 'admin_enqueue_scripts', [ $this, 'load_custom_css_js' ] );
    add_action('admin_menu', [ $this, 'my_menu_pages' ]);

    $dbv = get_option('dbv');
    if ($dbv != $this->dbv) {
        $this->create_database_tables();
        update_option('dbv', $this->dbv);
    }

    require_once(KAZI_CRUD_PATH.'/ajax/ajax_action.php');

    if (!class_exists('WP_List_Table')) {
      require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    }

    require_once(KAZI_CRUD_PATH.'/include/kc-list-items.php');

  }
  

  /**
   * Activate the plugin
   */
  function activate_crud_plugin_function() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = 'wp_crud';
  
    $sql = "CREATE TABLE $table_name (
      `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(50),
      `email` varchar(50),
      `created_at` varchar(50),
      `updated_at` varchar(50),
      PRIMARY KEY  (id)
    ) $charset_collate;";
  
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
  }

  /**
   * Deactivate the plugin
   */
  function deactivate() {
    global $wpdb;
    $sql = "DROP TABLE IF EXISTS $this->table_name";
    $wpdb->query($sql);
  }

  /**
   * Load custom css and js
   */
  public function load_custom_css_js() {
    wp_register_style( 'my_custom_css', KAZI_CRUD_URL.'/css/style.css', false, '1.0.0' );
    wp_enqueue_style( 'my_custom_css' );
    wp_enqueue_script( 'my_custom_script1', KAZI_CRUD_URL. '/js/custom.js' );
    wp_enqueue_script( 'my_custom_script2', KAZI_CRUD_URL. '/js/jQuery.min.js' );
    wp_localize_script( 'my_custom_script1', 'ajax_var', array( 'ajaxurl' => admin_url('admin-ajax.php') ));
  }
  
  

  /**
   * Create the menu pages
   */
  public function my_menu_pages() {
    add_menu_page( 
        __( 'KAZI_CRUD', 'kazi-crud' ),
        'All CRUD',
        'manage_options',
        'all-entry',
        [$this, 'my_submenu_output'],
        'dashicons-admin-generic',
        6
    ); 
    add_submenu_page(
        'all-entry', 
        __('Kazi CRUD', 'kazi-crud'), 
        __('New Entry', 'kazi-crud'), 
        'manage_options', 
        'new-entry', 
        [$this, 'my_menu_output'] );
}

  /**
   * Output the new entry page
   */
  public function my_menu_output() {
    require_once(KAZI_CRUD_PATH.'/admin-templates/new_entry.php');
  }

  /**
   * Output the view entries page
   */
  public function my_submenu_output() {
    global $wpdb;
    $table = new EntryListTable();
    $table->prepare_items();
    $message = '';
    if ('delete' === $table->current_action()) {
      $message = '<div class="div_message" id="message"><p>' . sprintf('Deleted Successfully: %d', count($_REQUEST['id'])) . '</p></div>';
    }
    ob_start();
  ?>
    <div class="wrap wqmain_body">
      <h2><?php echo esc_attr( 'View Entries', 'kazi-crud' ) ?></h2>
      <?php echo $message; ?>
      <form id="entry-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->search_box( 'search', 'search_id' ); $table->display() ?>
      </form>
    </div>
  <?php
    $wq_msg = ob_get_clean();
    echo $wq_msg;
  }

}

new KaziCRUD();