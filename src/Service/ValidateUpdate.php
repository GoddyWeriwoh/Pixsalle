<?php

declare(strict_types=1);

namespace Salle\PixSalle\Service;

class ValidateUpdate
{
    public function __construct()
    {
    }

    public function valPhone(string $in): string
    {
        if (strcmp($in, "") === 0){
            return "";
        }
        else if (strlen($in) != 9){
            return "Phone number must be 9 digits long";
        }
        else if($in[0] != '6') {
            return "Enter a valid spanish number";
        }
        return "";
    }

    public function valImg($files): string
    {
        $img = $files["photo"]["tmp_name"];
        if (empty($img)){
            return "";
        }
        $parts = pathinfo($files["photo"]["name"]);
        $exten = $parts["extension"];
        $size = getimagesize($img);
        if($files["photo"]["size"] >= 1000000) {
            return "Image bigger than 1Mb";
        }
        else if(!(strcmp($exten, "jpg") === 0 || strcmp($exten, "png") === 0)) {
            echo $exten;
            return "Image must be of type jpg or png";
        }
        else if($size[0] > 500 || $size[1] > 500) {
            return "Image must be 500x500 max";
        }
        return "";
    }
}