<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class StorageTest extends TestCase
{
    const STORAGES = ['skins', 'cloaks', 'heads'];

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testConnect()
    {
        foreach (self::STORAGES as $STORAGE) {
            $storage = \Storage::disk($STORAGE);
            $this->assertNotEmpty($storage);
        }
    }

    public function testOperations()
    {
        $file = 'php_unit_test';

        foreach (self::STORAGES as $STORAGE) {
            $storage = \Storage::disk($STORAGE);

            $content = Str::random(1024);

            if ($storage->exists($file)){
                $storage->delete($file);
            }

            $storage->put($file, $content);

            $this->assertEquals($content, $storage->get($file));

            $storage->delete($file);
        }
    }
}
