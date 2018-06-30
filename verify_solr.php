<?php

use GuzzleHttp\Exception\BadResponseException;

include dirname(dirname(__DIR__)) . '/vendor/autoload.php';

$host = getenv('PANTHEON_INDEX_HOST');
$port = getenv('PANTHEON_INDEX_PORT');
$path = 'sites/self/environments/' . $_ENV['PANTHEON_ENVIRONMENT'] . '/index/';
$client_cert = $_SERVER['HOME'] . '/certs/binding.pem';
$url = 'https://' . $host . ':' . $port;
$schema = dirname(dirname(__DIR__)) . '/web/modules/contrib/search_api_solr/solr-conf/4.x/schema.xml';

$client = new \GuzzleHttp\Client([
  'base_uri' => $url,
  'cert' => $client_cert,
  'verify' => FALSE,
]);

try {
  $response = $client->get($path . 'admin/system');
  $responseCode = $response->getStatusCode();
  if ($responseCode >= 200 && $responseCode < 300) {
    echo "Solr previously initialized.\n"; // $response->getStatusCode(), ' ', $response->getReasonPhrase(), "\n", $response->getBody()->getContents(), "\n";
  } else {
    throw new DomainException('Unsuccessful ping');
  }
} catch (Throwable $badResponseException) {
  if ($badResponseException instanceof BadResponseException || ($badResponseException instanceof DomainException && $badResponseException->getMessage() === 'Unsuccessful ping')) {
    try {
      $schemaFile = fopen($schema, 'r');
      if (!$schemaFile) {
        throw new DomainException('Unable to open ' . $schema);
      }
      $response = $client->put($path, [
        'body' => $schemaFile,
      ]);
      if ($response->getStatusCode() == 200) {
        echo "Solr initialized and ready.\n"; // $response->getBody()->getContents(), "\n";
      }
      else {
        echo "Unable to initialize schema:\n", $response->getStatusCode(), ' ', $response->getReasonPhrase(), "\n", $response->getBody()->getContents(), "\n";
        exit(1);
      }
    } catch (Throwable $exception) {
      echo get_class($exception) . ' ' . $exception->getMessage() . "\n";
      exit(1);
    }
  }
  else {
    echo get_class($badResponseException) . ' ' . $badResponseException->getMessage() . "\n";
    exit(1);
  }
}
