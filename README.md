# GYG Test

This small project fetches data from external service, filters based on provided parameters, and shows the available results.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine.

### Prerequisites

In order to get this service work on your local machine, you will need

* Fresh version of PHP - 7.1 and above
* Composer

### Installing

Git clone or unzip the project somewhere on your local machine.
Open terminal and move to project root directory.

Install dependencies
```
composer install
```

Prepare autoloader
```
composer dump-autoload -o
```

Call example
```
php solution.php 2017-11-20T09:30 2017-11-23T19:30 3
```

Output example
```
[
    {
        "product_id": 154,
        "available_starttimes": [
            "2017-11-22T08:00"
        ]
    },
    {
        "product_id": 177,
        "available_starttimes": [
            "2017-11-23T12:15"
        ]
    },
    {
        "product_id": 215,
        "available_starttimes": [
            "2017-11-20T20:15"
        ]
    },
    {
        "product_id": 782,
        "available_starttimes": [
            "2017-11-21T22:45"
        ]
    },
    {
        "product_id": 925,
        "available_starttimes": [
            "2017-11-22T15:00"
        ]
    }
]
```
## Running the tests

Move to directory 
```
/vendor/bin
```
Execute
```
phpunit --bootstrap ../autoload.php ../../tests
```


## Built With

* [Guzzle](https://github.com/guzzle/guzzle) - Extensible PHP HTTP client

## Authors

* **Tigran Ghabuzyan**  - [GhTigran](https://github.com/GhTigran)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
