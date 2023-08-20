<?php

namespace App\Client;

use DateTime;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use stdClass;

class NasaApiClient implements NasaApiClientInterface
{
    /**
     * Your personal API key.
     * 
     * @see https://api.nasa.gov/index.html#signUp
     * 
     * @todo Add separate config to keep this secret out of the repo.
     */
    protected const API_KEY = 'p960B4skMQHGdPnetw2KYFVzzoomz4GV5oZMZjUM';

    protected const ENDPOINT_EPIC_IMAGE_METADATA = 'https://epic.gsfc.nasa.gov/api/natural/date/';
    protected const BASE_URL_EPIC_IMAGE_FILE = 'https://epic.gsfc.nasa.gov/archive/natural/';
    protected const IMAGE_TYPES = ['png', 'jpg', 'thumbs'];

    /**
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    protected HttpClientInterface $httpClient;

    /**
     * Constructs a new NasaApiClient.
     * 
     * @param \Symfony\Contracts\HttpClient\HttpClientInterface $http_client
     */
    public function __construct(HttpClientInterface $http_client)
    {
        $this->httpClient = $http_client;
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
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
            throw new RuntimeException('API connection failed.');
        }

        $responseData = json_decode($response->getContent());
        if (empty($responseData)) {
            throw new InvalidArgumentException('No images were found for the provided date.');
        }

        return $responseData;
    }

    /**
     * {@inheritDoc}
     */
    public function getImageUrl(stdClass $imageMetadata, string $imageType): string
    {
        $dateTime = new DateTime($imageMetadata->date);
        if (!$dateTime || !in_array($imageType, static::IMAGE_TYPES, true)) {
            return '';
        }

        return static::BASE_URL_EPIC_IMAGE_FILE
            . $dateTime->format('Y/m/d') . DIRECTORY_SEPARATOR
            . $imageType . DIRECTORY_SEPARATOR
            . $imageMetadata->image . ($imageType === 'png' ? '.png' : '.jpg');
    }

    /**
     * {@inheritDoc}
     */
    public function getImageTypes(): array
    {
        return static::IMAGE_TYPES;
    }
}