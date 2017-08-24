<?php

namespace Willemo\FlightStats\Api;

use DateTime;
use Psr\Http\Message\ResponseInterface;

class Schedules extends AbstractApi
{
    /**
     * Get the API name to use in the URI.
     *
     * @return string The API name
     */
    public function getApiName()
    {
        return 'schedules';
    }

    /**
     * Get the API version to use in the URI.
     *
     * @return string The API version
     */
    public function getApiVersion()
    {
        return 'v1';
    }

    /**
     * Get information about a scheduled flight arriving on the given date.
     *
     * @param  string   $carrier     The carrier (airline) code
     * @param  integer  $flight      The flight number
     * @param  DateTime $date        The arrival date
     * @param  array    $queryParams Query parameters to add to the request
     * @return ResponseInterface     The response from the API
     */
    public function getFlightByArrivalDate(
        $carrier,
        $flight,
        DateTime $date,
        array $queryParams = []
    ) {
        $endpoint = sprintf(
            'flight/%s/%s/arriving/%s',
            $carrier,
            $flight,
            $date->format('Y/n/j')
        );

        return $this->sendRequest($endpoint, $queryParams);
    }

    /**
     * Get information about a scheduled flight departing on the given date.
     *
     * @param  string   $carrier     The carrier (airline) code
     * @param  integer  $flight      The flight number
     * @param  DateTime $date        The departure date
     * @param  array    $queryParams Query parameters to add to the request
     * @return ResponseInterface     The response from the API
     */
    public function getFlightByDepartureDate(
        $carrier,
        $flight,
        DateTime $date,
        array $queryParams = []
    ) {
        $endpoint = sprintf(
            'flight/%s/%s/departing/%s',
            $carrier,
            $flight,
            $date->format('Y/n/j')
        );

        return $this->sendRequest($endpoint, $queryParams);
    }
}
