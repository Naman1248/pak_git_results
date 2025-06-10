<?php

/**
 * Description of SeatingPlanController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp;

class SeatingPlanController extends StateController {

    public function CityWiseSeatingPlanAction() {

        $this->render('cityWiseSeatingPlan');
    }

    public function RoomsAction() {

        if ($this->post()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            if (!empty($post['offerId'])) {
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorsByOfferId($post['offerId']);
                $oRoomsModel = new \models\RoomsModel();
                $data['rooms'] = $oRoomsModel->findAll();
//                var_dump($data['rooms']);exit;
//                echo '<pre>';
//                var_dump($data);exit;
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('rooms', $data);
    }

    public function GATAttendanceSheetPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className');
            $oGatSlipModel = new \models\GatSlipModel();
            $data['applications'] = $oGatSlipModel->applicantsByOfferIdAndSlotNoAndRoomId($get['offerId'], $get['slotNo'], $get['roomId'], $get['cityId'], $get['roomFor']);
            $data['venueMajors'] = $oGatSlipModel->majorsByOfferIdAndSlotNoAndRoomId($get['offerId'], $get['slotNo'], $get['roomId'], $get['cityId']);
//            echo "<pre>";
//            print_r($data['applications']);exit;
            $data['total'] = sizeof($data['applications']);
            $oTestScheduleModel = new \models\TestScheduleModel();
            $data['slotDetail'] = $oTestScheduleModel->getDetailBySlotId($get['offerId'], $get['slotNo']);

            $oRoomsModel = new \models\RoomsModel();
            $data['venues'] = $oRoomsModel->findByPK($get['roomId'], 'venue');
            $data['venue'] = $data['venues']['venue'];
            $oMetaDataModel = new \models\MetaDataModel();
            $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $get['cityId']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('GATAttendanceSheetPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Attendance Sheet</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function GATSlotWiseApplicantsPDFAction() {
        //ini_set('memory_limit', '1024M');
        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className');

            $oTestScheduleModel = new \models\TestScheduleModel();
            $data['slotDetail'] = $oTestScheduleModel->dateAndTimeByofferIdAndSlotNo($get['offerId'], $get['slotNo']);
            $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($get['slotId']);
            $oMetaDataModel = new \models\MetaDataModel();
            $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $get['cityId']);
            $oGatSlipModel = new \models\GatSlipModel();
            $totalAppsData = $oGatSlipModel->applicantsByOfferIdAndSlotNo($offerIds, $get['slotNo'], $get['cityId'], ['count' => true]);
            $totalApps = $totalAppsData[0]['total'];
            $perPage = 500;
            $start = -$perPage;
            $files = [];
            $oZipArchive = new \ZipArchive();
            $zipFileBaseName = 'SeatingPlan-' . date('YmdHis') . '.zip';
            $zipFileName = UPLOAD_PATH . $zipFileBaseName;
            if ($oZipArchive->open($zipFileName, \ZIPARCHIVE::CREATE) !== TRUE) {
                die('Failed to load zip file');
            }
            $totalIterations = ceil($totalApps / $perPage);
            for ($i = 1; $i <= $totalIterations; $i++) {
                $start = $start + $perPage;
                $baseName = date('YmdHis') . '-' . $i . '.pdf';
                $fileName = UPLOAD_PATH . $baseName;
                $files[] = $fileName;
                $data['offset'] = $start;
                $data['applications'] = $oGatSlipModel->applicantsByOfferIdAndSlotNo($offerIds, $get['slotNo'], $get['cityId'], ['paging' => true, 'start' => $start, 'offset' => $perPage]);
//            echo "<pre>";
//            print_r($data['applications']);exit;
                $data['total'] = sizeof($data['applications']);

                $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
                $HTML = $this->getHTML('GATSlotWiseAllApplicantsPDF', $data);
                $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
                $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Attendance Sheet</td></tr></table> ");
                $obj->getPDFObject()->SetHeader($obj->getHeader());
                $obj->getPDFObject()->SetFooter($obj->getFooter());
                $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                $obj->getPDFObject()->output($fileName, 'F');
                $oZipArchive->addFile($fileName, $baseName);
            }
            $oZipArchive->close();
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=$zipFileBaseName");
            header("Content-length: " . filesize($zipFileName));
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile("$zipFileName");
        }
    }

    public function slotWiseApplicantDownloadAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oTestScheduleModel = new \models\TestScheduleModel();
            $data['slotDetail'] = $oTestScheduleModel->dateAndTimeByofferIdAndSlotNo($get['offerId'], $get['slotNo']);
            $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($get['slotId']);
            $oGatSlipModel = new \models\GatSlipModel();
//            $fields = 'a.userId,a.formNo,u.name applicantName,u.gender,u.fatherName,u.cnic,u.dob,u.ph1,u.email,u.add1,ao.className,m.name majorName,b.name baseName,isPaid,a.offerId,a.appId,m.majId,a.baseId,a.cCode';
            $fields = 'userId, formNo, rn, rollNo, name, fatherName, cnic, venue, major, date, time, slotNo, majId, roomId';
            $data['results'] = $oGatSlipModel->applicantsByOfferIdAndSlotNoForExcel($offerIds, $get['slotNo'], $get['cityId'], $fields);
            $data['total'] = sizeof($data['results']);
            $headings = ['userId' => "User Id", 'formNo' => "Form #", 'rn' => "RN", 'rollNo' => "Roll No.",
                'name' => "Applicant Name", 'fatherName' => "Father Name", 'cnic' => "CNIC",
                'venue' => "Venue", 'major' => "Major Name", 'date' => "Test Date", 'time' => "Test Time", 'slotNo' => "Slot No", 'majId' => "Major Id", 'roomId' => "Room Id"
            ];
//            print_r($offerData['className']);exit;
            \helpers\Common::downloadCSV($data['results'], $data['slotDetail']['date'] . '-Slot-' . $get['slotNo'], $headings);
        }
    }

    public function GATBlankAwardLisPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode, className, studyLevel, year');
            $oTestScheduleModel = new \models\TestScheduleModel();
            $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($get['slotId']);
            $oGatSlipModel = new \models\GatSlipModel();
            $data['applications'] = $oGatSlipModel->applicantsByOfferIdAndMajorId($offerIds, $get['majorId'], $get['cityId']);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorPrintitleByOfferIdAndMajorId($get['offerId'], $get['majorId']);
            $oMetaDataModel = new \models\MetaDataModel();
            $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $get['cityId']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            if (empty($data['applications'])) {
                $obj->setHTML('<h1>No Award List Data Avaiable For ' . $data['majorName'] . ' Major. </h1>');
                $obj->browse();
                exit;
            }

//            $HTML = $this->getHTML('GATBlankAwardListMajorWisePDF', $data);
            $obj->setFooter("<table><tr><td colspan='2' align='left' style='padding-top: 15px; padding-bottom: 10px; border-top:#00FF00 solid thin;'><h3>Name : _____________________</h3></td>
            <td colspan='2' align='right' style='padding-top: 15px; padding-bottom: 10px;'><h3>Signature : ___________________</h3></td>
            </tr>
            <tr><td colspan='2' style='border-top:#000 solid thin;'>@Powered by System Analyst Office</td><td style='border-top:#000 solid thin;' align='right'>Page# {PAGENO} of {nbpg}</td><td style='border-top:#000 solid thin;' align='right'></td></tr></table> ");
//            <tr><td colspan='2' style='border-top:#000 solid thin;'>@Powered by System Analyst Office</td><td style='border-top:#000 solid thin;' align='right'>Page# {PAGENO} of {nbpg}</td><td style='border-top:#000 solid thin;' align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");

            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Award List</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $offset = 20;
            $data['offset'] = 0;
            $chunks = array_chunk($data['applications'], $offset);
            $iterations = count($chunks);
            $i = 0;

            $fileName = 'GATBlankAwardListMajorWisePDF';
            if ($data['offerData']['studyLevel'] == 2) {

                $fileName = 'InterBlankAwardListMajorWisePDF';
            }

            foreach ($chunks as $row) {
                $data['applications'] = $row;
                $HTML = $this->getHTML($fileName, $data);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                if (++$i < $iterations) {
                    $obj->addPage();
                    $data['offset'] += 20;
                }
            }
            $obj->getPDFObject()->output();
        }
    }

    public function GATBlankAwardLisRNRangePDFAction() {
        $get['majorId'] = isset($_GET['majorId']) ? $_GET['majorId'] : null;
        $get['offerId'] = isset($_GET['offerId']) ? $_GET['offerId'] : null;
        $get['slotId'] = isset($_GET['slotId']) ? $_GET['slotId'] : null;
        $get['cityId'] = isset($_GET['cityId']) ? $_GET['cityId'] : null;
        $get['startRn'] = isset($_GET['startRn']) ? $_GET['startRn'] : null;
        $get['endRn'] = isset($_GET['endRn']) ? $_GET['endRn'] : null;
        $get['perPage'] = isset($_GET['perPage']) ? $_GET['perPage'] : null;
//        print_r($get); exit;
        if (!empty($get['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode, className, studyLevel, year');
            $oTestScheduleModel = new \models\TestScheduleModel();
            $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($get['slotId']);

            $oGatSlipModel = new \models\GatSlipModel();
            $data['applications'] = $oGatSlipModel->applicantsByOfferIdAndMajorIdByRnRange($offerIds, $get['majorId'], $get['cityId'], $get['startRn'], $get['endRn']);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorPrintitleByOfferIdAndMajorId($get['offerId'], $get['majorId']);
            $oMetaDataModel = new \models\MetaDataModel();
            $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $get['cityId']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            if (empty($data['applications'])) {
                $obj->setHTML('<h1>No Award List Data Avaiable For ' . $data['majorName'] . ' Major. </h1>');
                $obj->browse();
                exit;
            }

//            $HTML = $this->getHTML('GATBlankAwardListMajorWisePDF', $data);
            $obj->setFooter("<table><tr><td colspan='2' align='left' style='padding-top: 15px; padding-bottom: 10px; border-top:#00FF00 solid thin;'><h3>Name : _____________________</h3></td>
            <td colspan='2' align='right' style='padding-top: 15px; padding-bottom: 10px;'><h3>Signature : ___________________</h3></td>
            </tr>
            <tr><td colspan='2' style='border-top:#000 solid thin;'>@Powered by System Analyst Office</td><td style='border-top:#000 solid thin;' align='right'>Page# {PAGENO} of {nbpg}</td><td style='border-top:#000 solid thin;' align='right'></td></tr></table> ");
//            <tr><td colspan='2' style='border-top:#000 solid thin;'>@Powered by System Analyst Office</td><td style='border-top:#000 solid thin;' align='right'>Page# {PAGENO} of {nbpg}</td><td style='border-top:#000 solid thin;' align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");

            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Award List</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $offset = $get['perPage'];
            $data['offset'] = 0;
            $chunks = array_chunk($data['applications'], $offset);
            $iterations = count($chunks);
            $i = 0;

            $fileName = 'GATBlankAwardListMajorWisePDF';
            if ($data['offerData']['studyLevel'] == 2) {

                $fileName = 'InterBlankAwardListMajorWisePDF';
            }

            foreach ($chunks as $row) {
                $data['applications'] = $row;
                $HTML = $this->getHTML($fileName, $data);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                if (++$i < $iterations) {
                    $obj->addPage();
                    $data['offset'] += $get['perPage'];
                }
            }
            $obj->getPDFObject()->output();
        }
    }

    public function GATWithMarksAwardLisPDFAction() {
        $get['majorId'] = isset($_GET['majorId']) ? $_GET['majorId'] : null;
        $get['offerId'] = isset($_GET['offerId']) ? $_GET['offerId'] : null;
        $get['slotId'] = isset($_GET['slotId']) ? $_GET['slotId'] : null;
        $get['cityId'] = isset($_GET['cityId']) ? $_GET['cityId'] : null;
        $get['startRn'] = isset($_GET['startRn']) ? $_GET['startRn'] : null;
        $get['endRn'] = isset($_GET['endRn']) ? $_GET['endRn'] : null;
        $get['perPage'] = isset($_GET['perPage']) ? $_GET['perPage'] : null;
        if (!empty($get['offerId']) && !empty($get['majorId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode, className, studyLevel, year');
            $oTestScheduleModel = new \models\TestScheduleModel();
            $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($get['slotId']);
            $oGatResultModel = new \models\gatResultModel();
            $data['applications'] = $oGatResultModel->applicantsByOfferIdAndByMajorIdByRNRange($offerIds, $get['majorId'], $get['cityId'], $get['startRn'], $get['endRn']);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorPrintitleByOfferIdAndMajorId($get['offerId'], $get['majorId']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            if (empty($data['applications'])) {
                $obj->setHTML('<h1>No Result Data Avaiable For ' . $data['majorName'] . '. </h1>');
                $obj->browse();
                exit;
            }

//            $HTML = $this->getHTML('GATBlankAwardListMajorWisePDF', $data);
//            $obj->setFooter("<table><tr><td colspan='2' align='left' style='padding-top: 15px; padding-bottom: 10px; border-top:#00FF00 solid thin;'><h3>Name : _____________________</h3></td>
//            <td colspan='2' align='right' style='padding-top: 15px; padding-bottom: 10px;'><h3>Signature : ___________________</h3></td>
//            </tr>
//            <tr><td colspan='2' style='border-top:#000 solid thin;'>@Powered by System Analyst Office</td><td style='border-top:#000 solid thin;' align='right'>Page# {PAGENO} of {nbpg}</td><td style='border-top:#000 solid thin;' align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
//            
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Marks Sheet</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $offset = $get['perPage'];
            $data['offset'] = 0;
            $chunks = array_chunk($data['applications'], $offset);
            $iterations = count($chunks);
            $i = 0;
            $fileName = 'GATAwardListMajorWisePDF';
            if ($data['offerData']['studyLevel'] == 2) {

                $fileName = 'InterAwardListMajorWisePDF';
            }
            foreach ($chunks as $row) {
                $data['applications'] = $row;
                $HTML = $this->getHTML($fileName, $data);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                if (++$i < $iterations) {
                    $obj->addPage();
                    $data['offset'] += $get['perPage'];
                }
            }
            $obj->getPDFObject()->output();
        }
    }

    public function InterviewListPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId']) && !empty($get['majorId']) && !empty($get['baseId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode, className, studyLevel, year');
            $childOfferIds = $oAdmissionOffer->getChildOfferIds($get['offerId']);
            $oGatResultModel = new \models\gatResultModel();
            $data['applications'] = $oGatResultModel->applicantsByAllChildOfferIdsAndByMajorIdAndBaseId($childOfferIds, $get['majorId'], $get['baseId']);

            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            if (empty($data['applications'])) {
                $obj->setHTML('<h1>No Data Avaiable. </h1>');
                $obj->browse();
                exit;
            }

            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorPrintitleByOfferIdAndMajorId($get['offerId'], $get['majorId']);

            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['baseId']);

            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Marks Sheet</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $offset = 20;
            $data['offset'] = 0;
            $chunks = array_chunk($data['applications'], $offset);
            $iterations = count($chunks);
            $i = 0;
            $fileName = 'GATAwardListMajorWisePDF';
            if ($data['offerData']['studyLevel'] == 2) {

                $fileName = 'InterviewListMajorAndBaseWiseForInterPDF';
            }
            foreach ($chunks as $row) {
                $data['applications'] = $row;
                $HTML = $this->getHTML($fileName, $data);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                if (++$i < $iterations) {
                    $obj->addPage();
                    $data['offset'] += 20;
                }
            }
            $obj->getPDFObject()->output();
        }
    }

    public function GATNotificationDepttPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId']) && !empty($get['majorId']) && !empty($get['cityId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode, className, studyLevel, year');
            $oMajorsTestScheduleModel = new \models\MajorsTestScheduleModel();
            $data['slotDetail'] = $oMajorsTestScheduleModel->getScheduleByOfferIdAndMajorId($get['offerId'], $get['majorId'], $get['cityId'], $get['slotId']);
            $oTestSchedule = new \models\TestScheduleModel();
            $offerIds = $oTestSchedule->getOfferIdsBySlotId($get['slotId']);
            $oGatResultModel = new \models\gatResultModel();
            $data['applications'] = $oGatResultModel->applicantsByOfferIdAndByMajorIdAndByStatus($offerIds, $get['majorId'], $get['cityId'], 'Pass');
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorPrintitleByOfferIdAndMajorId($get['offerId'], $get['majorId']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            if (empty($data['applications'])) {
                $obj->setHTML('<h1>No Result Data Avaiable For ' . $data['majorName'] . ' Major. </h1>');
                $obj->browse();
                exit;
            }

            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Marks Sheet</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $offset = 20;
            $data['offset'] = 0;
            $chunks = array_chunk($data['applications'], $offset);
            $iterations = count($chunks);
            $i = 0;
            $fileName = 'GATNotificationDepttPDF';
//            if ($data['offerData']['studyLevel'] == 2) {
//
//                $fileName = 'InterNotificationPDF';
//            }
            foreach ($chunks as $row) {
                $data['applications'] = $row;
                $HTML = $this->getHTML($fileName, $data);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                if (++$i < $iterations) {
                    $obj->addPage();
                    $data['offset'] += 20;
                }
            }
            $obj->getPDFObject()->output();
        }
    }

    public function GATNotificationPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId']) && !empty($get['majorId']) && !empty($get['cityId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode, className, studyLevel, year');
            $oMajorsTestScheduleModel = new \models\MajorsTestScheduleModel();
            $data['slotDetail'] = $oMajorsTestScheduleModel->getScheduleByOfferIdAndMajorId($get['offerId'], $get['majorId'], $get['cityId'], $get['slotId']);
            $oTestSchedule = new \models\TestScheduleModel();
            $offerIds = $oTestSchedule->getOfferIdsBySlotId($get['slotId']);
            $oGatResultModel = new \models\gatResultModel();
            $data['applications'] = $oGatResultModel->applicantsByOfferIdAndByMajorIdAndByStatus($offerIds, $get['majorId'], $get['cityId'], 'Pass');
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorPrintitleByOfferIdAndMajorId($get['offerId'], $get['majorId']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            if (empty($data['applications'])) {
                $obj->setHTML('<h1>No Result Data Avaiable For ' . $data['majorName'] . ' Major. </h1>');
                $obj->browse();
                exit;
            }

            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Marks Sheet</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $offset = 20;
            $data['offset'] = 0;
            $chunks = array_chunk($data['applications'], $offset);
            $iterations = count($chunks);
            $i = 0;
            $fileName = 'GATNotificationPDF';
            if ($data['offerData']['studyLevel'] == 2) {

                $fileName = 'InterNotificationPDF';
            }
            foreach ($chunks as $row) {
                $data['applications'] = $row;
                $HTML = $this->getHTML($fileName, $data);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                if (++$i < $iterations) {
                    $obj->addPage();
                    $data['offset'] += 20;
                }
            }
            $obj->getPDFObject()->output();
        }
    }

    public function GATSeatingPlanPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className');
            $oGatSlipModel = new \models\GatSlipModel();
            $data['applications'] = $oGatSlipModel->applicantsByOfferIdAndSlotNoAndRoomId($get['offerId'], $get['slotNo'], $get['roomId'], $get['cityId'], $get['roomFor']);
            $data['venueMajors'] = $oGatSlipModel->majorsByOfferIdAndSlotNoAndRoomId($get['offerId'], $get['slotNo'], $get['roomId'], $get['cityId']);
//            echo "<pre>";
//            print_r($data['venueMajors']);exit;

            $data['total'] = sizeof($data['applications']);

            $oTestScheduleModel = new \models\TestScheduleModel();
            $data['slotDetail'] = $oTestScheduleModel->getDetailBySlotIdAndByCityId($get['offerId'], $get['slotNo'], $get['cityId']);
            $oMetaDataModel = new \models\MetaDataModel();
            $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $get['cityId']);
            $oRoomsModel = new \models\RoomsModel();
            $data['venues'] = $oRoomsModel->findByPK($get['roomId'], 'venue');
            $data['venue'] = $data['venues']['venue'];
//            echo "<pre>";
//            print_r($data);exit;
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('GATSeatingPlanPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Seating Plan</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function GATSlotWiseSeatingPlanPDFAction() {

        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className');
            $oTestScheduleModel = new \models\TestScheduleModel();
            $data['slotDetail'] = $oTestScheduleModel->dateAndTimeByofferIdAndSlotNo($get['offerId'], $get['slotNo']);
            $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($get['slotId']);
            $oMetaDataModel = new \models\MetaDataModel();
            $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $get['cityId']);
            $oGatSlipModel = new \models\GatSlipModel();
            $data['applications'] = $oGatSlipModel->countApplicantsByOfferIdWithVenueSlotAndVenueWise($offerIds, $get['slotNo'], $get['cityId']);
//            echo "<pre>";
//            print_r($data['applications']);exit;
            $data['total'] = sizeof($data['applications']);

            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('GATSlotWiseSeatingPlanPDF', $data);
//            $this->render('GATSlotWiseSeatingPlanPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Seating Plan</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function SlotWiseMajorsStrengthAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            if (!empty($post['offerId'])) {
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('slotWiseMajorsStrength', $data);
    }

    public function SlotWiseMajorsStrengthMultiAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            $data['slotNo'] = $post['slotNo'];
            $data['offerId'] = $post['offerId'];
            if (!empty($post['offerId'])) {
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getTestClassesByDeptt($data['dId'], $post['admissionYear']);
        if (!empty($post['admissionYear'])) {
            $oTestSlotsModel = new \models\TestSlotsModel();
            $data['activeTestSlots'] = $oTestSlotsModel->getSlotsByYear($post['admissionYear']);

            $oMetaDataModel = new \models\MetaDataModel();
            $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        }

        $data['yearList'] = \helpers\Common::yearList();
        $this->render('slotWiseMajorsStrengthMulti', $data);
    }

    public function ResultAnalysisForAClassPDFAction() {

        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode, className, testPassPer');
            $data['reqPer'] = $get['reqPer'];
            $oTestSchedule = new \models\TestScheduleModel();
            $offerIds = $oTestSchedule->getOfferIdsBySlotId($get['slotId']);
            $ogatResultModel = new \models\gatResultModel();
            $data['majors'] = $ogatResultModel->ResultStatbyOfferIds($offerIds, $data['offerData']['testPassPer'], $get['reqPer']);
            $data['totalPass'] = \helpers\Common::sumOfArray($data['majors'], 'Pass');
            $data['totalFail'] = \helpers\Common::sumOfArray($data['majors'], 'Fail');
            $data['totalAbsent'] = \helpers\Common::sumOfArray($data['majors'], 'Absent');
            $data['totalApplied'] = $data['totalPass'] + $data['totalFail'] + $data['totalAbsent'];
            $data['totalAppeared'] = $data['totalPass'] + $data['totalFail'];
            $data['totalReqPer'] = \helpers\Common::sumOfArray($data['majors'], 'reqPer');
//            echo '<pre>';
//            print_r($data['majors']);exit;
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);

            $fileName = 'ResultAnalysisForAClassPDF';
            if (!empty($get['reqPer'])) {
                $fileName = 'ResultAnalysisWithRequiredPerPDF';
            }

            $HTML = $this->getHTML($fileName, $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Majors Strength</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function ResultAnalysisAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            $data['slotNo'] = $post['slotNo'];
            $data['offerId'] = $post['offerId'];
            $data['reqPer'] = $post['reqPer'];
            if (!empty($post['offerId'])) {
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        if (!empty($post['admissionYear'])) {
            $oTestSlotsModel = new \models\TestSlotsModel();
            $data['activeTestSlots'] = $oTestSlotsModel->getSlotsByYear($post['admissionYear']);
        }
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('resultAnalysis', $data);
    }

    public function GATMajorsStrengthPDFAction() {

        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className');
            $oMajorsTestScheduleModel = new \models\MajorsTestScheduleModel();
            $data['majors'] = $oMajorsTestScheduleModel->allMajorsByOfferIdAndSlotWise($get['offerId']);
//            echo "<pre>";
//            print_r($data['majors']);exit;
//            $data['total'] = sizeof($data['applications']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('GATMajorsStrengthPDF', $data);
//            $this->render('GATMajorsStrengthPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Majors Strength</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function MultiOfferIdsMajorsStrengthPDFAction() {

        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className');
            $oMetaDataModel = new \models\MetaDataModel();
            $data['testCity'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $get['cityId']);
            $oMajorsTestScheduleModel = new \models\MajorsTestScheduleModel();
            $data['majors'] = $oMajorsTestScheduleModel->allMajorsByOfferIdsAndSlotWiseMulti($get['offerId'], $get['slotNo'], $get['cityId']);

            if ($get['cityId'] != 1) {
                $oMetaDataModel = new \models\MetaDataModel();
                $cityName = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $get['cityId']);
                $data['cityRn'] = '-' . substr($cityName['keyDesc'], 0, 1);
            }

            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('GATMajorsStrengthPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Majors Strength</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function GATSlotWiseEmployeeDutyPDFAction() {

        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className');

            $oEmployeeTestDutyModel = new \models\EmployeeTestDutyModel();
            $data['empDetail'] = $oEmployeeTestDutyModel->findByOfferIdAndSlotNo($get['offerId'], $get['slotNo'], $get['cityId']);

            $oTestScheduleModel = new \models\TestScheduleModel();
            $data['slotDetail'] = $oTestScheduleModel->dateAndTimeByofferIdAndSlotNo($get['offerId'], $get['slotNo']);
            $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($get['slotId']);

            $oGatSlipModel = new \models\GatSlipModel();
            $data['applications'] = $oGatSlipModel->countApplicantsByOfferIdWithVenueSlotAndVenueWise($offerIds, $get['slotNo'], $get['cityId'], $data['empDetail']);

            $data['total'] = sizeof($data['applications']);

            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);

            $HTML = $this->getHTML('GATSlotWiseEmpDutyPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Employee List</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);

            $oEmployeeFixDutyModel = new \models\EmployeeFixDutyModel();
            $data['fixEmployees'] = $oEmployeeFixDutyModel->getSortedFixEmployees();
            $HTML1 = $this->getHTML('GATSlotWiseEmpDutyFixPDF', $data);

            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->addPage();
            $obj->getPDFObject()->WriteHTML($HTML1, 2);

            $obj->getPDFObject()->output();
        }
    }

    public function SlotWiseDepartmentWiseEmployeeDutyPDFAction() {

        $get = $this->get()->all();
        if (!empty($get['offerId']) && !empty($get['depttId']) && !empty($get['slotId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className');

            $oDepttModel = new \models\DepttModel();
            $data['depttData'] = $oDepttModel->findByPK($get['depttId'], 'depttName');
            $oEmployeeTestDutyModel = new \models\EmployeeTestDutyModel();
            $data['empDetail'] = $oEmployeeTestDutyModel->findByOfferIdAndSlotIdAndDeptt($get['offerId'], $get['slotId'], $get['depttId']);

            $oTestScheduleModel = new \models\TestScheduleModel();
            $data['slotDetail'] = $oTestScheduleModel->dateAndTimeBySlotId($get['slotId']);

            $oGatSlipModel = new \models\GatSlipModel();
            $data['applications'] = $oGatSlipModel->countApplicantsBySlotIdWithVenueSlotAndVenueWise($get['slotId'], $get['depttId'], $data['empDetail']);
            $data['total'] = sizeof($data['applications']);

//            $oEmployeeFixDutyModel = new \models\EmployeeFixDutyModel();
//            $data['fixEmployees'] = $oEmployeeFixDutyModel->getSortedFixEmployees();

            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);

            $HTML = $this->getHTML('employeeDutyDepartmentWisePDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Employee List</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);

//            $HTML1 = $this->getHTML('GATSlotWiseEmpDutyFixPDF', $data);

            $obj->getPDFObject()->WriteHTML($HTML, 2);
//            $obj->addPage();
//            $obj->getPDFObject()->WriteHTML($HTML1, 2);

            $obj->getPDFObject()->output();
        }
    }

    public function ClassWiseEmployeesDutyPDFAction() {

        $get = $this->get()->all();
//        print_r($get);exit;

        if (!empty($get['offerId']) && !empty($get['depttId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className');

            $oDepttModel = new \models\DepttModel();
            $data['depttData'] = $oDepttModel->findByPK($get['depttId'], 'depttName');

            $oEmployeeTestDutyModel = new \models\EmployeeTestDutyModel();
            $data['empDetail'] = $oEmployeeTestDutyModel->findByOfferIdAndDepttAll($get['offerId'], $get['depttId']);
//            echo "<pre>";
//            print_r($data['empDetail']);
//            exit;
            $oTestScheduleModel = new \models\TestScheduleModel();
            $data['slots'] = $oTestScheduleModel->getScheduleByOfferIdAndCityId($get['offerId'], $get['cityId']);
//            $oGatSlipModel = new \models\GatSlipModel();
////            $data['applications'] = $oGatSlipModel->countApplicantsBySlotIdWithVenueSlotAndVenueWise($get['slotId'], $get['depttId'], $data['empDetail']);
//            $data['applications'] = $oGatSlipModel->countApplicantsByOfferIdAndCityIdWithVenue($get['offerId'], $get['cityId'], $get['depttId'], $data['empDetail']);
//            echo "<pre>";
//            print_r($data['applications']);
//            exit;
//            $data['total'] = sizeof($data['applications']);

            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);

            $HTML = $this->getHTML('employeeDutyClassAndDepttWisePDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Employee List</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);

//            $HTML1 = $this->getHTML('GATSlotWiseEmpDutyFixPDF', $data);

            $obj->getPDFObject()->WriteHTML($HTML, 2);
//            $obj->addPage();
//            $obj->getPDFObject()->WriteHTML($HTML1, 2);

            $obj->getPDFObject()->output();
        }
    }

    public function RollNumberFormatAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            if (!empty($post['offerId'])) {
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorsByOfferId($post['offerId']);
//                echo '<pre>';
//                var_dump($data);exit;
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('rollNumberFormat', $data);
    }

    public function InterviewListAction() {

        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['childBase'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            if (!empty($post['offerId']) && !empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $oAdmissionOffer = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOffer->findByPK($post['offerId'], 'cCode, className');
                $oBaseClass = new \models\BaseClassModel();
                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
                $data['baseId'] = $post['admissionBase'];
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorsByOfferId($post['offerId']);
//                echo '<pre>';
//                var_dump($data);exit;
            }
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('interviewList', $data);
    }

    public function TestScheduleAction() {
        $data['slotNo'] = '';
        $data['slotClass'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['id'] = $this->state()->get('depttUserInfo')['id'];
        $post = $this->post()->all();
        if ($this->isPost()) {
            if (!empty($post['slotNo'])) {
                $data['slotNo'] = $post['slotNo'];
                $oTestSlotsModel = new \models\TestSlotsModel();
                $data['slotDetail'] = $oTestSlotsModel->findByPK($post['slotNo']);
                $oTestScheduleModel = new \models\TestScheduleModel();
                $params = [
                    'offerId' => $post['offerId'],
                    'slotId' => $post['slotNo'],
                    'slotNo' => $data['slotDetail']['slotNo'],
                    'date' => $data['slotDetail']['date'],
                    'day' => $data['slotDetail']['day'],
                    'startTime' => $data['slotDetail']['startTime'],
                    'endTime' => $data['slotDetail']['endTime'],
                    'reportTime' => $data['slotDetail']['reportTime'],
                    'cityId' => $post['testCity'],
                    'addedBy' => $data['id'],
                    'addedOn' => date('Y-m-d H:i:s')
                ];
                $out = $oTestScheduleModel->insert($params);
            }
            if ($out) {
                $data['addMsg'] = 'Record added successfully.';
            } else {
                $data['errorMsg'] = 'Record not added, please try again..';
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];

        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');

        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);

        $oTestSlotsModel = new \models\TestSlotsModel();
        $data['activeTestSlots'] = $oTestSlotsModel->getSlotsByYear($post['admissionYear']);

        $data['yearList'] = \helpers\Common::yearList();

        $this->render('testSchedule', $data);
    }

    public function TestScheduleOldAction() {
        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['id'] = $this->state()->get('depttUserInfo')['id'];
        $post = $this->post()->all();
        if ($this->isPost()) {
            $oTestScheduleModel = new \models\TestScheduleModel();
            $params = [
                'offerId' => $post['offerId'],
                'slotNo' => $post['testSlotNo'],
                'date' => $post['testDate'],
                'day' => $post['dayOfTest'],
                'startTime' => $post['testStartTime'],
                'endTime' => $post['testEndTime'],
                'reportTime' => $post['testReportingTime'],
                'addedBy' => $data['id'],
                'addedOn' => date('Y-m-d H:i:s')
            ];

            $out = $oTestScheduleModel->insert($params);
            if ($out) {
                $data['addMsg'] = 'Record added successfully.';
            } else {
                $data['errorMsg'] = 'Record not added, please try again..';
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('testScheduleOld', $data);
    }

    public function TestScheduleClassAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['id'] = $this->state()->get('depttUserInfo')['id'];
        $post = $this->post()->all();
        if ($this->isPost()) {
            $oTestSlotsModel = new \models\TestSlotsModel();
            $params = [
                'year' => $post['admissionYear'],
                'class' => $post['slotClass'],
                'slotNo' => $post['testSlotNo'],
                'date' => $post['testDate'],
                'day' => $post['dayOfTest'],
                'startTime' => $post['testStartTime'],
                'endTime' => $post['testEndTime'],
                'reportTime' => $post['testReportingTime'],
                'addedBy' => $data['id'],
                'addedOn' => date('Y-m-d H:i:s')
            ];

            $out = $oTestSlotsModel->insert($params);
            if ($out) {
                $data['addMsg'] = 'Record added successfully.';
            } else {
                $data['errorMsg'] = 'Record not added, please try again..';
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $data['yearList'] = \helpers\Common::yearList();
        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        $data['testPrograms'] = $oMetaDataModel->byKeyValue('testProgram');

        $this->render('testScheduleClass', $data);
    }

    public function AddMajorTestSlotAction() {
        $post = $this->post()->all();
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['id'] = $this->state()->get('depttUserInfo')['id'];

        $data['slotNo'] = $post['testSlot'];
        $data['offerId'] = $post['offerId'];

        if ($this->isPost()) {
            $data['slotNo'] = $post['testSlot'];
            $data['offerId'] = $post['offerId'];
            $oTestScheduleModel = new \models\TestScheduleModel();
            $data['slots'] = $oTestScheduleModel->byOfferId($post['offerId']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majors'] = $oMajorsModel->getMajorsByOfferId($post['offerId']);
            $oTestSlotsModel = new \models\TestSlotsModel();
            $data['slotDetail'] = $oTestSlotsModel->findByPK($data['slotNo']);
            $oMajorsTestScheduleModel = new \models\MajorsTestScheduleModel();
            $params = [
                'offerId' => $post['offerId'],
                'majId' => $post['majorId'],
                'slotId' => $post['testSlot'],
                'cityId' => $post['testCity'],
                'slotNo' => $data['slotDetail']['slotNo'],
                'strength' => !empty($post['appStr']) ? $post['appStr'] : 0,
                'addedBy' => $data['id'],
                'addedOn' => date('Y-m-d H:i:s')
            ];

            $out = $oMajorsTestScheduleModel->insert($params);
            if ($out) {
                $data['addMsg'] = 'Record added successfully.';
            } else {
                $data['errorMsg'] = 'Record not added, please try again..';
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');

        $this->render('addMajorTestSlot', $data);
    }

    public function AddInterviewScheduleSlotWiseAction() {
        $post = $this->post()->all();
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['id'] = $this->state()->get('depttUserInfo')['id'];

        $data['offerId'] = $post['offerId'];

        if ($this->isPost()) {
//            echo "<pre>";
//            print_r($post);
            if (!empty($post['offerId']) && !empty($post['testCity']) && !empty($post['interviewSlotNo']) && !empty($post['majorId']) && !empty($post['interviewDate']) && !empty($post['interviewDay']) && !empty($post['interviewVenue']) && !empty($post['appStr'])) {
                $data['offerId'] = $post['offerId'];
                $data['testCity'] = $post['testCity'];
                $data['interviewSlotNo'] = $post['interviewSlotNo'];
                $data['majorId'] = $post['majorId'];
                $data['interviewDate'] = $post['interviewDate'];
                $data['interviewDay'] = $post['interviewDay'];
                $data['interviewVenue'] = $post['interviewVenue'];
                $data['interviewTime'] = $post['interviewTime'];
                $data['appStr'] = $post['appStr'];
                $data['baseId'] = 9;
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode, className');
                $offerIds = $oAdmissionOfferModel->getChildOfferIds($post['offerId']);
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorsByOfferId($post['offerId']);
                $ougtResultModel = new \models\UGTResultModel();
                $data['applications'] = $ougtResultModel->findByOfferIdsAndByMajorIdAndBaseIdWOInterviewSchedule($offerIds, $data['majorId'], $data['baseId'], $data['appStr']);
                echo "<pre>";
                print_r($data['applications']);
                exit;
                if ($out) {
                    $data['addMsg'] = 'Record added successfully.';
                } else {
                    $data['errorMsg'] = 'Record not added, please try again..';
                }
            } else {
                $data['errorMsg'] = 'Please Enter All Information.';
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');

        $this->render('AddInterviewScheduleSlotWise', $data);
    }

    public function MajorsTestSlotsListAction() {
        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            if (!empty($post['offerId'])) {
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oTestScheduleModel = new \models\TestScheduleModel();
                $data['slots'] = $oTestScheduleModel->byOfferIdSlots($post['offerId']);
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorsByOfferId($post['offerId']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('majorsTestSlotsList', $data);
    }

    public function RoomsSelectionAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $data['slotId'] = $post['slotId'];
                $oTestScheduleModel = new \models\TestScheduleModel();
                $data['slotDetail'] = $oTestScheduleModel->getDetailBySlotId($post['offerId'], $data['slotId']);
                $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($data['slotId']);
                $slotNo = $data['slotDetail']['slotNo'];
//                echo '<pre>';
//                print_r($data['slotDetail']);exit;
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                if (!empty($post['testCity'])) {
                    $data['testCity'] = $post['testCity'];
                    $oMetaDataModel = new \models\MetaDataModel();
                    $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $post['testCity']);
                    $data['slots'] = $oTestScheduleModel->byOfferId($post['offerId'], $post['testCity']);

                    $oRoomsAllocationModel = new \models\RoomsAllocationModel();
                    $data['rooms'] = $oRoomsAllocationModel->byOfferIdAndSlotNoAll($post['offerId'], $slotNo, $post['testCity']);
//                $data['rooms'] = $oRoomsAllocationModel->byOfferIdAndSlotNoAll($post['offerId'], $data['slotId']);
//                echo '<pre>';
//                print_r($data['rooms']);exit;
                    $oGatSlipModel = new \models\GatSlipModel();
                    $data['totApp'] = $oGatSlipModel->countDistinctApplicantsBySlotId($data['slotId'], $post['testCity']);
                    $data['totMulti'] = $oGatSlipModel->getByOfferIdAndSlotNoMulti($offerIds, $slotNo, $post['testCity']);
                    $data['multiTotal'] = sizeof($data['totMulti']);

                    $data['totSingle'] = $oGatSlipModel->getBySlotNoSingleMajorApplicants($data['slotId'], $post['testCity']);
                    $data['singleTotal'] = sizeof($data['totSingle']);
                }
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];

        if (!empty($post['admissionYear'])) {

            $oMetaDataModel = new \models\MetaDataModel();
            $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        }
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('roomsSelection', $data);
    }

    public function SelectedRoomsAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $data['slotId'] = $post['slotId'];
                $oTestScheduleModel = new \models\TestScheduleModel();
                $data['slotDetail'] = $oTestScheduleModel->getDetailBySlotId($post['offerId'], $data['slotId']);
                $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($data['slotId']);
                $slotNo = $data['slotDetail']['slotNo'];
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');

                $data['slots'] = $oTestScheduleModel->byOfferId($post['offerId'], $post['testCity']);
                if (!empty($post['testCity'])) {
                    $data['testCity'] = $post['testCity'];
                    $oMetaDataModel = new \models\MetaDataModel();
                    $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $post['testCity']);
                    $data['slots'] = $oTestScheduleModel->byOfferId($post['offerId'], $post['testCity']);

                    $oRoomsAllocationModel = new \models\RoomsAllocationModel();
                    $data['rooms'] = $oRoomsAllocationModel->getSelectedRoomsbyOfferIdAndSlotNo($post['offerId'], $data['slotId'], $post['testCity']);
                    $oGatSlipModel = new \models\GatSlipModel();
                    foreach ($data['rooms'] as $key => $row) {
                        $majors = $oGatSlipModel->majorsByOfferIdAndSlotNoAndRoomId($post['offerId'], $data['slotId'], $row['roomId'], $post['testCity']);
                        $data['rooms'][$key]['majors'] = $majors['major'];
                    }

//                echo '<pre>';
//                print_r($data['rooms']);
//                exit;
                    $data['sumOfCapacity'] = array_sum(array_column($data['rooms'], 'capacity'));
                    $data['sumOfAllotted'] = array_sum(array_column($data['rooms'], 'allotted'));
//                echo '<pre>';
//                print_r($data['rooms']);exit;
                    $data['totApp'] = $oGatSlipModel->countDistinctApplicantsBySlotId($data['slotId'], $post['testCity']);
                    $data['totMulti'] = $oGatSlipModel->getByOfferIdAndSlotNoMulti($offerIds, $slotNo, $post['testCity']);

                    $data['multiTotal'] = sizeof($data['totMulti']);
                    $data['totSingle'] = $oGatSlipModel->getBySlotNoSingleMajorApplicants($data['slotId'], $post['testCity']);
                    $data['singleTotal'] = sizeof($data['totSingle']);
                }
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        if (!empty($post['admissionYear'])) {

            $oMetaDataModel = new \models\MetaDataModel();
            $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        }
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('selectedRooms', $data);
    }

    public function roomsListAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $data['slotId'] = $post['slotId'];
                $oTestScheduleModel = new \models\TestScheduleModel();
                $data['slotDetail'] = $oTestScheduleModel->getDetailBySlotId($post['offerId'], $data['slotId']);
                $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($data['slotId']);
                $slotNo = $data['slotDetail']['slotNo'];
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');

                $data['slots'] = $oTestScheduleModel->byOfferId($post['offerId'], $post['testCity']);
                if (!empty($post['testCity'])) {
                    $data['testCity'] = $post['testCity'];
                    $oMetaDataModel = new \models\MetaDataModel();
                    $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $post['testCity']);
                    $data['slots'] = $oTestScheduleModel->byOfferId($post['offerId'], $post['testCity']);

                    $oRoomsAllocationModel = new \models\RoomsAllocationModel();
                    $data['rooms'] = $oRoomsAllocationModel->getSelectedRoomsbyOfferIdAndSlotNo($post['offerId'], $data['slotId'], $post['testCity']);
                    $oGatSlipModel = new \models\GatSlipModel();
                    foreach ($data['rooms'] as $key => $row) {
                        $majors = $oGatSlipModel->majorsByOfferIdAndSlotNoAndRoomId($post['offerId'], $data['slotId'], $row['roomId'], $post['testCity']);
                        $data['rooms'][$key]['majors'] = $majors['major'];
                    }

//                echo '<pre>';
//                print_r($data['rooms']);
//                exit;
                    $data['sumOfCapacity'] = array_sum(array_column($data['rooms'], 'capacity'));
                    $data['sumOfAllotted'] = array_sum(array_column($data['rooms'], 'allotted'));
//                echo '<pre>';
//                print_r($data['rooms']);exit;
                    $data['totApp'] = $oGatSlipModel->countDistinctApplicantsBySlotId($data['slotId'], $post['testCity']);
                    $data['totMulti'] = $oGatSlipModel->getByOfferIdAndSlotNoMulti($offerIds, $slotNo, $post['testCity']);

                    $data['multiTotal'] = sizeof($data['totMulti']);
                    $data['totSingle'] = $oGatSlipModel->getBySlotNoSingleMajorApplicants($data['slotId'], $post['testCity']);
                    $data['singleTotal'] = sizeof($data['totSingle']);
                }
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        if (!empty($post['admissionYear'])) {

            $oMetaDataModel = new \models\MetaDataModel();
            $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        }
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('selectedRooms', $data);
    }

    public function SelectedDepartmentsAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            if (!empty($post['offerId']) && !empty($post['testCity']) && !empty($post['slotId'])) {
                $data['offerId'] = $post['offerId'];
                $data['slotId'] = $post['slotId'];
                $data['cityId'] = $post['testCity'];
                $oTestScheduleModel = new \models\TestScheduleModel();
                $data['slotDetail'] = $oTestScheduleModel->getDetailBySlotId($post['offerId'], $data['slotId']);
                $data['slots'] = $oTestScheduleModel->byOfferId($post['offerId'], $post['testCity']);

                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'year, cCode, className');

                $oEmployeeTestDutyModel = new \models\EmployeeTestDutyModel();
                $data['deptts'] = $oEmployeeTestDutyModel->findAllDepttsByOfferId($post['offerId']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');

        $this->render('selectedEmpDutyDeptt', $data);
    }

    public function SelectedRoomsPDFAction() {

        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $data['offerId'] = $get['offerId'];
            $data['slotId'] = $get['slotId'];
            $data['cityId'] = $get['cityId'];
            $data['cityName'] = $get['cityName'];
            $oAdmissionOfferModel = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOfferModel->findByPK($get['offerId'], 'cCode,className');
            $oTestScheduleModel = new \models\TestScheduleModel();
            $data['slotDetail'] = $oTestScheduleModel->getDetailBySlotIdAndByCityId($get['offerId'], $get['slotId'], $get['cityId']);
            $oRoomsAllocationModel = new \models\RoomsAllocationModel();
            $data['rooms'] = $oRoomsAllocationModel->getSelectedRoomsbyOfferIdAndSlotNo($get['offerId'], $get['slotId'], $get['cityId']);
            $data['sumOfCapacity'] = sizeof($data['rooms']);

            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('selectedRoomsPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Selected Rooms</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function EmployeeTestDutyAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['id'] = $this->state()->get('depttUserInfo')['id'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            if (isset($post['btn-addEmployee']) && !empty($post['offerId']) && !empty($post['slotId']) && !empty($post['venueId'])) {
//                echo '<pre>';
//                print_r($post);exit;
                $oDepttModel = new \models\DepttModel();
                $data['dName'] = $oDepttModel->findByPK($post['depttId'], 'depttName');

                $oTestScheduleModel = new \models\TestScheduleModel();
                $data['slotDetail'] = $oTestScheduleModel->getDetailBySlotId($post['offerId'], $post['slotId']);

                $oDesignationModel = new \models\DesignationModel();
                $data['desigName'] = $oDesignationModel->findByPK($post['designation'], 'desig');

                $oRoomsModel = new \models\RoomsModel();
                $data['venueName'] = $oRoomsModel->findByPK($post['venueId'], 'venue');

                $oEmployeeTestDutyModel = new \models\EmployeeTestDutyModel();
                $_data['offerId'] = $post['offerId'];
                $_data['cityId'] = $post['testCity'];
                $_data['slotId'] = $post['slotId'];
                $_data['slotNo'] = $data['slotDetail']['slotNo'];
                $_data['roomId'] = $post['venueId'];
                $_data['venue'] = $data['venueName']['venue'];
                $_data['desigId'] = $post['designation'];
                $_data['designation'] = $data['desigName']['desig'];
                $_data['depttId'] = $post['depttId'];
                $_data['depttName'] = $data['dName']['depttName'];
                $_data['empName'] = $post['empName'];
                $_data['addedOn'] = date("Y-m-d H:i:s");
                $_data['addedBy'] = $data['id'];
                $oEmployeeTestDutyModel->insert($_data);
            }
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $data['slotId'] = $post['slotId'];
                $data['venueId'] = $post['venueId'];
                $data['testCity'] = $post['testCity'];
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');

                $oRoomsModel = new \models\RoomsModel();
                $data['venueName'] = $oRoomsModel->findByPK($data['venueId'], 'venue');

                $oTestScheduleModel = new \models\TestScheduleModel();
                $data['slots'] = $oTestScheduleModel->byOfferId($post['offerId'], $post['testCity']);
                $data['slotDetail'] = $oTestScheduleModel->getDetailBySlotId($post['offerId'], $post['slotId']);

                $oRoomsAllocationModel = new \models\RoomsAllocationModel();
                $data['venues'] = $oRoomsAllocationModel->getAllottedRoomsbySlotId($post['slotId']);

                $oDesignationModel = new \models\DesignationModel();
                $data['desigs'] = $oDesignationModel->getSortedDesignation();

                $oEmployeeTestDutyModel = new \models\EmployeeTestDutyModel();
                $data['empDetail'] = $oEmployeeTestDutyModel->findByOfferIdAndSlotIdAndVenueId($post['offerId'], $post['slotId'], $post['venueId'], $post['testCity']);
//                var_dump($data['empDetail']);exit;
                $oDepttModel = new \models\DepttModel();
                $data['deptts'] = $oDepttModel->getSortedDepartments();
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        $this->render('employeeTestDuty', $data);
    }

    public function EmployeeTestDutyMultiAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['id'] = $this->state()->get('depttUserInfo')['id'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $data['slotId'] = $post['slotId'];
                $data['venueId'] = $post['venueId'];
                $data['testCity'] = $post['testCity'];
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');

                $oTestScheduleModel = new \models\TestScheduleModel();
                $data['slots'] = $oTestScheduleModel->byOfferId($post['offerId'], $post['testCity']);
                $data['slotDetail'] = $oTestScheduleModel->getDetailBySlotId($post['offerId'], $post['slotId']);

                $oRoomsAllocationModel = new \models\RoomsAllocationModel();
                $data['venues'] = $oRoomsAllocationModel->getAllottedRoomsbySlotId($post['slotId']);

                $oDesignationModel = new \models\DesignationModel();
                $data['desigs'] = $oDesignationModel->getSortedDesignation();

                $oEmployeeTestDutyModel = new \models\EmployeeTestDutyModel();
                $data['empDetail'] = $oEmployeeTestDutyModel->findByOfferIdAndSlotNo($post['offerId'], $data['slotDetail']['slotNo'], $post['testCity']);
//                echo "<pre>"; var_dump($data['empDetail']);exit;
                $oDepttModel = new \models\DepttModel();
                $data['deptts'] = $oDepttModel->getSortedDepartments();
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        $this->render('employeeTestDutyMulti', $data);
    }

    public function AssignRollNumberAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();

            $data['offerId'] = $post['offerId'];
            if (!empty($post['offerId'])) {
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oMajorsTestScheduleModel = new \models\MajorsTestScheduleModel();
                $data['majors'] = $oMajorsTestScheduleModel->getMajorsByOfferIdWithAllStatistics($post['offerId']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('assignRollNumber', $data);
    }

    public function AssignRollNumberMultiAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            if (!empty($post['offerId'])) {
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                if (!empty($post['slotNo']) && !empty($post['testCity'])) {
                    $data['slotNo'] = $post['slotNo'];
                    $data['testCity'] = $post['testCity'];
                    $oTestScheduleModel = new \models\TestScheduleModel();
                    $data['slots'] = $oTestScheduleModel->byOfferIdAndCityId($post['offerId'], $post['testCity']);
                    $oMajorsTestScheduleModel = new \models\MajorsTestScheduleModel();
                    $data['majors'] = $oMajorsTestScheduleModel->getMajorsByOfferIdWithAllStatisticsMulti($post['slotNo'], $post['testCity']);
                    $oMetaDataModel = new \models\MetaDataModel();
                    $data['cityData'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $post['testCity']);
                }
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        if (!empty($post['admissionYear'])) {

            $oMetaDataModel = new \models\MetaDataModel();
            $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        }
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('assignRollNumberMulti', $data);
    }

    public function GATAwardListAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $oMetaDataModel = new \models\MetaDataModel();
            if (!empty($post['offerId']) && !empty($post['testCity'])) {

                $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $post['testCity']);
                $data['testCity'] = $post['testCity'];

                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');

                $oMajorsTestScheduleModel = new \models\MajorsTestScheduleModel();
                $data['majors'] = $oMajorsTestScheduleModel->getMajorsByOfferIdWithAllStatistics($post['offerId'], $post['testCity']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        $this->render('GATAwardList', $data);
    }

    public function TestResultEntryAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['cityId'] = $post['testCity'];
            if (!empty($post['offerId']) && !empty($post['majorId']) && !empty($post['testCity'])) {
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode, className, compTotal, subTotal, testTotal, testPassPer, studyLevel');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorsByOfferId($post['offerId']);
                $data['majorName'] = $oMajorsModel->getMajorPrintitleByOfferIdAndMajorId($post['offerId'], $post['majorId']);

                $oMajorsTestScheduleModel = new \models\MajorsTestScheduleModel();
                $data['slotDetail'] = $oMajorsTestScheduleModel->getScheduleByOfferIdAndMajorIdAndCityId($post['offerId'], $post['majorId'], $post['testCity']);
//                print_r($data['slotDetail']);exit;
                $oTestSchedule = new \models\TestScheduleModel();
                $offerIds = $oTestSchedule->getOfferIdsBySlotId($data['slotDetail']['slotId']);
                $oMetaDataModel = new \models\MetaDataModel();
                $data['testCentre'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $post['testCity']);

                $oGATResultModel = new \models\gatResultModel();
                $data['applications'] = $oGATResultModel->applicantsByOfferIdAndByMajorId($offerIds, $post['majorId'], $post['testCity']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        $this->render('testResultEntry', $data);
    }

    public function AssignVenueAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            if (!empty($post['offerId'])) {
                $data['testCity'] = $post['testCity'];
                $oMetaDataModel = new \models\MetaDataModel();
                $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $post['testCity']);
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oTestScheduleModel = new \models\TestScheduleModel();
                $data['slots'] = $oTestScheduleModel->statisticsByOfferIdAndSlots($post['offerId'], $post['testCity']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);

        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('assignVenue', $data);
    }

    public function AssignVenueMultiAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            if (!empty($post['offerId'])) {
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oTestScheduleModel = new \models\TestScheduleModel();
                $data['slots'] = $oTestScheduleModel->statisticsByOfferIdAndSlots($post['offerId']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('assignVenueMulti', $data);
    }

    public function GATSlotWiseReportsAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            if (!empty($post['offerId'])) {
                $data['testCity'] = $post['testCity'];
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oMetaDataModel = new \models\MetaDataModel();
                $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $post['testCity']);
                $oTestScheduleModel = new \models\TestScheduleModel();
                $data['slots'] = $oTestScheduleModel->statisticsByOfferIdAndSlots($post['offerId'], $post['testCity']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }

        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('GATSlotWiseReports', $data);
    }

    public function LaunchGATSlipSlotWiseAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->post()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            if (!empty($post['offerId'])) {
                $data['testCity'] = $post['testCity'];
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oMetaDataModel = new \models\MetaDataModel();
                $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $post['testCity']);
                $oTestScheduleModel = new \models\TestScheduleModel();
                $data['slots'] = $oTestScheduleModel->statisticsByOfferIdAndSlots($post['offerId'], $post['testCity']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('launchGATSlipSlotWise', $data);
    }

    public function GATApplicantsAdminAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['majorId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
//            print_r($post);
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $oAdmissionOffer = new \models\AdmissionOfferModel();
                $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $oBaseClass = new \models\BaseClassModel();
                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode']);
                $data['majorId'] = $post['majorId'];
            }
            $data['baseId'] = $post['admissionBase'];

            if (!empty($data['offerId'])) {
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['applications'] = $oApplicationsModel->allByFilterPaid($data['offerId'], $data['baseId'], $data['majorId']);
                $data['total'] = sizeof($data['applications']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
//        print_r($data); exit;
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('GATApplicantsAdmin', $data);
    }
}
