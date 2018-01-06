EspnFF
======

API for ESPN Fantasy Football Leagues

ESPN, why do you not make these endpoints publically accessible?

Why do you not have a real public API for us to consume? :(

## Anyway, current usage:
```php
$config = [
    'auth' => [
        'username' => 'YOUR_ESPN_USERNAME_HERE',
        'password' => 'YOUR_ESPN_PASSWORD_HERE',
    ],
    'leagueId' => 123456,
    'seasonId' => 2017,
];

$espn = new Espn($config);
$response = $espn->getClient()->request('GET', 'scoreboard');

print_r((string)$response->getBody());
```

Sadly, this is mostly all I can do for now.  It would be *awesome* if ESPN would at least expose a read-only (GET only) API for us :sob:
