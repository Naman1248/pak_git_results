<?php

/**
 * Description of Common
 *
 * @author SystemAnalyst
 */

namespace helpers;

class Common {

    public static function getTotalMarks($key) {
        $totalMarks = ['test' => 30,
            'interview' => 20
        ];
        return $totalMarks[$key];
    }

    public static function arrayToCsvDownload($array, $filename = "export.csv", $delimiter = ",") {
        // open raw memory as file so no temp files needed, you might run out of memory though
        $f = fopen('php://memory', 'w');
        // loop over the input array
        foreach ($array as $line) {
            // generate csv lines from the inner arrays
            fputcsv($f, $line, $delimiter);
//            fputcsv($f, $line, $delimiter);
        }
        // reset the file pointer to the start of the file
        fseek($f, 0);
        // tell the browser it's going to be a csv file
        header('Content-Type: application/csv');
        // tell the browser we want to save it instead of displaying it
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        // make php send the generated csv lines to the browser
        fpassthru($f);
    }

    public static function formatCnic($_cnic) {
        $cnic = str_split(str_replace('-', '', $_cnic));
        $newCnic = '';
        foreach ($cnic as $key => $value) {
            $newCnic .= $value;
            if ($key == 4 || $key == 11) {
                $newCnic .= '-';
            }
        }
        return $newCnic;
    }

    public static function formatPhone($_ph) {
        $ph = str_split(str_replace('-', '', $_ph));
        $newPh = '';
        foreach ($ph as $key => $value) {
            $newPh .= $value;
            if ($key == 3) {
                $newPh .= '-';
            }
        }
        return $newPh;
    }

    public static function downloadCSV($data, $file, $headings) {
        $arr[] = array_values($headings);
        foreach ($data as $row) {
            $row['cnic'] = static::formatCnic($row['cnic']);
            if (!empty($row['ph1'])) {
                $row['ph1'] = static::formatPhone($row['ph1']);
            }
            if (!empty($row['dob'])) {
                $row['dob'] = date('d-m-Y', strtotime($row['dob']));
            }
            $arr[] = $row;
        }
//        echo '<pre>';
//        print_r($arr);exit;
        $filename = $file . ".csv";
        $filename = preg_replace('~[\\n\s]~', '_', $filename);
        static::arrayToCsvDownload($arr, $filename);
    }

    public static function downloadCSV2($data, $file, $headings) {

        $filename = $file . ".csv";
        $delimiter = ",";
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $f = fopen('php://output', 'w');

        //fputcsv($f, array_values($headings), $delimiter);
        $contents = implode('\t', array_values($headings)) . '\n';
        foreach ($data as $line) {
            $contents .= implode('\t', $line) . '\n';
            fputcsv($f, $line, $delimiter);
        }
    }

    public static function sumOfArray($arr, $key) {
        $total = 0;
        foreach ($arr as $k => $v) {
            $total += $v[$key];
        }
        return $total;
    }

    public static function numberToWords($num) {
        $ones = array(
            0 => "ZERO",
            1 => "One",
            2 => "Two",
            3 => "Three",
            4 => "Four",
            5 => "Five",
            6 => "Six",
            7 => "Seven",
            8 => "Eight",
            9 => "Nine",
            10 => "Ten",
            11 => "Eleven",
            12 => "Twelve",
            13 => "Thirteen",
            14 => "Fourteen",
            15 => "Fifteen",
            16 => "Sixteen",
            17 => "Seventeen",
            18 => "Eighteen",
            19 => "Nineteen",
            "014" => "Fourteen"
        );
        $tens = array(
            0 => "ZERO",
            1 => "Ten",
            2 => "Twenty",
            3 => "Thirty",
            4 => "Forty",
            5 => "Fifty",
            6 => "Sixty",
            7 => "Seventy",
            8 => "Eighty",
            9 => "Ninety"
        );
        $hundreds = array(
            "Hundred",
            "Thousand",
            "Million",
            "Billion",
            "Trillion",
            "Quardrillion"
        ); /* limit t quadrillion */
        $num = number_format($num, 2, ".", ",");
        $num_arr = explode(".", $num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(",", $wholenum));
        krsort($whole_arr, 1);
        $rettxt = "";
        foreach ($whole_arr as $key => $i) {

            while (substr($i, 0, 1) == "0")
                $i = substr($i, 1, 5);
            if ($i < 20) {
                /* echo "getting:".$i; */
                $rettxt .= $ones[$i];
            } elseif ($i < 100) {
                if (substr($i, 0, 1) != "0")
                    $rettxt .= $tens[substr($i, 0, 1)];
                if (substr($i, 1, 1) != "0")
                    $rettxt .= " " . $ones[substr($i, 1, 1)];
            } else {
                if (substr($i, 0, 1) != "0")
                    $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
                if (substr($i, 1, 1) != "0")
                    $rettxt .= " " . $tens[substr($i, 1, 1)];
                if (substr($i, 2, 1) != "0")
                    $rettxt .= " " . $ones[substr($i, 2, 1)];
            }
            if ($key > 0) {
                $rettxt .= " " . $hundreds[$key] . " ";
            }
        }
        if ($decnum > 0) {
            $rettxt .= " and ";
            if ($decnum < 20) {
                $rettxt .= $ones[$decnum];
            } elseif ($decnum < 100) {
                $rettxt .= $tens[substr($decnum, 0, 1)];
                $rettxt .= " " . $ones[substr($decnum, 1, 1)];
            }
        }
        return $rettxt . " Only";
    }

