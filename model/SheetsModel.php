<?php
require_once "model/Sheets.php";
require_once 'Classes/PHPExcel.php';

class SheetsModel
{
    public function getSheets($file)
    {
        //Tiến hành xác thực file
        $objFile = PHPExcel_IOFactory::identify($file);
        $objData = PHPExcel_IOFactory::createReader($objFile);

        //Chỉ đọc dữ liệu
        $objData->setReadDataOnly(true);

        // Load dữ liệu sang dạng đối tượng
        $objPHPExcel = $objData->load($file);

        //Chọn trang cần truy xuất
        $sheet = $objPHPExcel->setActiveSheetIndex(0);

        //Lấy ra số dòng cuối cùng
        $Totalrow = $sheet->getHighestRow();
        //Lấy ra tên cột cuối cùng
        $LastColumn = $sheet->getHighestColumn();

        //Chuyển đổi tên cột đó về vị trí thứ, VD: C là 3,D là 4
        $TotalCol = PHPExcel_Cell::columnIndexFromString($LastColumn);

        //Tạo mảng chứa dữ liệu
        $arr = [];

        //Tiến hành lặp qua từng ô dữ liệu
        //----Lặp dòng, Vì dòng đầu là tiêu đề cột nên chúng ta sẽ lặp giá trị từ dòng 2
        for ($i = 2; $i <= $Totalrow; $i++) {
            //----Lặp cột
            for ($j = 0; $j < $TotalCol; $j++) {
                // Tiến hành lấy giá trị của từng ô đổ vào mảng
                $arr[$i - 2][$j] = $sheet->getCellByColumnAndRow($j, $i)->getValue();;
            }
        }
        // print_r($arr);
        $sheets = [];
        foreach ($arr as $a) {
            $s = new Sheets($a);
            array_push($sheets, $s);
        }
        return $sheets;
    }

    public function callAPI($sheets)
    {
        $keyAPI = $_ENV['API'];
        $count = 0;
        foreach ($sheets as $s) {
            if ($s->status != 'OK') {
                $origin = $s->origin;    //Get origin from input
                $destination = $s->destination;     //Get destination from input

                //Encode inputs into url and call API
                $distance_data = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?&origins=' . urlencode($origin) . '&destinations=' . urlencode($destination) . '&key=' . urlencode($keyAPI));
                $distance_arr = json_decode($distance_data); // Decode json
                // Get the elements as array
                $elements = $distance_arr->rows[0]->elements;
                $s->distance = $elements[0]->distance->text;
                $s->message = $elements[0]->status;
                $s->status = $distance_arr->status;
                $s->googleOrigin = implode($distance_arr->origin_addresses);
                $s->googleDestination = implode($distance_arr->destination_addresses);
            }

            if ($s->price == NULL) {
                $s->price = 10000;
            }
            $distance = str_replace(',', '', $s->distance);
            $s->total = $s->price * (float)$distance;
            $s->no = $count;
            $count++;
        }
        return $sheets;
    }

    public function writeSheets()
    {
        $header = array_keys((array)$_SESSION['sheets'][0]);
        $f = fopen('original.csv', 'w');
        fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($f, $header);
        // loop over the input array
        foreach ($_SESSION['sheets'] as $line) {
            fputcsv($f, (array)$line);
        }
        fclose($f);
        if (file_exists('original.csv')) {
            // tell the browser it's going to be a csv file
            header('Content-Type: application/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="result.csv";');
            ob_end_clean();
            readfile('original.csv');
            exit;
        }
    }

    public function addRow($origin, $destination, $prices)
    {
        $s = new Sheets([NULL, $origin, $destination, NULL, $prices, NULL, NULL, NULL, NULL, NULL]);
        if (count($_SESSION['sheets']) == 0) {
            $_SESSION['sheets'] = [];
        }
        array_push($_SESSION['sheets'], $s);
        return $_SESSION['sheets'];
    }

    public function deteleRow($no)
    {
        // print_r($sheets);
        foreach ($_SESSION['sheets'] as $key => $value) {
            if ($value->no == $no) {
                unset($_SESSION['sheets'][$key]);
            }
        }
        return $_SESSION['sheets'];
    }
}