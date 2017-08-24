<?php

namespace Willemo\FlightStats\Api;

use DateTime;
use Psr\Http\Message\ResponseInterface;

class FlightStatus extends AbstractApi
{
    /**
     * Get the API name to use in the URI.
     *
     * @return string The API name
     */
    public function getApiName()
    {
        return 'flightstatus';
    }

    /**
     * Get the API version to use in the URI.
     *
     * @return string The API version
     */
    public function getApiVersion()
    {
        return 'v2';
    }

    /**
     * Returns the Flight Status associated with provided Flight ID.
     *
     * @param  string $flightId    FlightStats' Flight ID number for the desired
     *                             flight
     * @param  array  $queryParams Query parameters to add to the request
     * @return ResponseInterface   The response from the API
     */
    public function getFlightStatusById($flightId, array $queryParams = [])
    {
        $endpoint = 'flight/status/' . $flightId;

        return $this->sendRequest($endpoint, $queryParams);
    }

    public function getFlightStatusByArrivalDate(
        $carrier,
        $flight,
        DateTime $date,
        array $queryParams = []
    ) {
        $endpoint = sprintf(
            'flight/status/%s/%s/arr/%s',
            $carrier,
            $flight,
            $date->format('Y/n/j')
        );

        if (!isset($queryParams['utc'])) {
            $queryParams['utc'] = $this->flexClient->getConfig('use_utc_time');
        }

        return $this->sendRequest($endpoint, $queryParams);
    }

    public function getFlightStatusByDepartureDate(
        $carrier,
        $flight,
        DateTime $date,
        array $queryParams = []
    ) {
        $endpoint = sprintf(
            'flight/status/%s/%s/dep/%s',
            $carrier,
            $flight,
            $date->format('Y/n/j')
        );

        if (!isset($queryParams['utc'])) {
            $queryParams['utc'] = $this->flexClient->getConfig('use_utc_time');
        }

        return $this->sendRequest($endpoint, $queryParams);
    }
}
