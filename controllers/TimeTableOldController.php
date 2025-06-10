<?php

/**
 * Description of TimeTableController
 *
 * @author SystemAnalyst
 */

namespace controllers;

class TimeTableController extends SuperController {

    public function indexAction() {
        $this->render('index');
    }

    public function displayTimeTableAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['majorId'] = '';
        $get = $this->get()->all();
//        print_r($get);exit;
        if (!empty($get['rn'])) {
            $rn = $get['rn'];
            $t_no = $get['semester'];
            $C_CODE = 21;
            $data['lectures'] = [1, 2, 3, 4, 5, 6];
            $data['practicalMajors'] = [1, 2, 3, 4, 5, 6, 7, 8, 9];
            $data['days'] = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday'];
            $oTimeTable = new \models\TimeTableModel();
            $data['timetableData'] = $oTimeTable->findByRN($rn, $t_no, $C_CODE);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'L']);
            if (empty($data['timetableData'])) {
                $obj->setHTML('<h1>Your Time Table Does Not Exist...</h1>');
                $obj->browse();
                exit;
            }
            $oStudent = new \models\StudentModel();
            $data['studentData'] = $oStudent->findStudentByRN($rn,$C_CODE, 2020);
//            var_dump($data['studentData']);exit;
            $oMajors = new \models\MajorsModel();
            $data['major'] = $oMajors->getMajorByYearClassIdAndMajorId($data['studentData']['YEAR'], $data['studentData']['CCODE'], $data['studentData']['MAJID']);
            $oSubjectCombination = new \models\SubjectCombinationModel();
            $data['subjects'] = $oSubjectCombination->getSubjectsByClassAndMajorAndSetNo($data['studentData']['CCODE'], $data['studentData']['MAJID'], $data['studentData']['SETNO']);
            $rem = $rn % 50;
            $tutorial = 0;
            if (Srem === 0) {
                $tutorial = 50;
            } else {
                $tutorial = $rem;
            }
            $data['studentData']['tutorial'] = $tutorial;

            $HTML = $this->getHTML('timeTable', $data);
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        } else {

            print_r("Please Enter Complete Information...");
        }
    }

}
