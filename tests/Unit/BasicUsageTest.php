<?php

namespace JordJD\JsonKeyValueStore\Tests;

use JordJD\JsonKeyValueStore\JsonKeyValueStore;
use PHPUnit\Framework\TestCase;

final class BasicUsageTest extends TestCase
{
    private function createNewStore()
    {
        $file = __DIR__.'/Data/store.json.gz';

        if (file_exists($file)) {
            unlink($file);
        }

        return new JsonKeyValueStore($file);
    }

    private function createPopulatedStore()
    {
        $store = $this->createNewStore();

        $store->set('testString', 'stringValue');
        $store->set('testInteger', 12345);

        $testObj = new \stdClass();
        $testObj->name = 'Bob';
        $testObj->job = 'Web Developer';
        $testObj->pet = new \stdClass();
        $testObj->pet->type = 'cat';
        $testObj->pet->name = 'Destructor';

        $store->set('testObject', $testObj);

        return $store;
    }

    public function testCreateEmptyStore()
    {
        $store = $this->createNewStore();

        $this->assertFileExists($store->getFile());
        $this->assertEquals('{}', gzdecode(file_get_contents($store->getFile())));
    }

    public function testSetString()
    {
        $store = $this->createNewStore();

        $store->set('testString', 'stringValue');

        $this->assertEquals('{"testString":"stringValue"}', gzdecode(file_get_contents($store->getFile())));
    }

    public function testSetInteger()
    {
        $store = $this->createNewStore();

        $store->set('testInteger', 12345);

        $this->assertEquals('{"testInteger":12345}', gzdecode(file_get_contents($store->getFile())));
    }

    public function testSetObject()
    {
        $store = $this->createNewStore();

        $testObj = new \stdClass();
        $testObj->name = 'Bob';
        $testObj->job = 'Web Developer';
        $testObj->pet = new \stdClass();
        $testObj->pet->type = 'cat';
        $testObj->pet->name = 'Destructor';

        $store->set('testObject', $testObj);

        $this->assertEquals('{"testObject":{"name":"Bob","job":"Web Developer","pet":{"type":"cat","name":"Destructor"}}}', gzdecode(file_get_contents($store->getFile())));
    }

    public function testGetString()
    {
        $store = $this->createPopulatedStore();

        $value = $store->get('testString');

        $this->assertIsString($value);
        $this->assertEquals('stringValue', $value);
    }

    public function testGetInteger()
    {
        $store = $this->createPopulatedStore();

        $value = $store->get('testInteger');

        $this->assertIsInt($value);
        $this->assertEquals(12345, $value);
    }

    public function testGetObject()
    {
        $store = $this->createPopulatedStore();

        $value = $store->get('testObject');

        $testObj = new \stdClass();
        $testObj->name = 'Bob';
        $testObj->job = 'Web Developer';
        $testObj->pet = new \stdClass();
        $testObj->pet->type = 'cat';
        $testObj->pet->name = 'Destructor';

        $this->assertIsObject($value);
        $this->assertEquals($testObj, $value);
    }

    public function testGetNonExistantValue()
    {
        $store = $this->createPopulatedStore();

        $value = $store->get('testNonexistantValue');

        return $this->assertNull($value);
    }

    public function testDeletion()
    {
        $store = $this->createPopulatedStore();

        $store->delete('testString');
        $value = $store->get('testString');

        return $this->assertNull($value);
    }
}
