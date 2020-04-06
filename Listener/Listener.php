<?php

class NervBot_Listener_Listener {

    // Etend le controller de la Taigachat
    public static function load_controller($class, array &$extend){
        switch ($class) {
            case 'Dark_TaigaChat_ControllerPublic_TaigaChat':
                    $extend[] = 'NervBot_ControllerPublic_Taigachat';
                break;
        }
    }

}

?>
