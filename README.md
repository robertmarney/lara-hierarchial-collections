# Transforms flat collections to a nested hierarchy

[![Latest Version on Packagist](https://img.shields.io/packagist/v/robertmarney/lara-hierarchial-collections.svg?style=flat-square)](https://packagist.org/packages/robertmarney/lara-hierarchial-collections)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/robertmarney/lara-hierarchial-collections/run-tests?label=tests)](https://github.com/robertmarney/lara-hierarchial-collections/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/robertmarney/lara-hierarchial-collections/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/robertmarney/lara-hierarchial-collections/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/robertmarney/lara-hierarchial-collections.svg?style=flat-square)](https://packagist.org/packages/robertmarney/lara-hierarchial-collections)

Package to extend collections of hierarchical data to organize data into nodes according to the hierarchy.

The package supports unlimited starting nodes, and has been tested to 10 levels deep.

### Use Cases:

* Organizational Charts
* Chart of Accounts

## Requirements:

* Illuminate Collections 8+/9+ (Packaged in Laravel 8+/9+)
* PHP 8.0 / 8.1


## Installation

You can install the package via composer:

```bash
composer require robertmarney/lara-hierarchial-collections
```


## Basic Usage,

The tool accepts Support Collections or Eloquent Collections, within the collection we expect Eloquent Models or `StdClass` objects.

Assuming a primary key of `id` and parent identifier of `parent_id`:

```php
$laraHierarchy = new RCM\LaraHierarchy\LaraHierarchy();

$collection = User::select(['id', 'parent_id', 'name'])->get();

$hierarchy = $laraHierarchy->collectionToHierarchy($collection)->toArray();

// Result:

[
    'id' => 1,
    'parent_id' => null,
    'name' => 'John Doe'
    'children' => [
        [
            'id' => 1000,
            'parent_id' => 1,
            'name' => 'Sue Smith'
            'children' => [//...]
        ],
        //...
    ]               
]
```
### Customizing Local Key:

If you are not using ids (eg uuid) you can override the local comparison value:

```php
$hierarchy = $laraHierarchy->collectionToHierarchy($collection, localIdentifier: 'custom_primary_key')
```

### Customizing Parent Key:

Similiarly, you can change the parent key if the local relationship is not formed on the default `parent_id`

```php
$hierarchy = $laraHierarchy->collectionToHierarchy($collection, parentIdentifier: 'custom_parent_id')
```

### Providing the `relationName` property will change the collection name where children will be placed

```php
$hierarchy = $laraHierarchy->collectionToHierarchy($collection, relationName: 'descendants')->toArray();

// Result:

[
    'id' => 1,
    'parent_id' => null,
    'name' => 'John Doe'
    'descendants' => [
        [
            'id' => 1000,
            'parent_id' => 1,
            'name' => 'Sue Smith'
            'descendants' => [//...]
        ],
        //...
    ]               
]
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Credits

- [Robert Marney](https://github.com/robertmarney)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
