<?php
require_once './vendor/autoload.php';

// Creating the new document...

$template = new \PhpOffice\PhpWord\TemplateProcessor(realpath('template.docx'));

$template->setValues([

    'name'=> 'Alihaile Faarah',
    'email'=> 'alihaile2020@gmail.com',
    'address'=> 'Hodan, Kobac Appartments',
    
]);

$template->setImageValue('qrcode',
[
    'path'=>'qrcode.png',
    'width'=>'100',
    'height'=>'100',
    'ratio'=>false
]);

$path = 'from_template3.docx';

$template->saveAs($path);
