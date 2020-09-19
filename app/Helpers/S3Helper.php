<?php


namespace App\Helpers;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

class S3Helper
{
    public $config;
    public $client;
    public $bucket;

    public function __construct()
    {
        $S3 = constants('S3');
        $credentials = new Credentials($S3['KEY'], $S3['SECRET']);

        $this->client = new S3Client([
            'version' => 'latest',
            'region' => $S3['REGION'],
            'credentials' => $credentials
        ]);

        $this->bucket = $S3['BUCKET'];
    }

    public function uploadImage($image, $target, $type)
    {
        try {
            $result = $this->client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $target,
                'ACL'    => 'public-read',
                'SourceFile' => $image,
                'ContentType' => $type,
                'StorageClass' => 'STANDARD'
            ]);

            if (isset($result['ObjectURL'])) {
                gc_collect_cycles();
                return $result['ObjectURL'];
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function isS3($path)
    {
        if (strpos($path, 'amazonaws.com') !== false) {
            return true;
        } else {
            return false;
        }
    }
}
