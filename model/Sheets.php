<?php
class Sheets
{
    public $no;
    public $origin;
    public $destination;
    public $distance;
    public $price;
    public $total;
    public $googleOrigin;
    public $googleDestination;
    public $status;
    public $message;


    public function __construct($sheet)
    {
        $this->no = $sheet[0];
        $this->origin = $sheet[1];
        $this->destination = $sheet[2];
        $this->distance = $sheet[3];
        $this->price = $sheet[4];
        $this->total = $sheet[5];
        $this->googleOrigin = $sheet[6];
        $this->googleDestination = $sheet[7];
        $this->status = $sheet[8];
        $this->message = $sheet[9];
    }
}