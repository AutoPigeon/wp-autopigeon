<?php

class AP_Emails{
    static public function get_emails(){
        $response = wp_remote_get(AUTOPIGEON_API_DOMAIN . "integration/emails/", array(
            "headers" => array(
                "Authorization" => "Token " . get_option("ap_auth_token")
            )
        ));
        if ( is_array( $response ) && ! is_wp_error( $response ) ) {
            try{
                $body = json_decode($response['body'], true);
                if ($body["status"] == "good"){
                    return $body["data"];
                }
                else{
                    return array();
                }
            }
            catch (Exception $exc){
                return array();
            }
        }
        else{
            return array();
        }
    }
}




?>