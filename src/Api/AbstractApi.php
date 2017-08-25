<?php

namespace Willemo\FlightStats\Api;

use DateTime;
use DateTimeZone;
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

    /**
     * Parse the airlines array into an associative array with the airline's
     * FS code as the key.
     *
     * @param  array  $airlines The airlines from the response
     * @return array            The associative array of airlines
     */
    protected function parseAirlines(array $airlines)
    {
        $parsed = [];

        foreach ($airlines as $airline) {
            $parsed[$airline['fs']] = $airline;
        }

        return $parsed;
    }

    /**
     * Parse the airports array into an associative array with the airport's
     * FS code as the key.
     *
     * @param  array  $airports The airports from the response
     * @return array            The associative array of airports
     */
    protected function parseAirports(array $airports)
    {
        $parsed = [];

        foreach ($airports as $airport) {
            $parsed[$airport['fs']] = $airport;
        }

        return $parsed;
    }

    /**
     * Change a date/time in a local time zone to UTC.
     *
     * @param  string  $dateTimeString The local date/time as a string
     * @param  string  $timeZone       The local time zone name
     * @param  boolean $shouldFormat   Should the response be formatted ('c')
     * @return DateTime|string         The date/time in UTC
     */
    protected function dateToUtc(
        string $dateTimeString,
        string $timeZone,
        $shouldFormat = true
    ) {
        $dt = new DateTime($dateTimeString, new DateTimeZone($timeZone));

        $dt->setTimeZone(new DateTimeZone('UTC'));

        if (!$shouldFormat) {
            return $dt;
        }

        return $dt->format('c');
    }

    /**
     * Parse the response from the API to a more uniform and thorough format.
     *
     * @param  array  $response The response from the API
     * @return array            The parsed response
     */
    abstract protected function parseResponse(array $response);
}
