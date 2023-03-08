<?php

namespace App\Exception;

use JetBrains\PhpStorm\ArrayShape;

class managementError
{
#[ArrayShape(['content' => "string", 'exception' => "array"])]
public function responseError($text, $errorCode){
    return [
        'content' => 'Error.',
        'exception'=> [
            'message' => $text,
            'code' => $errorCode,
        ],
    ];
}
}