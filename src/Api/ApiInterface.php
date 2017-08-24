<?php

namespace Willemo\FlightStats\Api;

interface ApiInterface
{
    /**
     * Get the API name to use in the URI.
     *
     * @return string The API name
     */
    public function getApiName();

    /**
     * Get the API version to use in the URI.
     *
     * @return string The API version
     */
    public function getApiVersion();
}
