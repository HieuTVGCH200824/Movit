<?php
require_once 'Classes/PHPExcel.php';
require_once realpath(__DIR__ . "/vendor/autoload.php");

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$keyAPI = getenv("DistanceAPI");
//Đường dẫn file        

$file = 'data.csv';
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
$data = [];

//Tiến hành lặp qua từng ô dữ liệu
//----Lặp dòng, Vì dòng đầu là tiêu đề cột nên chúng ta sẽ lặp giá trị từ dòng 2
for ($i = 2; $i <= $Totalrow; $i++) {
    //----Lặp cột
    for ($j = 0; $j < $TotalCol; $j++) {
        // Tiến hành lấy giá trị của từng ô đổ vào mảng
        $data[$i - 2][$j] = $sheet->getCellByColumnAndRow($j, $i)->getValue();;
    }
}

for ($i = 0; $i < count($data); $i++) {
    if ($data[$i][3] == NULL) {
        $origin = $data[$i][1];    //Get origin from input
        $destination = $data[$i][2];     //Get destination from input

        //Encode inputs into url and call API
        $distance_data = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?&origins=' . urlencode($origin) . '&destinations=' . urlencode($destination) . '&key=' . urlencode($keyAPI));
        $distance_arr = json_decode($distance_data); // Decode json
        if ($distance_arr->status == 'OK') {
            $destination_addresses = $distance_arr->destination_addresses[0];
            $origin_addresses = $distance_arr->origin_addresses[0];
        } else {
            echo "<p>The request was Invalid</p>";
            exit();
        }
        if ($origin_addresses == "" or $destination_addresses == "") {
            echo "<p>Destination or origin address not found</p>";
            exit();
        }
        // Get the elements as array
        $elements = $distance_arr->rows[0]->elements;
        $distance = $elements[0]->distance->text;
        $duration = $elements[0]->duration->text;
        $data[$i][3] = $distance;
    }
}

//open file ready to write to
//opens the file or creates if it doesn't already exist
$fo = fopen('data_result.csv', 'w+');

//loop through array
foreach ($data as $lines) {
    //push array values to line in the csv file
    fputcsv($fo, $lines);
}

//close file
fclose($fo);
//Hiển thị mảng dữ liệu
echo '<pre>';
var_dump($data);
