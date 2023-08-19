<?php

namespace App\Client;

interface NasaApiClientInterface
{
    /**
     * @param string $date
     *   The date in YYYY-MM-DD format.
     * 
     * @return array|null
     */
    public function getDailyEarthImageMetadata(string $date): ?array;

    /**
     * @todo
     */
    public function downloadDailyEarthImages(string $date, string $destination): void;
}