    public static function yearList() {
        return [2025, 2024, 2023, 2022, 2021, 2020, 2019, 2018, 2017];
    }
    public static function yearListVC() {
        return [2025, 2024];
    }
    public static function currentYear() {
        return [2025,2024];
    }

    public static function controllerList() {
        return ['ADMIN', 'Dashboard', 'Merit List', 'GAT', 'UGT Result', 'UGT Plan', 'Seating Plan', 'SAO'];
    }

    public static function currentYearList() {
        return [2023];
    }

    public static function yearListAdmin() {
        return [2023, 2022, 2021, 2020, 2019, 2018, 2017];
    }

    public static function generateBucket() {
        return rand(10, 99);
    }

    function generateToken($size = 64, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        if ($size < 1) {
            throw new \RangeException("Size must be a positive integer");
        }
        $tokens = [];
        $max = mb_strlen($characters, '8bit') - 1;
        for ($m = 0; $m < $size; ++$m) {
            $tokens [] = $characters[random_int(0, $max)];
        }
        return implode('', $tokens);
    }

//    public static function getClasses() {
//        return ['Select anOption', 'Secondary School',
//            'Higher Secondary School',
//            'Bachelors (14 Years)',
//            'Masters',
//            'Bachelors Honors (16 Years)',
//            'MS/M.Phil.',
//            'UET Test',
//            'DAE'
//        ];
//    }

    public static function getExamLevelByPreReq($preReq) {
        $preExam = explode(',', $preReq);
        $arr = ['Select an Option'];
        $classes = static::getExamLevel();
        foreach ($classes as $key => $class) {
            if (in_array($key, $preExam)) {
                $arr[$key] = $class;
            }
        }
        return $arr;
    }

    public static function getExamLevelByTwoPreReq($preReq, $preReq1) {
        $preExam0 = explode(',', $preReq);
        $preExam1 = explode(',', $preReq1);
        $preExam = array_unique(array_merge($preExam0, $preExam1));
        $arr = ['Select an Option'];
        $classes = static::getExamLevel();
        foreach ($classes as $key => $class) {
            if (in_array($key, $preExam)) {
                $arr[$key] = $class;
            }
        }
        return $arr;
    }

    public static function getExamLevel() {
        return ['Select an Option',
            'Secondary School (Matric)',
            'Higher Secondary School (Inter)',
            'Bachelors (14 Years)',
            'Masters Or Bachelors Honors (16 Years)',
            'MS/M.Phil.',
            'Engineering College Admission Test',
            'DAE',
            'LAT',
//            'NTS/GCU-GAT'
            'NTS/ETS-GAT'
        ];
    }

    public static function getClasses() {
        return ['Select an Option',
            'Secondary School (Matric / O-Level)',
            'Higher Secondary School (Inter / A-Level)',
            'Bachelors (14 Years)',
            'Masters Or Bachelors Honors (16 Years)',
            'MS/M.Phil.',
            'Engineering College Admission Test',
            'DAE',
            'LAT',
            'NTS/GCU-GAT'
        ];
    }

