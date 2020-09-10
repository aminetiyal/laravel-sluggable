<?php

namespace Aminetiyal\Sluggable\Tests;

use Illuminate\Support\Str;
use Aminetiyal\Sluggable\SlugOptions;

class UniqueWithTestModelTest extends TestCase
{
    /** @test */
    public function it_can_be_unique_with_other_fields()
    {
        $model = UniqueWithTestModel::create(['name' => 'this is a test', 'type' => 'test']);
        $this->assertEquals("this-is-a-test", $model->slug);

        $model = UniqueWithTestModel::create(['name' => 'this is a test', 'type' => 'test']);
        $this->assertEquals("this-is-a-test-1", $model->slug);

        $model = UniqueWithTestModel::create(['name' => 'this is a test']);
        $this->assertEquals("this-is-a-test", $model->slug);

        $model = UniqueWithTestModel::create(['name' => 'this is a test']);
        $this->assertEquals("this-is-a-test-1", $model->slug);
    }
}
