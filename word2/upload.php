<?php


$docPath = createDoc();
$accessToken = AccesToken();
$itemID = uploadDoc($accessToken, $docPath);
openDoc($accessToken, $docPath);


// Creates the Document Using the Provided Template
function createDoc(){

  require_once './vendor/autoload.php';

  // Creating the new document...

  $template = new \PhpOffice\PhpWord\TemplateProcessor(realpath('template2.docx'));

  $template->setValues([

      'ref'=> 'JFS/XM/ATAD/011/23',
      'date'=> '19/01/2023',
      
  ]);

  $template->setImageValue('qrcode',
  [
      'path'=>'qrcode.png',
      'width'=>'100',
      'height'=>'100',
      'ratio'=>false
  ]);

  $path = 'from_template9.docx';

  $template->saveAs($path);

  return $path;

}

// Gets the created file and uploads it to the SharePoint Drive
function uploadDoc($accessToken, $path)
{

  $fileContents = file_get_contents($path); // Read the contents of the image file

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://graph.microsoft.com/v1.0/drives/b!8MDhRyTZNU-uuvRbSUgUjcJUZG2EIXtMhNwacBvbWpuUVVst2_9nR6TKaoBmnYQq/root:/test/'.$path.':/content',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => $fileContents,
    CURLOPT_HTTPHEADER => array(
      'Authorization: Bearer '. $accessToken 
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  echo $response;
}

function openDoc($accessToken, $itemID)
{

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://graph.microsoft.com/v1.0/sites/villasomaliafrs.sharepoint.com,47e1c0f0-d924-4f35-aeba-f45b4948148d,6d6454c2-2184-4c7b-84dc-1a701bdb5a9b/drive/root:/test/'. $itemID .'?Autho=null',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'Authorization: Bearer ' . $accessToken
    ),
  ));

  $json = curl_exec($curl);

  curl_close($curl);

  // Decode the JSON response into an associative array
  $data = json_decode ($json, true);

  // Get the web URL of the file from the array
  $webUrl = $data ["webUrl"];

  // Redirect to the web URL using the header function
  header ("Location: $webUrl");
  exit;
}

function AccesToken(){

  $curl = curl_init();
  
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://login.microsoftonline.com/695822cd-3aaa-446d-aac2-3ebb02854b8a/oauth2/v2.0/token?Content-Type=application%2Fx-www-form-urlencoded',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'client_id=a5d1b470-ceaf-4bb3-8a73-db1dd0ca5661&scope=https%3A%2F%2Fgraph.microsoft.com%2F.default&client_secret=Hzo8Q~Q.nKyCbD6WpqysfD8GPZfpP04bsFt-ncFk&grant_type=client_credentials',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/x-www-form-urlencoded',
      'Cookie: fpc=AvtPK5Dz759HgjJgzmeSAChRGrKTAQAAAIgG3NwOAAAA; stsservicecookie=estsfd; x-ms-gateway-slice=estsfd'
    ),
  ));
  
  $response = curl_exec($curl);
  
  // Decode the JSON response into an associative array
  $data = json_decode ($response, true);
  
  // Get the web URL of the file from the array
  $accessToken = $data ["access_token"];
  
  curl_close($curl);
  return $accessToken;
  
  }