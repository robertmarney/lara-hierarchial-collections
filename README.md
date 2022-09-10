# Transforms flat collections to a nested hierarchy

[![Latest Version on Packagist](https://img.shields.io/packagist/v/robertmarney/lara-hierarchial-collections.svg?style=flat-square)](https://packagist.org/packages/robertmarney/lara-hierarchial-collections)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/robertmarney/lara-hierarchial-collections/run-tests?label=tests)](https://github.com/robertmarney/lara-hierarchial-collections/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/robertmarney/lara-hierarchial-collections/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/robertmarney/lara-hierarchial-collections/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/robertmarney/lara-hierarchial-collections.svg?style=flat-square)](https://packagist.org/packages/robertmarney/lara-hierarchial-collections)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.



## Installation

You can install the package via composer:

```bash
composer require robertmarney/lara-hierarchial-collections
```

Y

## Basic Usage,

Assuming a primary key of `id` and parent identifier of `parent_id`:

```php
$laraHierarchy = new RCM\LaraHierarchy();

$collection = User::select('id', 'parent_id', 'name')->get();

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
            'children' => [...]
        ],
        ...
    ]               
]
```
### Customizing Local Key:

If you are not using ids (eg uuid) you can override the local comparison value:

```php
$hierarchy = $laraHierarchy->collectionToHierarchy($collection, localIdentifier: 'custom_primary_key')
```

### Customizing Parent Key:

Similiarly,you can change the parent key

```php
$hierarchy = $laraHierarchy->collectionToHierarchy($collection, parentIdentifier: 'custom_parent_id')
```

### Changing the children property

```php
$hierarchy = $laraHierarchy->collectionToHierarchy($collection, relationName: 'descendants')
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
