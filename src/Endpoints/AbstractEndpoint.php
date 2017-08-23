<?php

namespace Willemo\FlightStats\Endpoints;

use Willemo\FlightStats\FlexClient;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractEndpoint
{
    /**
     * The Flex Client instance.
     *
     * @var Willemo\FlightStats\FlexClient
     */
    protected $client;

    /**
     * The endpoint parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Optional query parameters for the request.
     *
     * @var array
     */
    protected $queryParams;

    /**
     * Create a configured endpoint instance.
     *
     * @param FlexClient $client      The FlexClient instance
     * @param array      $parameters  The endpoint parameters
     * @param array      $queryParams The optional query parameters
     */
    public function __construct(
        FlexClient $client,
        array $parameters,
        array $queryParams = []
    ) {
        $this->client = $client;

        $resolver = new OptionsResolver;

        $this->configurepParameters($resolver);

        $this->parameters = $resolver->resolve($parameters);

        $this->queryParams = $queryParams;
    }

    /**
     * Send a request to the API endpoint
     *
     * @return Psr\Http\Message\ResponseInterface The response from the API
     */
    public function sendRequest()
    {
        $endpoint = $this->buildEndpoint();

        $query = $this->buidlQuery();

        return $this->client->getClient()->request('GET', $endpoint, [
            'query' => $query,
        ]);
    }

    /**
     * Build the endpoint of the URI.
     *
     * @return string The full endpoint string for this API endpoint
     */
    protected function buildEndpoint()
    {
        $parts = [
            $this->getEndpoint(),
            $this->client->getConfig('protocol'),
            $this->getVersion(),
            $this->client->getConfig('format'),
        ];

        $parts = array_merge($parts, $this->parameters);

        return implode('/', $parts);
    }

    /**
     * Build the query array for the endpoint, including authentication
     * information.
     *
     * @return array The query parameters
     */
    protected function buidlQuery()
    {
        $auth = [
            'appId' => $this->client->getConfig('appId'),
            'appKey' => $this->client->getConfig('appKey'),
        ];

        return array_merge($auth, $this->queryParams);
    }

    /**
     * Configure the endpoint parameters.
     *
     * @param  OptionsResolver $resolver The options resolver to configure
     * @return void
     */
    abstract public function configurepParameters(OptionsResolver $resolver);

    /**
     * Get the base endpoint for this endpoint.
     *
     * @return string The endpoint
     */
    abstract public function getEndpoint();

    /**
     * Get the version of this endpoint.
     *
     * @return string The version
     */
    abstract public function getVersion();
}
