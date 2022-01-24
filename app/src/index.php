
<?php
require 'OpenscreenWrapper.php';
use Openscreen\OpenscreenWrapper;

$accountId = 'KW6498MT5437JW';
$projectId = '2bcca008-4a9c-48f0-b66d-9fec3d45c159';
$os = new OpenscreenWrapper('LiQ7CLLf4tKIJcHU8y', 'Usy4dVbzFU9GljZdGx2OoSdy', $debug = true);

// $accountId = getenv('ACCOUNT_ID'); 
// $projectId = getenv('PROJECT_ID');
// $os = new OpenscreenWrapper(getenv('KEY'), getenv('SECRET'), (bool)getenv('DEBUG'));

// An example using request body 
// $response = $os->query('POST', "projects/$projectId/assets", null, [
//   'name'=>'GreenDay123',
//   'description'=>'An item',
//   'customAttributes'=>[
//     'color'=>'red'
//   ],
//   'qrCodes'=>[
//     [
//       'intentType'=>'DYNAMIC_REDIRECT',
//       'intent'=>'https://localhost:8000/cool',
//       'intentState'=>[],
//     ],
//     [
//       'intentType'=>'DYNAMIC_REDIRECT',
//       'intent'=>'https://localhost:8000/song',
//       'intentState'=>[],
//     ]
//   ]
// ]);
$assetId = '91b33908-f82b-4137-9763-afdc398bd633';

// $response = $os->query('GET', "projects/$projectId/assets");

// An example using request body
$response = $os->query('GET', "assets/$assetId/qrcodes", ['lightColor' => '#FFFFFF00', 'dataUrl' => true]);
?>