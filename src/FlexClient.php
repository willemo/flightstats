<?php

namespace Willemo\FlightStats;

use Symfony\Component\OptionsResolver\OptionsResolver;
use GuzzleHttp\Client;

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
            'base_uri' => 'https://api.flightstats.com/flex',
            'protocol' => 'rest',
            'format' => 'json',
            'use_http_errors' => true,
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
}
