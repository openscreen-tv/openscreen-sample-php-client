
<?php
require 'AWSCognito.php';
use AWSCognito\AWSCognitoWrapper;
$cognito = new AWSCognitoWrapper();

// $accountId = getenv('ACCOUNT_ID'); 
// $projectId = getenv('PROJECT_ID');
// $accessToken = $cognito->authenticate(getenv('KEY'), getenv('SECRET'));
$accountId = 'LL8817LM1522BD';
$projectId = '9f5d2e04-98b2-463c-ab87-7447c9f48a35';
$accessToken = $cognito->authenticate('zZzMClvXafy7Y1SiLn', 'HKQ7B17yjMkndExR5u9wofG8');

echo $accessToken;

// Get assets by project ID
// Create a stream
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Authorization: Bearer $accessToken\r\n"
  )
);
$context = stream_context_create($opts);

// Open the file using the HTTP headers set above
$file = file_get_contents("https://kbdgsb6g57.execute-api.us-east-1.amazonaws.com/prod/projects/$projectId/assets", false, $context);