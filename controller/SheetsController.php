<?php
require_once "model/SheetsModel.php";
require_once "Classes/PHPExcel.php";


class SheetsController
{
    public $model;
    public $sheets;

    public function __construct()
    {
        $this->model = new SheetsModel();
    }

    public function execute($file)
    {
        //view sheets list
        $arr = $this->model->getSheets($file);
        // print_r($arr);
        if ($_SESSION['sheets']) {
            foreach ($arr as $a) {
                array_push($_SESSION['sheets'], $a);
            }
            $sheets = $_SESSION['sheets'];
        } else {
            $sheets = $arr;
        }
        $_SESSION['sheets'] = $this->model->callAPI($sheets);
        //render view sheet
        require_once "view/renderSheets.php";
    }

    public function export()
    {
        $this->model->writeSheets();
        require_once "view/renderSheets.php";
    }

    public function add($origin, $destination, $prices)
    {
        $sheets = $this->model->addRow($origin, $destination, $prices);
        $sheets = $this->model->callAPI($sheets);
        $_SESSION['sheets'] = $sheets;
        require_once "view/renderSheets.php";
    }

    public function delete($no)
    {
        $sheets = $this->model->deteleRow($no);
        require_once "view/renderSheets.php";
    }
}