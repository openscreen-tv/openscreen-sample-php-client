
<?php

use AWSCognito\AWSCognitoWrapper;

$accountId = getenv('ACCOUNT_ID') 
$projectId = getenv('PROJECT_ID') 

$cognito = new AWSCognitoWrapper()
$accessToken = $cognito->authenticate(getenv('KEY'), getenv('SECRET'))\