    public static function getClassById($Id) {

        return self::getClasses()[$Id];
    }

    public static function getStudyLevels() {
        return ['Certificate',
            '',
            'Intermediate',
            'Undergraduate',
            'Post Graduate Diploma',
            'Graduate',
            'MS/M.Phil.',
            'PhD'
        ];
    }

    public static function getStudyLevelById($Id) {
        if ($Id == 100) {

            return 'GAT';
        }
        return self::getStudyLevels()[$Id];
    }

    public static function getBaseTypeDetail() {
        return [1 => 'Inter School / Board',
            2 => 'District / Divisional Level',
            3 => 'Colour Holder or member of Provincial / National Teams'
        ];
    }

    public static function getBaseTypeDetailById($Id) {
        return self::getBaseTypeDetail()[$Id];
    }

    public static function checkValidMarks($obt, $tot) {
        if ($obt > $tot) {
            $this->printAndDieJsonResponse(false, ['msg' => 'Obtained Marks is Greater Than Total Marks.']);
        }
    }

    public static function getClassNameById($id) {
        $arr = [
            1 => 'FA/FSC/ICS/ICOM/ARTS/GERNEARL SCIENCE',
            2 => 'BCOM (Self Supporting)',
            8 => 'PHD',
            109 => 'BSCS Admissions (Self Supporting in Morning) - Spring',
            110 => 'BSCS (Self Supporting in Evening) - Spring',
            90 => 'CERTIFICATE IN ARABIC',
            61 => 'B.A/B.SC HONORS (Regular) - Spring',
            65 => 'B.A/B.SC HONORS (Self Supporting) - Spring',
            11 => 'BSCS HONORS (Self Supporting Morning)',
            13 => 'MBA',
            19 => 'EMBA',
            26 => 'CERTIFICATE IN ARCHIVE STUDIES',
            4 => 'MS / PHIL',
            50 => 'MS / PHIL (Morning)',
            20 => 'BSCS HONORS (Self Supporting Evening)',
            21 => 'B.A/B.SC HONORS (Regular)',
            27 => 'B.SC. ELECTRICAL ENGINEERING',
            36 => 'BFA GRAPHIC DESIGN (Regular)',
            40 => 'B.A/B.SC HONORS (Self Supporting)',
            41 => 'CERTIFICATE IN TURKISH',
            42 => 'CERTIFICATE IN FRENCH',
            43 => 'CERTIFICATE IN PERSIAN',
            44 => 'CERTIFICATE IN PUNJABI',
            46 => 'CERTIFICATE IN URDU',
            47 => 'CERTIFICATE IN ENGLISH',
            79 => 'CERTIFICATE IN CHINESE LANGUAGE',
            94 => 'CERTIFICATE IN GERMAN',
            48 => 'BFA GRAPHIC DESIGN (Self Supporting)',
            49 => 'BFA Painting (Regular)',
            58 => 'LLB Regular (Regular)',
            81 => 'CERTIFICATE IN FORENSIC SCIENCE',
            78 => 'POST GRADUATE DIPLOMA IN CHILD AND ADULT PSYCHOTHERAPY',
            100 => 'GCU GAT',
            111 => 'F.SC. / ICS Self Supporting (Second Shift)',
            112 => 'PGD in Environmental and Social Impact Assessment-II',
            113 => 'PGD in Disaster Management',
            114 => 'PGD in Tourism and Recreation',
            115 => 'Certificate in Environmental Impact Assessment',
            117 => 'B.Ed (Self Supporting) - II',
            118 => 'B.Ed (Self Supporting)',
            119 => 'Chinese Language Course(Only For GCU Students)',
            150 => 'CERTIFICATE IN HINDI',
            210 => '',
            360 => '',
            400 => '',
            480 => '',
            490 => '',
            121 => 'BS Admission Test Fall - I',
            64 => 'PGD in GIS and Remote Sensing',
            101 => 'DIPLOMA IN FORGIVENESS Psychology and Practice',
            59 => 'CERTIFICATE OF FORGIVENESS EDUCATION'
        ];
        if (!empty($arr[$id])) {

            return $arr[$id];
        }

        return $id;
    }

}
