<?php

require 'vendor/autoload.php'; // Include the AWS SDK for PHP
require 'vendor/autoload.php'; // Include the dotenv package

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Dotenv\Dotenv;

/* *************************************************** 
Usage example $filePath = '/path/to/file.ext';
**************************************************** */
$filePath = 'documents/nieta.jpg';
/* ************************************************* */

function uploadToS3($filePath)
{
    // Load environment variables from .env file
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $bucketName = $_ENV['BUCKET_NAME'];
    $accessKey = $_ENV['AWS_ACCESS_KEY_ID'];
    $secretKey = $_ENV['AWS_SECRET_ACCESS_KEY'];
    $region = $_ENV['AWS_REGION'];

    try {
        // Create an S3 client
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secretKey,
            ],
        ]);

        // Generate a unique object key based on the uploaded file's name
        // $objectKey = basename($filePath);
        $objectKey = uniqid() . '_' . basename($filePath);

        // Upload the file to the S3 bucket
        $result = $s3->putObject([
            'Bucket' => $bucketName,
            'Key' => $objectKey,
            'SourceFile' => $filePath,
        ]);

        if ($result['@metadata']['statusCode'] === 200) {
            echo 'File uploaded successfully.';
        } else {
            echo 'Failed to upload the file.';
        }
    } catch (S3Exception $e) {
        echo 'Error uploading the file: ' . $e->getMessage();
    }
}

function displayUploadedFiles()
{
    // Load environment variables from .env file
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $bucketName = $_ENV['BUCKET_NAME'];
    $accessKey = $_ENV['AWS_ACCESS_KEY_ID'];
    $secretKey = $_ENV['AWS_SECRET_ACCESS_KEY'];
    $region = $_ENV['AWS_REGION'];

    try {
        // Create an S3 client
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secretKey,
            ],
        ]);

        // List all objects in the S3 bucket
        $objects = $s3->listObjects([
            'Bucket' => $bucketName,
        ]);

        // Display the links to view the uploaded files
        echo '<ul>';
        foreach ($objects['Contents'] as $object) {
            $fileKey = $object['Key'];
            $fileUrl = $s3->getObjectUrl($bucketName, $fileKey);

            echo '<li><a href="' . $fileUrl . '" target="_blank">' . $fileKey . '</a></li>';
        }
        echo '</ul>';

    } catch (S3Exception $e) {
        echo 'Error displaying uploaded files: ' . $e->getMessage();
    }
}

uploadToS3($filePath);
displayUploadedFiles();
