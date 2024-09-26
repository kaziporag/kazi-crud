<?php 

class EntryListTable extends WP_List_Table {

    public function __construct() {
        global $status, $page;
        parent::__construct(array(
            'singular' => __('Entry Data', 'kazi-crud'),
            'plural' => __('Entry Datas', 'kazi-crud'),
        ) );
    
    }

    public function column_default($item, $column_name) {
        switch($column_name){
          case 'action': echo '<a href="'.admin_url('admin.php?page=new-entry&entryid='.$item['id']).'">Edit</a>';
        }
        return @$item[$column_name];
    }

    public function column_feedback_name($item) {
      $actions = array( 'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id']) );
      return sprintf('%s %s', $item['id'], $this->row_actions($actions) );
    }

    public function column_cb($item) {
      return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item['id'] );
    }

    public function get_columns() {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'name'          => __('Name', 'kazi-crud'),
            'email'         => __('Email', 'kazi-crud'),
            'action'        => __('Action', 'kazi-crud')
        );
      return $columns;
    }

    public function get_sortable_columns() {
      $sortable_columns = array(
        'name' => array('name', true)
      );
      return $sortable_columns;
    }

    public function get_bulk_actions() {
      $actions = array( 'delete' => 'Delete' );
      return $actions;
    }

    public function process_bulk_action() {
      global $wpdb;
      $table_name = "wp_crud";
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)") or wp_die($wpdb->last_error) or db_delete($this->table_name, $ids);;
            }
        }
    }

    public function prepare_items() {
        global $wpdb,$current_user;

        $table_name = "wp_crud";
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $total_items = $wpdb->get_var("SELECT SQL_CALC_FOUND_ROWS COUNT(id) FROM $table_name");

        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';

		if(isset($_REQUEST['s']) && $_REQUEST['s']!='') {
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE `name` LIKE '%".$_REQUEST['s']."%' OR `email` LIKE '%".$_REQUEST['s']."%' ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged * $per_page), ARRAY_A);
		} else {
			  $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged * $per_page), ARRAY_A);
		}

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        )) ;
    }
}