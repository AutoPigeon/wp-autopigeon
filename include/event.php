<?php



/**
 *  --- Triggered Events ---
 * 
 * id
 * trigger
 * handler
 * datetime
 * options
 * 
 */

class AP_Events{

    static function instance() {
        static $inst = null;
        if ($inst === null) { $inst = new self; }
        return $inst;
    }
    private function __construct() { }
    private function __clone() { }

    public function db_create_tables(){
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        if (AUTOPIGEON_DEVELOPMENT_MODE){
            $wpdb->query("DROP TABLE `{$wpdb->base_prefix}ap_events`");
            $wpdb->query("DROP TABLE `{$wpdb->base_prefix}ap_triggered_events`");
        }
    
        $triggered_events_sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}ap_triggered_events` (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            _trigger TEXT,
            handler TEXT,
            creation_date DATETIME,
            _target INT(50),
            options TEXT
            ) $charset_collate;
        ";
        $events_sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->base_prefix}ap_events` (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            _trigger TEXT,
            _trigger_name TEXT,
            _action TEXT,
            _options TEXT,
            _type TEXT,
            _name TEXT
            ) $charset_collate;
        ";

        $wpdb->query($triggered_events_sql);
        $wpdb->query($events_sql);

    }

    public function get_events(){
        global $wpdb;
        $result = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}ap_events` ORDER BY id DESC", ARRAY_A);
        return $result;
    }

    public function add_event($options){
        global $wpdb;
        $wpdb->insert(
            "{$wpdb->base_prefix}ap_events",
            array(
                "_trigger" => $options["trigger"],
                "_action" => $options["event"],
                "_trigger_name" => $options["trigger_name"],
                "_type" => $options["type"],
                "_options" => json_encode($options["options"]),
                "_name" => $options["name"]
            )
        );
        return $wpdb->insert_id;
    }
    public function edit_event_options($id, $option){
        global $wpdb;
        $updated = $wpdb->update("{$wpdb->base_prefix}ap_events", array(
            "_options"=>$options
        ), array(
            "id"=>$id
        ));
        return $updated;
    }
    private function event_trigger($trigger, $handler, $options, $target){
        global $wpdb;
        $creation_date = date('d-m-y h:i:s');
        $_options = json_encode($options);
        $wpdb->insert("{$wpdb->base_prefix}ap_triggered_events", array(
            "trigger" => $trigger,
            "handler" => $handler,
            "creation_date" => $creation_date,
            "options" => $_options,
            "_target" => $target
        ));
    }
    private function check_already_triggered($handler, $trigger, $target){
        global $wpdb;
        $results = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->base_prefix}ap_triggered_events` WHERE handler='%s' AND trigger='%s' AND _target='%s' LIMIT 1", $handler,$trigger,$target), ARRAY_A);
        if ($results == null){
            return false;
        }
        else{
            return true;
        }
    }

    public function setup_event_listeners(){
        add_action("publish_post", [$this, "event_handler_publish_post"]);
        add_action("publish_page", [$this, "event_handler_publish_page"]);
    }
    // event handlers
    public function event_handler_publish_post($post_id, $post){
        $handler = __METHOD__;
        

        if (in_array($post->post_type, array("product"))){
            $this->event_handler_public_post_product($post_id, $post); // handle the product in another function
        }
        else{
            event_trigger(
                "publish_post",
                __METHOD__,
                array(
                    
                ),
                $post_id
            );
        }
    }

    private function event_handler_public_post_product($product_id, $product) // woocommerce
    {

    }

    public function event_handler_publish_page($page_id, $page){

    }
}