<?php

/**
 * Description of ConvocationController
 *
 * @author SystemAnalyst
 */

namespace controllers;

class ConvocationController extends SuperController {

    public function indexAction() {
//        die('NA');
        $this->render('index');
    }

    public function searchAction() {
        $get = $this->post()->all();
//        print_r($get);exit;
        if (!empty($get['rn'])) {
            $rn = $get['rn'];
            $oConvocation = new \models\ConvocationModel();
            $data = $oConvocation->findByRN($rn);
            if (empty($data)) {
                echo 'Roll Number Does Not Exist...';
            } else {
                //print_r($data);
                $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'L']);
                $HTML = $this->getHTML('invitation', $data);
                
                echo $HTML;
                exit;
//                $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/conv.css'), 1);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                $obj->getPDFObject()->output();
            }
        } else {
            echo "Please Try Again...";
        }
    }

}
