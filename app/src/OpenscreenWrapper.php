<?php
namespace Openscreen;
require __DIR__ . '/../vendor/autoload.php';
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;

class OpenscreenWrapper {
  private const CONFIG_URL = 'https://config.openscreen.com/prod-aNCxEnLyMSsR8sso.json';

  private $debug = false;
  private $config;
  private $client;
  private $user = null;
  private $accessToken = null;

  public function __construct(string $username, string $password, ?bool $debug = false) {
    $this->debug = $debug;
    $rawConfig = file_get_contents(self::CONFIG_URL);
    $this->config = json_decode($rawConfig);
    $this->client = new CognitoIdentityProviderClient([
      'version' => '2016-04-18',
      'region' => $this->config->region,
    ]);
    $this->accessToken = $this->authenticate($username, $password);
  }

  public function getToken() : string {
    return $this->accessToken;
  }

  private function authenticate(string $username, string $password) : string {
    try {
      $result = $this->client->initiateAuth([
        'AuthFlow' => 'USER_PASSWORD_AUTH',
        'ClientId' => $this->config->clientId,
        'UserPoolId' => $this->config->poolId,
        'AuthParameters' => [
          'USERNAME' => $username,
          'PASSWORD' => $password,
        ],
      ]);
      if ($this->debug) {
        echo "Authenticated\n";
      }
    } catch (\Exception $e) {
      return "[ERROR] AWS Cognito: $e";
    }
    
    return $result->get('AuthenticationResult')['AccessToken'];
  }

  private function getBaseUrl() : string {
    return $this->config->endpoint;
  }

  /**
   * 
   */
  public function query(string $method = 'GET', string $path, ?array $queryParams = null, ?array $body = null) : ?object {
    $accessToken = $this->getToken();
    $opts = [
      'http'=>[
        'method'=>$method,
        'header'=>["Content-Type: application/json", "Authorization: Bearer $accessToken", "Accept: application/json, text/plain, */*"],
        'max_redirects' => 3,
      ]
    ];
    if ($body) {
      $opts['http']['content'] = json_encode((object)$body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
    if ($this->debug) {
      $stringOpts = json_encode($opts);
      echo "Sending request to $path:\n$stringOpts\n\n";
    }
    $context = stream_context_create($opts);
    $baseUrl = $this->getBaseUrl();
    $url = "$baseUrl/$path";
    if ($queryParams) {
      $queryParams = http_build_query($queryParams);
      $url = "$url?$queryParams";
    }
    $response = file_get_contents($url, false, $context);
    if ($response) {
      if ($this->debug) {
        echo "Response from $path:\n$response\n\n";
      }
      return json_decode($response);
    } else {
      if ($this->debug) {
        echo "No response\n\n";
      }
      return null;
    }
  }
}

?>