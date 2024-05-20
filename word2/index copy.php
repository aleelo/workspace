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

$path = 'from_template2.docx';

$template->saveAs($path);

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');

// It will be called downloaded.pdf
header('Content-Disposition: attachment; filename='."$path");

// The PDF source is in original.pdf
readfile($path);