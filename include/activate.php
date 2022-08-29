<?php

class AutoPigeon_Activate{
    public function activate(){
        $this->create_event_tables();

    }
    private function create_event_tables(){
        require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "include/event.php");
        AP_Events::instance()->db_create_tables();
    }
}

?>