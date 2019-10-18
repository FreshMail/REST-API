# FreshMail

A php library which implements the functionality of FreshMail REST API.

This API client covers all functions of API V2 such as:
 - subscribers management
 - list management
 - campaign management
 - sending transactional SMS messages
 - sending transactional mail messages (in a legacy way)

If You want to send transactional messages in rich format please use new [API V3 client](https://github.com/FreshMail/php-api-client).

## Installation via composer

Add to `composer.json` file:

    {
        "require": {
            "freshmail/rest-api": "dev-master"
        }
    }

Use in php project:

    use FreshMail\RestApi as FmRestApi;

## Installation by hand

    require_once 'class.rest.php';
    require_once 'config.php';

## Examples

All samples included in samples directory.

## Thanks to
@adam187 
