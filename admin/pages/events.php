<?php

require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "admin/page.php");
require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "include/utils.php");
require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "include/security.php");
require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "include/event.php");

class AP_Events_Page extends AP_Page{
    public function __construct(){
        $this->title = "Events";
        $this->action_button_title = "Add Event";
        $this->action_button_action = "?page=ap_events&action=add";
        $this->action_pages = array(
            "add"=> array ("use_header" => false, "function" => [$this, "page_add"])
        );

        $this->setup();
    }

    public function default_page(){
        ?>
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th>
                        Name
                    </th>
                    <th>
                        Type
                    </th>
                    <th>
                        Trigger
                    </th>
                    <th>
                        Event
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $events = AP_Events::instance()->get_events();
                if ($events == array()){
                    ?>
                    <tr>
                        <td>No Events</td>
                    </tr>
                    <?php
                }
                else{
                    foreach ($events as $event){
                        $wpnonce_delete = wp_create_nonce("ap_delete_" . $event["id"]);
                    ?>
                    <tr>
                        <td>
                            <strong>
                        <?php
                            echo AP_Utils::dot_long_string(esc_html($event["_name"]), 50);
                        ?>
                        </strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a href="<?php echo esc_url("?page=ap_events&action=edit&event=".esc_html($event["id"])) ?>">Edit</a> |
                            </span>
                            <span class="trash">
                                <a href="<?php echo esc_url("?page=ap_events&action=delete&event=".esc_html($event["id"]) . "&_wponce=" . $wpnonce_delete)?>" class="submitdelete" aria-label="Move “Hello world!” to the Trash">
                                    Trash
                                </a>
                            </span>
                        </div>
                        </td>
                        <td>
                        <?php
                            echo esc_html($event["_type"]);
                        ?>
                        </td>
                        <td>
                        <?php
                            echo esc_html($event["_trigger_name"]);
                        ?>
                        </td>
                        <td>
                            <?php
                                echo esc_html($event["_action"]);
                            ?>
                        </td>
                    </tr>
                    <?php
                    }
                }
                ?>
                
            </tbody>
        </table>
        <?php
    }

    
    public function page_add(){
        
        if (AP_Utils::check_get_field("event_type")){
            if ($_GET["event_type"] == "post_notification"){

                if ($this->method == "POST"){

                    if (!AP_Utils::verify_nonce("_wpnonce")){
                        wp_safe_redirect(admin_url("admin.php?page=ap_events"));
                        die();
                    }

                    if (AP_Utils::check_post_field("email")){
                        $email = $_POST["email"];
                        if (AP_Utils::check_post_field("name")){
                            $name = $_POST["name"];
                            AP_Events::instance()->add_event(array(
                                "name" => $name,
                                "type" =>  "Post Notification",
                                "trigger" => "publish_post",
                                "trigger_name" => "Publish Post",
                                "event" => "Send Email",
                                "options" => array("email"=>$email)
                            ));
                            wp_safe_redirect( admin_url("admin.php?page=ap_events") );
                            die();

                        }else{
                            $this->add_form_error("name", "YOu must supply a name");
                        }
                        
                        
                    }
                    else{
                        $this->add_form_error("email", "You must select an email");
                    }

                    
                }
                

                require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "include/forms/events.form.emails.php");
                $form = new AP_Events_Emails_Form("POST", "?page=ap_events&action=add&event_type=post_notification",$this, "ap_events_email_post_notification");
                $form->render();
                
            }
            else if ($_GET["event_type"] == "wc_product_notification"){
                require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "include/forms/events.form.emails.php");
                $form = new AP_Events_Emails_Form("POST", "?page=ap_events&action=add&event_type=wc_product_notification",$this, "ap_events_email_wc_product_notification");
                $form->render();
            }
            else if ($_GET["event_type"] == "subscribe"){
                require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "include/forms/events.form.emails.php");
                $form = new AP_Events_Emails_Form("POST", "?page=ap_events&action=add&event_type=subscribe",$this, "ap_events_email_subscribe");
                $form->render();
            }
            else{
                wp_safe_redirect(admin_url("admin.php?page=ap_events"));
                die();
            }
        }
        else{



            ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="card" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">Post Notifications</h5>
                        <p class="card-text ">Post notifications will send an email to your subscribers when you publish a new post</p>
                        <a href="?page=ap_events&action=add&event_type=post_notification" class="btn btn-primary">Create New</a>
                    </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">Product Notification (WooCommerce)</h5>
                        <p class="card-text ">Product notifications will send an email to your subscribers when you publish a new product with woocommerce </p>
                        <a href="?page=ap_events&action=add&event_type=wc_product_notification" class="btn btn-primary">Create New</a>
                    </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">Subscribe</h5>
                        <p class="card-text ">This will send an email to the new subscriber on subscription</p>
                        <a href="?page=ap_events&action=add&event_type=subscribe" class="btn btn-primary">Create New</a>
                    </div>
                    </div>
                </div>

            </div>
        <?php
        }
    }
}
?>