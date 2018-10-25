<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 25.10.2018
 * Time: 2:31
 */

namespace App\Services;


class Progress
{
    public function __construct()
    {

    }

    public function start(){
        if(!is_dir($this->folder_path() )){
            mkdir($this->folder_path());
        }
        if (!file_exists($this->file_path())) {
            fopen($this->file_path(), 'w');
        }
    }
    public function stop()
    {
        if ($this->is_parsing()) {
            unlink($this->file_path());
        }
    }

    public function is_parsing()
    {
        return file_exists($this->file_path());
    }

    public function file_path()
    {
        return $this->folder_path() . '/' . 'progress';
    }
    public function folder_path()
    {
        return storage_path('parser');
    }
}