<?php

namespace Willemo\FlightStats;

use Symfony\Component\OptionsResolver\OptionsResolver;
use GuzzleHttp\Client;
use Willemo\FlightStats\Exception\InvalidApiException;
use Willemo\FlightStats\Api\ApiInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @method ApiInterface flightStatus()
 */
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
     * @var GuzzleHttp\Client
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
     * @return GuzzleHttp\Client The configured HTTP client
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
     * Get a veriable from the config.
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
     * @return ResponseInterface   The response from the API
     */
    public function sendRequest(
        $api,
        $version,
        $endpoint,
        array $queryParams = []
    ) {
        $endpoint = $this->buildEndpoint($api, $version, $endpoint);

        $query = $this->buidlQuery($queryParams);

        return $this->getClient()->request('GET', $endpoint, [
            'query' => $query
        ]);
    }

    /**
     * Get the API to make requests to.
     *
     * @param  string $name        The name of the API
     * @param  array  $arguments   The arguments for this method (not used)
     * @throws InvalidApiException If the API class doesn't exist
     * @return ApiInterface        The API object
     */
    public function __call($name, $arguments)
    {
        $apiName = ucfirst($name);

        $className = __NAMESPACE__ . '\\Api\\' . $apiName;

        if (!class_exists($className)) {
            throw new InvalidApiException("API {$apiName} doesn't exist.");
        }

        return new $className($this);
    }

    /**
     * Build the endpoint of the URI.
     *
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
    protected function buidlQuery(array $queryParams = [])
    {
        $auth = [
            'appId' => $this->config['appId'],
            'appKey' => $this->config['appKey'],
        ];

        $queryParams = array_merge($auth, $queryParams);

        $extendedOptions = $queryParams['extendedOptions'] ?: [];

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
}
