<?php
/**
 * This file is part of PluginTemplate plugin for FacturaScripts.
 * Copyright (C) 2025 Your Name <your@email.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace FacturaScripts\Test\Plugins;

use FacturaScripts\Plugins\PluginTemplate\Model\ExampleModel;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for ExampleModel
 *
 * @author Your Name <your@email.com>
 */
final class ExampleModelTest extends TestCase
{
    /**
     * Test that a new model can be created and saved
     */
    public function testCreate(): void
    {
        $model = new ExampleModel();
        $model->name = 'Test Name';
        $model->description = 'Test Description';
        $model->active = true;

        $this->assertTrue($model->save(), 'model-can-not-be-saved');
        $this->assertNotEmpty($model->id, 'model-id-is-empty');

        // Clean up
        $this->assertTrue($model->delete(), 'model-can-not-be-deleted');
    }

    /**
     * Test that model validation works
     */
    public function testValidation(): void
    {
        $model = new ExampleModel();
        $model->name = ''; // Empty name should fail validation
        $model->description = 'Test Description';

        $this->assertFalse($model->save(), 'empty-name-should-not-be-saved');
    }

    /**
     * Test that model can be retrieved from database
     */
    public function testRead(): void
    {
        // Create a test record
        $model = new ExampleModel();
        $model->name = 'Test Read';
        $model->description = 'Test Read Description';
        $this->assertTrue($model->save());

        // Retrieve it
        $retrieved = new ExampleModel();
        $this->assertTrue($retrieved->loadFromCode($model->id));
        $this->assertEquals('Test Read', $retrieved->name);

        // Clean up
        $this->assertTrue($model->delete());
    }

    /**
     * Test that model can be updated
     */
    public function testUpdate(): void
    {
        // Create a test record
        $model = new ExampleModel();
        $model->name = 'Original Name';
        $this->assertTrue($model->save());

        // Update it
        $model->name = 'Updated Name';
        $this->assertTrue($model->save());

        // Verify update
        $retrieved = new ExampleModel();
        $this->assertTrue($retrieved->loadFromCode($model->id));
        $this->assertEquals('Updated Name', $retrieved->name);

        // Clean up
        $this->assertTrue($model->delete());
    }

    /**
     * Test that model can be deleted
     */
    public function testDelete(): void
    {
        // Create a test record
        $model = new ExampleModel();
        $model->name = 'To Be Deleted';
        $this->assertTrue($model->save());
        $id = $model->id;

        // Delete it
        $this->assertTrue($model->delete());

        // Verify deletion
        $retrieved = new ExampleModel();
        $this->assertFalse($retrieved->loadFromCode($id));
    }
}
