<?php
namespace AWSCognito;
require __DIR__ . '/../vendor/autoload.php';
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;

class AWSCognitoWrapper {
  private const COOKIE_NAME = 'openscreen-cognito-access-token';
  private const CONFIG_URL = 'https://config.openscreen.com/api-production.json';

  public bool $useCookie;

  private $user = null;
  private $client;
  private $clientId;
  private $userPoolId;

  public function __construct(?bool $useCookie = false) {
    $this->$useCookie = $useCookie;

    $this->client = new CognitoIdentityProviderClient([
      'version' => '2016-04-18',
      'region' => 'us-east-1',
    ]);

    if ($this->$useCookie) {
      try {
        $this->user = $this->client->getUser([
          'AccessToken' => $this->getAuthenticationCookie()
        ]);
      } catch(\Exception $e) {
          // an exception indicates the accesstoken is incorrect - $this->user will still be null
      }
    }
  }

  public function authenticate(string $username, string $password) : string {
    try {
      $config = file_get_contents(self::CONFIG_URL);
      $parsed = json_decode($config);
      $result = $this->client->initiateAuth([
        'AuthFlow' => 'USER_PASSWORD_AUTH',
        'ClientId' => $parsed->clientId,
        'UserPoolId' => $parsed->poolId,
        'AuthParameters' => [
          'USERNAME' => $username,
          'PASSWORD' => $password,
        ],
      ]);
    } catch (\Exception $e) {
      return "[ERROR] AWS Cognito: $e";
    }
  
    $accessToken = $result->get('AuthenticationResult')['AccessToken'];
  
    if ($this->$useCookie) {
      $this->setAuthenticationCookie(accessToken);
    }
  
    return accessToken;
  }
  
  private function setAuthenticationCookie(string $accessToken, $time = 3600) : void {
    /*
     * Please note that plain-text storage of the access token is insecure and
     * not recommended by AWS. This is only done to keep this example
     * application as easy as possible. Read the AWS docs for more info:
     * http://docs.aws.amazon.com/cognito/latest/developerguide/amazon-cognito-user-pools-using-tokens-with-identity-providers.html
    */
    setcookie(self::COOKIE_NAME, $accessToken, time() + $time);
  }
  
  private function getAuthenticationCookie() : string {
    return $_COOKIE[self::COOKIE_NAME] ?? '';
  }
}

?>