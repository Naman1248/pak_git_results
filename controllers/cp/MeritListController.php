<?php

/*
 * Description of MeritListController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp;

class MeritListController extends StateController {

    public function resetMeritListAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $this->render('resetMeritList', $data);
    }

    public function MSAggregateAction() {
        if ($this->isPost()) {
            $post = $this->post()->all();
//            print_r($post);exit;
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['admissionBase'];
        }
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
            $oMajorsModel = new \models\MajorsModel();
            $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
            $oUgtResultModel = new \models\UGTResultModel();
            $oUgtResultModel->calculateMSAggregate($data['offerId'], $data['majorId'], $data['baseId']);
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];

        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('MSAggregate', $data);
    }

    public function UGTAggregateAction() {
        if ($this->isPost()) {
            $post = $this->post()->all();
//            print_r($post);exit;
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['admissionBase'];
        }
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
            $oMajorsModel = new \models\MajorsModel();
            $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
            $oUgtResultModel = new \models\UGTResultModel();
            $oUgtResultModel->UGTAggregate($data['offerId'], $data['majorId'], $data['baseId']);
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];

        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('UGTAggregate', $data);
    }

    public function baseWiseStatisticsAction() {
        $get = $this->post()->all();
        $post = $this->post()->all();
        $data['offerId'] = $get['offerId'] ?? '';
        $data['baseId'] = $get['baseId'] ?? '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];
        }
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
            $data['baseName'] = $oBaseClass->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
            $oMeritListInfoModel = new \models\MeritListInfoModel();
            $data['applications'] = $oMeritListInfoModel->baseWiseMeritListsInfo($data['offerId'], $data['baseId']);
            $data['total'] = sizeof($data['applications']);
        } else {
            $post['admissionYear'] = $post['admissionYear'];
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('baseWiseStatistics', $data);
    }

    public function infoAdminAction() {
        $get = $this->get()->all();
        $post = $this->post()->all();
        $data['offerId'] = $get['offerId'] ?? '';
        $data['baseId'] = $get['baseId'] ?? '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];
        }
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
            $oMeritListInfoModel = new \models\MeritListInfoModel();
            $data['applications'] = $oMeritListInfoModel->displayLockedMeritLists($data['offerId'], $data['baseId']);
            $oMajorsModel = new \models\MajorsModel();
            foreach ($data['applications'] as $key => $row) {
                $data['applications'][$key]['majorName'] = $oMajorsModel->getMajorNameByOfferIdAndMajorId($row['offerId'], $row['majId']);
            }
//            print_r($data['application']);exit;
            $data['total'] = sizeof($data['applications']);
        } else {
            $post['admissionYear'] = $post['admissionYear'];
        }

        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('infoAdmin', $data);
    }
    public function interviewPanelAction() {
        $get = $this->get()->all();
        $post = $this->post()->all();
        $data['offerId'] = $get['offerId'] ?? '';
        $data['baseId'] = $get['baseId'] ?? '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];
        }
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className, year');
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
            $oInterviewPanelModel = new \models\InterviewPanelModel();
            $data['applications'] = $oInterviewPanelModel->displayInterviewPanel($data['offerId'], $data['baseId']);
            $oMajorsModel = new \models\MajorsModel();
            foreach ($data['applications'] as $key => $row) {
                $data['applications'][$key]['majorName'] = $oMajorsModel->getMajorNameByOfferIdAndMajorId($row['offerId'], $row['majId']);
            }
//            print_r($data['application']);exit;
            $data['total'] = sizeof($data['applications']);
        } else {
            $post['admissionYear'] = $post['admissionYear'];
        }

        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('interviewPanel', $data);
    }

    public function infoChallanAction() {
        $get = $this->get()->all();
        $post = $this->post()->all();
        $data['offerId'] = $get['offerId'] ?? '';
        $data['baseId'] = $get['baseId'] ?? '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];
        }
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
            $oMeritListInfoModel = new \models\MeritListInfoModel();
            $data['applications'] = $oMeritListInfoModel->displayLockedMeritLists($data['offerId'], $data['baseId']);
            $oMajorsModel = new \models\MajorsModel();
            foreach ($data['applications'] as $key => $row) {
                $data['applications'][$key]['majorName'] = $oMajorsModel->getMajorNameByOfferIdAndMajorId($row['offerId'], $row['majId']);
            }
//            print_r($data['application']);exit;
            $data['total'] = sizeof($data['applications']);
        } else {
            $post['admissionYear'] = $post['admissionYear'];
        }

        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('infoChallan', $data);
    }

    public function infoAdminAllAction() {
        $get = $this->get()->all();
        $post = $this->post()->all();
        $data['offerId'] = $get['offerId'] ?? '';
        $data['baseId'] = $get['baseId'] ?? '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];
        }
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
            $oMeritListInfoModel = new \models\MeritListInfoModel();
            $data['applications'] = $oMeritListInfoModel->meritListInfoByOfferIdAndBaseId($data['offerId'], $data['baseId']);
            $oMajorsModel = new \models\MajorsModel();
            foreach ($data['applications'] as $key => $row) {
                $data['applications'][$key]['majorName'] = $oMajorsModel->getMajorNameByOfferIdAndMajorId($row['offerId'], $row['majId']);
            }
            $data['total'] = sizeof($data['applications']);
        } else {
            $post['admissionYear'] = $post['admissionYear'];
        }

        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('infoAdminAll', $data);
    }

    public function baseWiseStatisticsPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className, year');
            $oBaseClass = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClass->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['baseId']);
            $oMeritListInfoModel = new \models\MeritListInfoModel();
            $data['applications'] = $oMeritListInfoModel->baseWiseMeritListsInfo($get['offerId'], $get['baseId']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('baseWiseStatisticsPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore </td></td></table>");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function MSMeritListPDFAction() {
        $get = $this->get()->all();
        $data['id'] = $this->state()->get('depttUserInfo')['id'];
        if (!empty($get['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className, studyLevel, year');
            $oUGTResultModel = new \models\UGTResultModel();
            $data['applications'] = $oUGTResultModel->meritListDetail($get['offerId'], $get['majorId'], $get['baseId'], $get['meritList']);
//            echo "<pre>";
//            print_r($data['applications']);exit;
            $data['total'] = sizeof($data['applications']);
            $data['meritListCtgry'] = $get['meritListCtgry'];
            $data['meritList'] = $get['meritList'];
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['offerId'], $data['offerData']['cCode'], $get['majorId']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['baseId']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            if ($data['offerData']['studyLevel'] == 2 && $data['offerData']['year'] > 2023) {
                $HTML = $this->getHTML('InterMeritList', $data);
            } else {

                $HTML = $this->getHTML('MSMeritList', $data);
            }

            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> MeritList</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function MeritListAdminPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oMeritListInfoModel = new \models\MeritListInfoModel();
            $data['meritInfo'] = $oMeritListInfoModel->meritListInfoDetail($get['offerId'], $get['majorId'], $get['baseId'], $get['meritList']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Legal', 'orientation' => 'P']);
            if (empty($data['meritInfo']['dueDate']) || $data['meritInfo']['isPublished'] == 'NO') {
                $obj->setHTML('<h1>MERIT LIST NOT PUBLISHED OR DUE DATE NOT SAVED.</h1>');
                $obj->browse();
                exit;
            }
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className, year');
            $oUGTResultModel = new \models\UGTResultModel();
            $data['meritList'] = $get['meritList'];
            $data['dueDate'] = $data['meritInfo']['dueDate'];
            $data['meritListCtgry'] = $data['meritInfo']['meritListCtgry'];
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['offerId'], $data['offerData']['cCode'], $get['majorId']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['baseId']);
            $applications = $oUGTResultModel->meritListDetail($get['offerId'], $get['majorId'], $get['baseId'], $get['meritList']);
            $data['total'] = sizeof($applications);

            if ($data['offerData']['cCode'] == 1 || $data['offerData']['cCode'] == 111) {
                $obj->setFooter("<table>
            <tr><td colspan='3'>&nbsp;</td></tr>                
            <tr><td colspan='3'>&nbsp;</td></tr>                
            <tr><td colspan='3' align='right'><h3>Registrar _______________</h3></td>
            </tr>                
            <tr><td style='border-bottom:#000 solid thin;' colspan='3' align='justify'>
            <h3><u> Important Note:</u></h3>
            <br><h3>
             (i) Please refer to the interview / documents verification schedule for the necessary instructions.
            <br>
             (ii) Errors and omissions are excepted.
            <br />&nbsp;</td></tr><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</h3></td></tr></table> ");
            } else {
                $obj->setFooter("<table>
            <tr><td colspan='3'>&nbsp;</td></tr>                
            <tr><td colspan='3'>&nbsp;</td></tr>                
            <tr><td colspan='3' align='right'><h3>Registrar _______________</h3></td>
            </tr>                
            <tr><td style='border-bottom:#000 solid thin;' colspan='3' align='justify'>
            <h3><u> Important Note:</u></h3>
            <br><h3>
            1. The candidates listed above are admitted provisionally, subject to the verification of all academic credentials. The University reserves the right to withdraw the provisional admission, with or without providing any reason.
            <br>
            2. Main Campus students can obtain their fee challans from their respective Academic Departments. However, KSK Campus students should collect their fee challans from the Camp Office of Academic Departments located at the Main Campus.
            <br>
            3. Documents required for fee challan collection: (i) original admission form (ii) photographs (iii) original academic certificates (iv) one set of attested copies of academic certificates (v) attested copies of the candidate’s CNIC/B-Form and Parent’s CNIC.
            <br>
            4)  The Errors and omissions excepted.<br />&nbsp;</td></tr><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</h3></td></tr></table> ");
            }
//            1)  The Candidates of both Main Campus and KSK can collect their fee challans from their Academic department or camp office at the MAIN CAMPUS. 
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> MeritList</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $offset = 33;
//            $offset = 36;
            $chunks = array_chunk($applications, $offset);
            $iterations = count($chunks);
            $i = 0;
            if ($data['offerData']['studyLevel'] == 2 && $data['offerData']['year'] > 2023) {
                $fileName = 'MeritListAdminInter';
            } else {
                $fileName = 'MeritListAdmin';
            }
            foreach ($chunks as $row) {
                $data['applications'] = $row;
                $HTML = $this->getHTML('MeritListAdmin', $data);
//                $HTML = $this->getHTML('MeritListAdminInter', $data);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                if (++$i < $iterations){
                    $obj->addPage();
                }
            }

            //$obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }
    public function MeritListInterviewPanelPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oMeritListInfoModel = new \models\MeritListInfoModel();
            $data['meritInfo'] = $oMeritListInfoModel->meritListInfoDetail($get['offerId'], $get['majorId'], $get['baseId'], $get['meritList']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Legal', 'orientation' => 'P']);
            if (empty($data['meritInfo']['dueDate']) || $data['meritInfo']['isPublished'] == 'NO') {
                $obj->setHTML('<h1>MERIT LIST NOT PUBLISHED OR DUE DATE NOT SAVED.</h1>');
                $obj->browse();
                exit;
            }
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className, year');
            $oUGTResultModel = new \models\UGTResultModel();
            $data['meritList'] = $get['meritList'];
            $data['dueDate'] = $data['meritInfo']['dueDate'];
            $data['meritListCtgry'] = $data['meritInfo']['meritListCtgry'];
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['offerId'], $data['offerData']['cCode'], $get['majorId']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['baseId']);
            $applications = $oUGTResultModel->meritListDetailForInterviewPanel($get['offerId'], $get['majorId'], $get['baseId'], $get['meritList'], $get['startSrNo'], $get['endSrNo']);
            $data['total'] = sizeof($applications);

            if ($data['offerData']['cCode'] == 1 || $data['offerData']['cCode'] == 111) {
                $obj->setFooter("<table>
            <tr><td colspan='3'>&nbsp;</td></tr>                
            <tr><td colspan='3'>&nbsp;</td></tr>                
            <tr><td colspan='3' align='right'><h3>Registrar _______________</h3></td>
            </tr>                
            <tr><td style='border-bottom:#000 solid thin;' colspan='3' align='justify'>
            <h3><u> Important Note:</u></h3>
            <br><h3>
             (i) Please refer to the interview / documents verification schedule for the necessary instructions.
            <br>
             (ii) Errors and omissions are excepted.
            <br />&nbsp;</td></tr><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</h3></td></tr></table> ");
            } else {
                $obj->setFooter("<table>
            <tr><td colspan='3'>&nbsp;</td></tr>                
            <tr><td colspan='3'>&nbsp;</td></tr>                
            <tr><td colspan='3' align='right'><h3>Registrar _______________</h3></td>
            </tr>                
            <tr><td style='border-bottom:#000 solid thin;' colspan='3' align='justify'>
            <h3><u> Important Note:</u></h3>
            <br><h3>
            1)  The Candidates of both Main Campus and KSK can collect their fee challans from their Academic department or camp office at the MAIN CAMPUS. 
            <br>
            2)  Check list for collection of fee challans: The aforementioned candidates shall bring their (i) original admission form (ii) photographs (iii) original academic certificates (iv) attested copies of academic certificates (v) attested copies of CNIC/B-Form, Parent’s CNIC.
            <br>
            3)  The Errors and omissions excepted.<br />&nbsp;</td></tr><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</h3></td></tr></table> ");
            }
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> MeritList</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $offset = 36;
//            $offset = 36;
            $chunks = array_chunk($applications, $offset);
            $iterations = count($chunks);
            $i = 0;
            if ($data['offerData']['studyLevel'] == 2 && $data['offerData']['year'] > 2023) {
                $fileName = 'MeritListAdminInter';
            } else {
                $fileName = 'MeritListAdmin';
            }
            foreach ($chunks as $row) {
                $data['applications'] = $row;
                $HTML = $this->getHTML('MeritListAdminInter', $data);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                if (++$i < $iterations){
                    $obj->addPage();
                }
            }

            //$obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function MeritListAdminPDFWOAGGAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Legal', 'orientation' => 'P']);
            $oMeritListInfoModel = new \models\MeritListInfoModel();
            $data['meritInfo'] = $oMeritListInfoModel->meritListInfoDetail($get['offerId'], $get['majorId'], $get['baseId'], $get['meritList']);
            if (empty($data['meritInfo']['dueDate']) || $data['meritInfo']['isPublished'] == 'NO') {
                $obj->setHTML('<h1>MERIT LIST NOT PUBLISHED OR DUE DATE NOT SAVED.</h1>');
                $obj->browse();
                exit;
            }
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['offerId'], 'cCode,className');
            $oUGTResultModel = new \models\UGTResultModel();
            $data['meritList'] = $get['meritList'];
            $data['dueDate'] = $data['meritInfo']['dueDate'];
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['offerId'], $data['offerData']['cCode'], $get['majorId']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['baseId']);
            $applications = $oUGTResultModel->meritListDetail($get['offerId'], $get['majorId'], $get['baseId'], $get['meritList']);
            $data['total'] = sizeof($applications);
            $obj->setFooter("<table>
            <tr><td colspan='3'>&nbsp;</td></tr>                
            <tr><td colspan='3'>&nbsp;</td></tr>                
            <tr><td colspan='3' align='right'><h3>Registrar _______________</h3></td>
            </tr>                
            <tr><td style='border-bottom:#000 solid thin;' colspan='3' align='justify'>
            <h3><u> Important Note:</u></h3>
            <br><h3>
            1)  The above mentioned candidates are provisionally admitted subject to the verification of all the academic documents. The due date for the submission of dues is <b>" . date('d-m-Y', strtotime($data['dueDate'])) . "</b>. The candidates can collect the fee challan from the department concerned. The candidates must bring all the original documents and a set of attested photocopies at the time of issuance of fee challan.
            <br>
            2)  The Errors and omissions excepted.<br />&nbsp;</td></tr><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</h3></td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> MeritList</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $offset = 36;
            $chunks = array_chunk($applications, $offset);
            $iterations = count($chunks);
            $i = 0;
            foreach ($chunks as $row) {
                $data['applications'] = $row;
                $HTML = $this->getHTML('MeritListAdminWOAGG', $data);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                if (++$i < $iterations)
                    $obj->addPage();
            }

            //$obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function applicantTransferAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['admissionBase'];
            $data['formNo'] = $post['formNo'];
            $data['setNo'] = $post['setNo'];
            if (!empty($data['offerId'])) {
                if (empty($data['offerId']) || empty($data['majorId']) || empty($data['baseId']) || empty($data['formNo']) || empty($data['setNo'])) {
                    $data['errorMsg'] = 'Please enter all information';
                } else {
                    $oAdmissionOffer = new \models\AdmissionOfferModel();
                    $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className, shift');

                    $oApplicationsModel = new \models\ApplicationsModel();
                    $formData = $oApplicationsModel->findOneByField('formNo', $data['formNo'], 'appId');
                    $oUgtResultModel = new \models\UGTResultModel();
                    $ugtData = $oUgtResultModel->findOneByField('formNo', $data['formNo'], 'appId, meritList');
                    if (empty($formData)) {
                        $data['errorMsg'] = 'Application for this Form Does Not Exist.';
                    } elseif (empty($ugtData)) {
                        $data['errorMsg'] = 'Form Does Not Exist for Merit List.';
                    } elseif (!empty($ugtData['meritList'])) {
                        $data['errorMsg'] = 'Cannot transfer because already selected for Merit List.' . $ugtData['meritList'];
                    } elseif ($formData['majId'] != $data['majorId']) {
                        $data['errorMsg'] = 'Please Generate New Application for This Form Number From New Application Form.';
                    } else {
                        $this->db()->beginTransaction();
                        $applicationupdate = $oApplicationsModel->upsert(
                                [
                                    'offerId' => $data['offerId'],
                                    'cCode' => $data['offerData']['cCode'],
                                    'majId' => $data['majorId'],
                                    'baseId' => $data['baseId'],
                                    'formNo' => $data['formNo'],
                                    'setNo' => $data['setNo']
                                ], $formData['appId']);
                        $ugtresultupdate = $oUgtResultModel->upsert(
                                [
                                    'offerId' => $data['offerId'],
                                    'cCode' => $data['offerData']['cCode'],
                                    'majId' => $data['majorId'],
                                    'baseId' => $data['baseId'],
                                    'formNo' => $data['formNo'],
                                    'setNo' => $data['setNo'],
                                    'major' => $data['majorName'],
                                    'baseName' => $data['baseName'],
                                    'shift' => $data['offerData']['shift'],
                                    'class' => $data['offerData']['className'],
                                    'interviewObt' => NULL,
                                    'interviewResult' => NULL,
                                    'interviewAgg' => NULL,
                                    'totAgg' => NULL,
                                    'isVerified' => 'NO',
                                    'locMeritList' => 'N'
                                ], $ugtData['appId']);
                        if ($applicationupdate && $ugtresultupdate) {
                            $this->db()->commit();
                            $data['updateMsg'] = 'Applicant transferred successfully.';
                        } else {
                            $this->db()->rollback();
                            $data['errorMsg'] = 'Some error occured, please try again.';
                        }
                    }
                }
            }
        }
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
            $oMajorsModel = new \models\MajorsModel();
            $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('applicantTransfer', $data);
    }

    public function newApplicationForShiftingAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['admissionBase'];
            $data['formNo'] = $post['formNo'];
            $data['setNo'] = $post['setNo'];
            if (!empty($data['offerId'])) {
                if (empty($data['offerId']) || empty($data['majorId']) || empty($data['baseId']) || empty($data['formNo']) || empty($data['setNo'])) {
                    $data['errorMsg'] = 'Please enter all information';
                } else {
                    $oAdmissionOffer = new \models\AdmissionOfferModel();
                    $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className, shift');

                    $oApplicationsModel = new \models\ApplicationsModel();
                    $formData = $oApplicationsModel->findOneByField('formNo', $data['formNo'], '*');
//                    echo "<pre>";
//                    print_r($formData);exit;

                    $oUgtResultModel = new \models\UGTResultModel();
                    $ugtData = $oUgtResultModel->findOneByField('formNo', $data['formNo'], 'appId');
                    if (empty($formData)) {
                        $data['errorMsg'] = 'Application for this Form Does Not Exist.';
                    } elseif (empty($ugtData)) {
                        $data['errorMsg'] = 'Form Does Not Exist for Merit List.';
                    } elseif ($formData['shiftApplication'] == 1) {
                        $data['errorMsg'] = 'This Form Has Already Shifted.';
                    } elseif ($formData['majId'] == $data['majorId']) {
                        $data['errorMsg'] = 'Already Applied for This Major.';
                    } else {
                        $appId = $oApplicationsModel->upsert(
                                [
                                    'userId' => $formData['userId'],
                                    'offerId' => $data['offerId'],
                                    'cCode' => $data['offerData']['cCode'],
                                    'baseId' => $data['baseId'],
                                    'childBase' => $formData['childBase'],
                                    'baseTypeDet' => $formData['baseTypeDet'],
                                    'majId' => $data['majorId'],
                                    'picExt' => $formData['picExt'],
                                    'chalId' => $formData['chalId'],
                                    'picture' => $formData['picture'],
                                    'picBucket' => $formData['picBucket'],
                                    'setNo' => $data['setNo'],
                                    'addedOn' => date("Y-m-d H:i:s"),
                                    'depositDate' => $formData['depositDate'],
                                    'lastUpdate' => $formData['lastUpdate'],
                                    'isPaid' => $formData['isPaid'],
                                    'endDate' => $formData['endDate'],
                                    'updatedBy' => $formData['updatedBy'],
                                    'updatedOn' => $formData['updatedOn'],
                                    'version' => $formData['version'],
                                    'transactionId' => $formData['transactionId'],
                                    'branchCode' => $formData['branchCode'],
                                    'paidOn' => $formData['paidOn'],
                                    'rn' => $formData['rn'],
                                    'shiftApplication' => 1
                                ]
                        );
                        $pk = $oApplicationsModel->upsert(['formNo' => $appId . date("y")], $appId);

                        if ($pk) {
                            $appId = $oApplicationsModel->upsert(['shiftApplication' => 1], $formData['appId']);
                            $data['updateMsg'] = 'Applicant transferred successfully.';
                        } else {
                            $data['errorMsg'] = 'Some error occured, please try again.';
                        }
                    }
                }
            }
        }
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
            $oMajorsModel = new \models\MajorsModel();
            $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('newApplicationForShifting', $data);
    }

//    public function dropApplicantMeritListInfoAction() {
//        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
//        if ($this->isPost()) {
//            $post = $this->post()->all();
//            $data['offerId'] = $post['offerId'];
//            $data['majorId'] = $post['majorId'];
//            $data['baseId'] = $post['admissionBase'];
//            $data['formNo'] = $post['formNo'];
//            if (empty($data['offerId']) || empty($data['majorId']) || empty($data['baseId']) || empty($data['formNo'])) {
//                $data['errorMsg'] = 'Please enter all information';
//            } else {
//                $oAdmissionOffer = new \models\AdmissionOfferModel();
//                $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
//            }
//            $oUgtResultModel = new \models\UGTResultModel();
//            $ugtData = $oUgtResultModel->findOneByField('formNo', $data['formNo'], 'appId, meritList');
//            if (empty($ugtData)) {
//                $data['errorMsg'] = 'Form Number Does Not Exist.';
//            } elseif (empty($ugtData['meritList'])) {
//                $data['errorMsg'] = 'Form Number Does Not Exist for  any Merit List.';
//            } else {
//                $ugtresultupdate = $oUgtResultModel->dropMeritListByFormNo($ugtData['appId']);
//                if ($ugtresultupdate) {
//                    $data['updateMsg'] = 'Applicant Removed From Merit List successfully.';
//                } else {
//                    $data['errorMsg'] = 'Some error occured, please try again.';
//                }
//            }
//        }
//        if (!empty($data['offerId'])) {
//            $oAdmissionOffer = new \models\AdmissionOfferModel();
//            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
//            $oMajorsModel = new \models\MajorsModel();
//            $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
//            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);
//            $oBaseClass = new \models\BaseClassModel();
//            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
//            $oBaseClassModel = new \models\BaseClassModel();
//            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
//        } else {
//            $post['admissionYear'] = date('Y');
//        }
//
//        $data['admissionYear'] = $post['admissionYear'];
//        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
//        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
//        $data['yearList'] = \helpers\Common::yearList();
////        print_r($data);exit;
//        $this->render('dropApplicantMeritListInfo', $data);
//    }

    public function downnlaodcsvAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oUGTResultModel = new \models\UGTResultModel();
//            $fields = 'a.userId,a.formNo,u.name applicantName,u.gender,u.fatherName,u.cnic,u.dob,u.ph1,u.email,u.add1,ao.className,m.name majorName,b.name baseName,isPaid,a.offerId,a.appId,m.majId,a.baseId,a.cCode';
            $fields = 'a.userId, a.formNo, a.rollNo,a.total,a.status,a.interviewDate, a.interviewTime, a.interviewVenue, a.name applicantName,a.gender,a.fatherName, a.cnic, a.dob, a.contactNo, a.email, a.add1, '
                    . 'a.appId,a.offerId,a.majId,a.cCode,a.baseId,a.class,a.major,a.baseName, a.meritList, a.srNo';
            $data['result'] = $oUGTResultModel->meritListCSV($get['oid'], $get['mid'], $get['bid'], $get['meritId'], $fields);
            $headings = ['userId' => "User Id", 'formNo' => "Form #", 'rollNo' => "Roll No.", 'total' => "Test Marks", 'status' => "Result",
                'interviewDate' => "Interview Date", 'interviewTime' => "Interview Time", 'interviewVenue' => "interview Venue", 'applicantName' => "Applicant Name", 'gender' => "Gender",
                'fatherName' => "Father Name", 'cnic' => "CNIC", 'dob' => "Date of Birth", 'ph1' => "Contact No", 'email' => "Email", 'add1' => "Address",
                'appId' => "App Id", 'offerId' => "Offer Id", 'majId' => "Major Id", 'cCode' => "Class Code", 'baseId' => "Base Id",
                'className' => "Class", 'majorName' => "Major", 'baseName' => "Base Name", 'meritList' => 'M_List', 'srNo' => 'sr_No'
            ];
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $offerData['cCode'], $get['mid']);
//            print_r($offerData['className']);exit;
            \helpers\Common::downloadCSV($data['result'], $data['majorName']['name'], $headings);
        }
    }
}
