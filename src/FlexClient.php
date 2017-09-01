<?php

namespace Willemo\FlightStats;

use Symfony\Component\OptionsResolver\OptionsResolver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Willemo\FlightStats\Exception\ClientException as FlexClientException;
use Willemo\FlightStats\Api\FlightStatus;
use Willemo\FlightStats\Api\Schedules;
use Psr\Http\Message\ResponseInterface;

class FlexClient
{
    /**
     * The API client configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * The HTTP client.
     *
     * @var Client
     */
    protected $client;

    /**
     * Create a new FlexClient instance with its config.
     *
     * @param array $config The API client config array
     */
    public function __construct(array $config = [])
    {
        $resolver = new OptionsResolver();

        $this->configureConfig($resolver);

        $this->config = $resolver->resolve($config);
    }

    /**
     * Configure the OptionsResolver for the config.
     *
     * @param  OptionsResolver $resolver The OptionsResolver to configure
     * @return void
     */
    public function configureConfig(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'base_uri' => 'https://api.flightstats.com/flex/',
            'protocol' => 'rest',
            'format' => 'json',
            'use_http_errors' => true,
            'use_utc_time' => true,
        ]);

        $resolver->setRequired([
            'appId',
            'appKey',
        ]);
    }

    /**
     * Get the configured HTTP client.
     *
     * @return Client The configured HTTP client
     */
    public function getClient()
    {
        if ($this->client === null) {
            $this->client = new Client([
                'base_uri' => $this->config['base_uri'],
            ]);
        }

        return $this->client;
    }

    /**
     * Get a variable from the config.
     *
     * @param  string $name The name of the variable to get
     * @return mixed        The value of the config variable
     */
    public function getConfig($name)
    {
        return $this->config[$name];
    }

    /**
     * Send a request to the API.
     *
     * @param  string $api         The API name
     * @param  string $version     The API version
     * @param  string $endpoint    The endpoint of the URI
     * @param  array  $queryParams The query parameters
     * @return array               The response from the API
     */
    public function sendRequest(
        $api,
        $version,
        $endpoint,
        array $queryParams = []
    ) {
        $endpoint = $this->buildEndpoint($api, $version, $endpoint);

        $query = $this->buildQuery($queryParams);

        try {
            $response = $this->getClient()->request('GET', $endpoint, [
                'query' => $query
            ]);

            return $this->parseResponse($response);
        } catch (ClientException $e) {
            $this->parseClientException($e);
        }

        return [];
    }

    /**
     * Get the FlightStatus API.
     *
     * @return FlightStatus
     */
    public function flightStatus()
    {
        return new FlightStatus($this);
    }

    /**
     * Get the Schedules API.
     *
     * @return Schedules
     */
    public function schedules()
    {
        return new Schedules($this);
    }

    /**
     * Build the endpoint of the URI.
     *
     * @param  string $api      The API name
     * @param  string $version  The API version
     * @param  string $endpoint The endpoint to use
     * @return string The full endpoint string for this API endpoint
     */
    protected function buildEndpoint($api, $version, $endpoint)
    {
        return implode('/', [
            $api,
            $this->config['protocol'],
            $version,
            $this->config['format'],
            $endpoint,
        ]);
    }

    /**
     * Build the query array for the endpoint, including authentication
     * information.
     *
     * @param  array $queryParams The query parameters to use
     * @return array              The query parameters
     */
    protected function buildQuery(array $queryParams = [])
    {
        $auth = [
            'appId' => $this->config['appId'],
            'appKey' => $this->config['appKey'],
        ];

        $queryParams = array_merge($auth, $queryParams);

        if (isset($queryParams['extendedOptions'])) {
            $extendedOptions = $queryParams['extendedOptions'];
        } else {
            $extendedOptions = [];
        }

        if (!is_array($extendedOptions)) {
            $extendedOptions = [$extendedOptions];
        }

        if ($this->config['use_http_errors'] &&
            !in_array('useHTTPErrors', $extendedOptions)
        ) {
            $extendedOptions[] = 'useHTTPErrors';
        }

        $queryParams['extendedOptions'] = implode('+', $extendedOptions);

        return $queryParams;
    }

    /**
     * Parse the response from the API to an array
     *
     * @param  ResponseInterface $response The response from the API
     * @return array                       The response array
     */
    protected function parseResponse(ResponseInterface $response)
    {
        return json_decode($response->getBody(), true);
    }

    /**
     * Parse the error response from the API
     *
     * @param  ClientException $exception The exception that contains the
     *                                    response
     * @throws FlexClientException        The parsed error response from the API
     * @return void
     */
    protected function parseClientException(ClientException $exception)
    {
        $response = $exception->getResponse();

        $lines = explode("\n", $response->getBody());

        $message = 'Something went wrong';

        foreach ($lines as $line) {
            if (strpos($line, 'message') !== false) {
                $message = substr($line, 9);

                break;
            }
        }

        throw new FlexClientException($message, 0);
    }
}
