<?php

namespace App\Client;

use DateTime;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use stdClass;

class NasaApiClient implements NasaApiClientInterface
{
    protected const ENDPOINT_EPIC_IMAGE_METADATA = 'https://epic.gsfc.nasa.gov/api/natural/date/';
    protected const BASE_URL_EPIC_IMAGE_FILE = 'https://epic.gsfc.nasa.gov/archive/natural/';
    protected const IMAGE_TYPES = ['png', 'jpg', 'thumbs'];

    /**
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    protected HttpClientInterface $httpClient;

    /**
     * The API key to use for requests.
     */
    protected string $apiKey;

    /**
     * Constructs a new NasaApiClient.
     *
     * @param \Symfony\Contracts\HttpClient\HttpClientInterface $http_client
     */
    public function __construct(HttpClientInterface $http_client, string $api_key = null)
    {
        $this->httpClient = $http_client;
        $this->apiKey = $api_key;
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function getDailyEarthImageMetadata(string $date): ?array
    {
        $responseData = $this->getJSON(static::ENDPOINT_EPIC_IMAGE_METADATA . $date);
        if ($responseData === false) {
            throw new RuntimeException('API connection failed.');
        }

        if (empty($responseData)) {
            throw new InvalidArgumentException('No images were found for the provided date.');
        }

        return $responseData;
    }

    /**
     * Sends a GET request and returns the JSON data content.
     *
     * @param string $url
     *   The URL to send the request to.
     * @param array $query
     *   Optional query parameters to add to the request.
     *
     * @return mixed|bool
     *   A data object containing or false in case of errors.
     */
    protected function getJSON(string $url, array $query = []): mixed
    {
        $apiKey = $this->apiKey;
        if ($apiKey && !isset($query['api_key'])) {
            $query['api_key'] = $apiKey;
        }

        $data = $this->httpClient->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'query' => $query,
        ]);

        return $data ? json_decode($data->getContent()) : false;
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