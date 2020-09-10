<?php

namespace Aminetiyal\Sluggable\Tests;

use File;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /** @var \Aminetiyal\Sluggable\Tests\TestModel */
    protected $testModel;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    /**
     * @param  $app
     */
    protected function setUpDatabase(Application $app)
    {
        $app['config']->set('database.default', 'testing_database');
        $app['config']->set('database.connections.testing_database', [
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);

        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('other_field')->nullable();
            $table->string('url')->nullable();
        });

        $app['db']->connection()->getSchemaBuilder()->create('unique_with_test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('slug')->nullable();
        });

        $app['db']->connection()->getSchemaBuilder()->create('test_model_soft_deletes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('other_field')->nullable();
            $table->string('url')->nullable();
            $table->softDeletes();
        });

        $app['db']->connection()->getSchemaBuilder()->create('translatable_models', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name')->nullable();
            $table->text('other_field')->nullable();
            $table->text('non_translatable_field')->nullable();
            $table->text('slug')->nullable();
        });
    }
}
