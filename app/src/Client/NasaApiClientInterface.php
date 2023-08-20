<?php

namespace App\Client;

use stdClass;

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
    public function downloadDailyEarthImages(string $date, string $destination, string $imageType): void;

    /**
     * Builds the archive url from which the given image can be downloaded.
     *
     * @param \stdClass $imageMetadata
     *   The image metadata as returned by ::getDailyEarthImageMetadata.
     * @param string $imageType
     *   The image format and size, one of "png", "jpg", "thumbs".
     *
     * @return string
     */
    public function getImageUrl(stdClass $imageMetadata, string $imageType): string;

    /**
     * Get the list of supported image types.
     *
     * @return array<string>
     */
    public function getImageTypes(): array;
}