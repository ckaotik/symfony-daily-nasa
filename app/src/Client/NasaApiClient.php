<?php

namespace App\Client;

// use Guzzle\Http\ClientInterface;

use DateTime;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Filesystem\Filesystem;
use stdClass;

class NasaApiClient implements NasaApiClientInterface
{
    /**
     * Your personal API key.
     * 
     * @see https://api.nasa.gov/index.html#signUp
     */
    protected const API_KEY = 'p960B4skMQHGdPnetw2KYFVzzoomz4GV5oZMZjUM';
    
    /**
     * One of 'png', 'jpg', 'thumbs'.
     */
    protected const IMAGE_TYPE = 'thumbs';

    protected const ENDPOINT_EPIC_IMAGE_METADATA = 'https://epic.gsfc.nasa.gov/api/natural/date/';
    protected const BASE_URL_EPIC_IMAGE_FILE = 'https://epic.gsfc.nasa.gov/archive/natural/';

    /**
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    protected HttpClientInterface $httpClient;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected Filesystem $fileSystem;

    /**
     * Constructs a new NasaApiClient.
     * 
     * @param \Symfony\Contracts\HttpClient\HttpClientInterface $http_client
     * @param \Symfony\Component\Filesystem\Filesystem $file_system
     */
    public function __construct(HttpClientInterface $http_client, Filesystem $file_system)
    {
        $this->httpClient = $http_client;
        $this->fileSystem = $file_system;
    }

    /**
     * {@inheritDoc}
     */
    public function getDailyEarthImageMetadata(string $date): ?array
    {
        $response = $this->httpClient->request('GET', static::ENDPOINT_EPIC_IMAGE_METADATA . $date, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'query' => [
                'api_key' => static::API_KEY,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        return json_decode($response->getContent());
    }

    /**
     * {@inheritDoc}
     */
    public function downloadDailyEarthImages(string $date, string $destination): void
    {
        $imagesMetadata = $this->getDailyEarthImageMetadata($date);
        if (!is_array($imagesMetadata)) {
            return;
        }

        foreach ($imagesMetadata as $imageMetadata) {
            if (empty($imageMetadata->image)) {
                continue;
            }

            $sourceUrl = $this->buildImageUrl($imageMetadata);

            $this->fileSystem->mkdir($destination);
            $this->fileSystem->copy($sourceUrl, $destination . basename($sourceUrl));
        }
    }

    /**
     * Builds the archive url from which the given image can be downloaded.
     */
    protected function buildImageUrl(stdClass $imageMetadata): string 
    {
        $dateTime = new DateTime($imageMetadata->date);

        return static::BASE_URL_EPIC_IMAGE_FILE
            . $dateTime->format('Y/m/d') . DIRECTORY_SEPARATOR
            . static::IMAGE_TYPE . DIRECTORY_SEPARATOR
            . $imageMetadata->image . (static::IMAGE_TYPE === 'png' ? '.png' : '.jpg');
    }
}