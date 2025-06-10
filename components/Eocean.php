<?php

/**
 * Description of Eocean
 *
 * @author SystemAnalyst
 */

namespace components;

class Eocean {


    public static function sendSms($number, $message) {
        $url = 'https://pk.eocean.net/APIManagement/API/RequestAPI?user=gcu&pwd=ABdZS45rl2Raf7CKzhVHC1woaeSKibcSb%2fsb4S25rL81j8SSc82fOuqS5Owgvyoxog%3d%3d&sender=GCU&reciever=_Number_&msg-data=_MESSAGE_&response=string';
//        $url = 'https://pk.eocean.us/APIManagement/API/RequestAPI?user=gcu&pwd=ABdZS45rl2Raf7CKzhVHC1woaeSKibcSb%2fsb4S25rL81j8SSc82fOuqS5Owgvyoxog%3d%3d&sender=GCU&reciever=_Number_&msg-data=_MESSAGE_&response=string';
        $url = str_replace(['_Number_', '_MESSAGE_'], [$number, urlencode($message)], $url);
//        echo $url;exit;        
        file_get_contents($url);
//        var_dump(file_get_contents($url));
    }
}
