<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");


$path = $_SERVER['DOCUMENT_ROOT'];
$request_type = $_SERVER["REQUEST_METHOD"];
$json_data = json_decode(file_get_contents('php://input'), true);
if ( !empty($json_data["auth-token"]) ){
    $token = $json_data["auth-token"];
}
else{
    $token = "";
}

if (!empty($_REQUEST["_wp_nonce"])){
    $wp_nonce = $_REQUEST["_wp_nonce"];
}
else{
    $wp_nonce = "";
}

require_once dirname(__FILE__) . "/config.php";
require_once $path . '/wp-config.php';
require_once $path . '/wp-load.php';
require_once $path . '/wp-includes/wp-db.php';
require_once $path . "/wp-includes/pluggable.php";
require_once dirname(__FILE__) . "/include/security.php";
require_once dirname(__FILE__) . "/include/utils.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function error($error){
    $object = array(
        "error" => $error
    );
    echo json_encode($object);
    http_response_code(404);
}

//verify nonce
if ($wp_nonce == ""){
    error("invalid _wp_nonce (plugin error)");
    die();
}
else{
    $verify = wp_verify_nonce($wp_nonce, "ap_integrate");
    if (!$verify){
        error("invalid _wp_nonce (plugin error)");
        die();
    }
}

global $wpdb;

if ($request_type != "POST"){
    error("Incorrect request type (plugin error)");
    die();
}
if (current_user_can("manage_options")) {
   
    $token = AP_Security::clean_input($token);

    if ( !empty($token) ){
        $post = [
            "platform" => "wordpress",
            "url" => get_site_url()
        ];
        $data = wp_remote_post(AUTOPIGEON_API_DOMAIN . "integration/new/", array(
            "method" => "POST",
            "headers" => array(
                "Authorization" =>  "Token " . $token
            ),
            "body" => array(
                "platform" => "wordpress",
                "url" => get_site_url()
            )
        ));
        $data = $data['body'];
        $data = json_decode($data,true);

        if ($data["status"] == "good" && !empty($data["integration"])) {
            AP_Utils::wp_set_option("ap_auth_token", $token);
            AP_Utils::wp_set_option("ap_integration", $data["integration"]);
        }
        else if ($data["status"] == "failed"){
            error($data["error"]);
            die();
        }
        else{
            error("Unkown status from AutoPigeon servers (plugin error).");
            die();
        }
    }
    else{
        error("Token is required (plugin error)");
        die();
    }
   echo '{"done": true}';
}
else{
    error("You don't have permission");
    die();
}

?>
