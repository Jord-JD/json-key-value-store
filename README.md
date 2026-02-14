# JSON Key Value Store

[![Build Status](https://travis-ci.org/Jord-JD/json-key-value-store.svg?branch=master)](https://travis-ci.org/Jord-JD/json-key-value-store)
[![Coverage Status](https://coveralls.io/repos/github/Jord-JD/json-key-value-store/badge.svg?branch=master)](https://coveralls.io/github/Jord-JD/json-key-value-store?branch=master)
[![StyleCI](https://styleci.io/repos/132195531/shield?branch=master)](https://styleci.io/repos/132195531)

A simple JSON based key value store.

## Installation

This JSON Key Value Store package can be easily installed using Composer. Just run the following command from the root of your project.

```
composer require jord-jd/json-key-value-store
```

If you have never used the Composer dependency manager before, head to the [Composer website](https://getcomposer.org/) for more information on how to get started.

## Usage

Using the JSON Key Value Store is designed to be super simple.

Here is a basic usage example:

```php
use JordJD\JsonKeyValueStore\JsonKeyValueStore;

$store = new JsonKeyValueStore('store.json.gz');

$store->set('key1', 'value123');
$store->set('key2', 'value456');
$store->delete('key2');

$value = $store->get('key1');

// $value = 'value123'
```