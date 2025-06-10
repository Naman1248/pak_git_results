<?php

/**
 * Description of ChallanController
 *
 * @author SystemAnalyst
 */

namespace controllers;

class ChallanController extends SuperController {

    public function indexAction() {
//        var_dump($data['ClassCodes']); exit;
        $oStudentChallans = new \models\StudentChallansModel();
        $data['classCodes'] = $oStudentChallans->challanClasses();
        $data['yearsList'] = \helpers\Common::yearList();
//        var_dump($data['ClassCodes']);
        $this->render('index', $data);
    }

    public function displayChallanAction() {
//        $this->render('html');exit;
        $get = $this->get()->all();
        $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'L']);
        if (!empty($get['yearAdmission']) && !empty($get['program']) && !empty($get['semester']) && !empty($get['rn'])) {
//            print_r($get);
            $year = $get['yearAdmission'];
            $ccode = $get['program'];
            $instNo = $get['semester'];
            $rn = $get['rn'];
            $oStudentChallans = new \models\StudentChallansModel();
            $data['challan'] = $oStudentChallans->findByYearClassRollNoInst($year, $ccode, $rn, $instNo);
            if ($data['challan']['DAYS'] > 0) {
                $oStudentFineModel = new \models\StudentFineModel();
                $fineData = $oStudentFineModel->add($data['challan']);
                $data['fine'] = $fineData;
//                if ($fineData['FINEAMOUNT'] > 0) {
                $newGrandTotal = $data['fine']['FINEAMOUNT'] + $data['challan']['GRANDTOTAL'];
                $data['challan']['AMNTINWORDS'] = \helpers\Common::numberToWords($newGrandTotal);
//                }
            }
            if (empty($data['challan'])) {
                $obj->getPDFObject()->WriteHTML("<H1> Dues Information Does Not Exist... </H1>", 2);
                $obj->getPDFObject()->output();
            } else {
                $banks = [
                    1 => ['name' => 'United Bank Limited', 'accountNo' => 'UBL MCA # : 275635026', 'shortName' => 'UBL'],
                    2 => ['name' => 'Habib Bank Limited', 'accountNo' => 'HBL MCA # : 00427992134303', 'shortName' => 'HBL']
                ];

                $semesters = ['0 Semester', '1st Semester', '2nd Semester', '3rd Semester', '4th Semester', '5th Semester', '6th Semester', '7th Semester', '8th Semester'];
                $data['bank'] = $banks[$data['challan']['BANKID']];
                
                $data['semester'] = $semesters[$instNo];
                $data['challan']['ID'] = str_pad($data['challan']['ID'], 6, '0', STR_PAD_LEFT);
                $heads = ["BANK COPY", "UNIVERSITY COPY", "STUDENT 'S COPY"];
                $data['heads'] = $heads;

                $HTML = $this->getHTML('challan', $data);
//                echo '<link rel="stylesheet" href="' . ASSET_URL . 'ss/pdfForm.css?' . time() . '"';
//                exit;
                $css = file_get_contents(ASSET_URL . 'ss/pdfForm.css?' . time());
                $obj->getPDFObject()->WriteHTML($css, 1);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                $obj->getPDFObject()->output();
            }
        } else {
            $obj->getPDFObject()->WriteHTML("<H1> Please Enter Complete Information...</H1>", 2);
            $obj->getPDFObject()->output();
        }
    }

}
