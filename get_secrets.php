<?php
require 'vendor/autoload.php';

use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

function getDbCredentials($secretName, $region = 'us-east-1') {
    $client = new SecretsManagerClient([
        'version' => 'latest',
        'region' => $region
    ]);

    try {
        $result = $client->getSecretValue([
            'SecretId' => $secretName,
        ]);

        if (isset($result['SecretString'])) {
            return json_decode($result['SecretString'], true);
        }
    } catch (AwsException $e) {
        error_log($e->getMessage());
        return null;
    }
}
