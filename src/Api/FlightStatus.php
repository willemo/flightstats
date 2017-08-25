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
     * Get the flight status from a flight associated with provided Flight ID.
     *
     * @param  string $flightId    FlightStats' Flight ID number for the desired
     *                             flight
     * @param  array  $queryParams Query parameters to add to the request
     * @return array               The response from the API
     */
    public function getFlightStatusById($flightId, array $queryParams = [])
    {
        $endpoint = 'flight/status/' . $flightId;

        $response = $this->sendRequest($endpoint, $queryParams);

        return $this->parseResponse($response);
    }

    /**
     * Get the flight status from a flight that's arriving on the given date.
     *
     * @param  string   $carrier     The carrier (airline) code
     * @param  integer  $flight      The flight number
     * @param  DateTime $date        The arrival date
     * @param  array    $queryParams Query parameters to add to the request
     * @return array                 The response from the API
     */
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

        $response = $this->sendRequest($endpoint, $queryParams);

        return $this->parseResponse($response);
    }

    /**
     * Get the flight status from a flight that's departing on the given date.
     *
     * @param  string   $carrier     The carrier (airline) code
     * @param  integer  $flight      The flight number
     * @param  DateTime $date        The departure date
     * @param  array    $queryParams Query parameters to add to the request
     * @return array                 The response from the API
     */
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

        $response = $this->sendRequest($endpoint, $queryParams);

        return $this->parseResponse($response);
    }

    /**
     * Parse the response from the API to a more uniform and thorough format.
     *
     * @param  array  $response The response from the API
     * @return array            The parsed response
     */
    protected function parseResponse(array $response)
    {
        $airlines = $this->parseAirlines($response['appendix']['airlines']);

        $airports = $this->parseAirports($response['appendix']['airports']);

        $flights = [];

        foreach ($response['flightStatuses'] as $flight) {
            // Set the carrier
            $carrier = $airlines[$flight['carrierFsCode']];

            $flight['carrier'] = $carrier;

            // Set the departure airport
            $departureAirport = $airports[$flight['departureAirportFsCode']];

            $flight['departureAirport'] = $departureAirport;

            // Set the arrival airport
            $arrivalAirport = $airports[$flight['arrivalAirportFsCode']];

            $flight['arrivalAirport'] = $arrivalAirport;

            $flights[] = $flight;
        }

        return $flights;
    }
}
