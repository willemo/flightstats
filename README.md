# FlightStats

PHP client for the FlightStats API.

## Installation

Use Composer to install this package:

```
composer.phar install willemo/flightstats
```

## Usage

Create a new `Willemo\FlightStats\FlexClient` and use that to make requests to the FlightStats API:

```php
$client = new Willemo\FlightStats\FlexClient([
    'appId' => 'yourAppId',
    'appKey' => 'yourAppKey',
]);

// Get information about flight AA100 departing on September 5th:
$response = $client->schedules()->getFlightByDepartureDate(
    'AA',
    100
    new DateTime('2017-09-05')
);

$json = $response->getBody();

$data = json_decode($json, true);
```

## Available APIs

The following FlightStats APIs are currently available:

### Flight Status API

[Flight Status API documentation](https://developer.flightstats.com/api-docs/flightstatus/v2/flight)

#### getFlightStatusById

Get the flight status from a flight associated with provided Flight ID.

```php
$client->flightStatus()->getFlightStatusById(123456, [
    // Optional query parameters
    'extendedOptions' => [
        'includeDeltas',
    ],
]);
```

#### getFlightStatusByArrivalDate

Get the flight status from a flight that's arriving on the given date.

```php
$client->flightStatus()->getFlightStatusByArrivalDate('AA', 100, new DateTime('2017-09-05'), [
    // Optional query parameters
    'utc' => true,
    'extendedOptions' => [
        'includeDeltas',
    ],
]);
```

#### getFlightStatusByDepartureDate

Get the flight status from a flight that's departing on the given date.

```php
$client->flightStatus()->getFlightStatusByDepartureDate('AA', 100, new DateTime('2017-09-05'), [
    // Optional query parameters
    'utc' => true,
    'extendedOptions' => [
        'includeDeltas',
    ],
]);
```

### Schedules API

[Schedules API documentation](https://developer.flightstats.com/api-docs/scheduledFlights/v1)

#### getFlightByArrivalDate

```php
$client->schedules()->getFlightByArrivalDate('AA', 100, new DateTime('2017-09-05'), [
    // Optional query parameters
    'extendedOptions' => [
        'includeDeltas',
    ],
]);
```

#### getFlightByDepartureDate

```php
$client->schedules()->getFlightByDepartureDate('AA', 100, new DateTime('2017-09-05'), [
    // Optional query parameters
    'extendedOptions' => [
        'includeDeltas',
    ],
]);
```

## Note

Copyright of the name FlightStats and its API belong to FlightStats.
