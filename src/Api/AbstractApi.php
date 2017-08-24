<?php

namespace Willemo\FlightStats\Api;

use Willemo\FlightStats\FlexClient;

abstract class AbstractApi implements ApiInterface
{
    /**
     * The FlexClient.
     *
     * @var Willemo\FlightStats\FlexClient
     */
    protected $flexClient;

    /**
     * Create an instance of the API object.
     *
     * @param FlexClient $flexClient The configured FlexClient object
     */
    public function __construct(FlexClient $flexClient)
    {
        $this->flexClient = $flexClient;
    }

    /**
     * Get the API name to use in the URI.
     *
     * @return string The API name
     */
    abstract public function getApiName();

    /**
     * Get the API version to use in the URI.
     *
     * @return string The API version
     */
    abstract public function getApiVersion();

    /**
     * Send the request through the FlexClient.
     *
     * @param  string $endpoint    The endpoint to make the
     *                             request to
     * @param  array  $queryParams The query parameters
     * @return array               The response from the API
     */
    protected function sendRequest($endpoint, array $queryParams)
    {
        return $this->flexClient->sendRequest(
            $this->getApiName(),
            $this->getApiVersion(),
            $endpoint,
            $queryParams
        );
    }
}
