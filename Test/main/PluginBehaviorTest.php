<?php

/**
 * PluginTemplate tests. Copyright (C) 2025 Your Name <your@email.com>.
 * Licensed under the GNU Lesser General Public License, version 3 or later.
 */

namespace FacturaScripts\Test\Plugins;

use FacturaScripts\Core\Session;
use FacturaScripts\Core\Tools;
use FacturaScripts\Dinamic\Model\User;
use FacturaScripts\Plugins\PluginTemplate\Controller\ExampleController;
use FacturaScripts\Plugins\PluginTemplate\Controller\ListExampleModel;
use FacturaScripts\Plugins\PluginTemplate\Init;
use FacturaScripts\Plugins\PluginTemplate\Model\ExampleModel;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class PluginBehaviorTest extends TestCase
{
    public function testModelValidatesTrimsAndResetsFields(): void
    {
        $model = new ExampleModel();
        self::assertFalse($model->test());
        $model->name = '   Example record   ';
        $model->updated_at = '2000-01-01 00:00:00';
        self::assertTrue($model->test());
        self::assertSame('Example record', $model->name);
        self::assertNotSame('2000-01-01 00:00:00', $model->updated_at);
        self::assertSame('id', $model::primaryColumn());
        self::assertSame('example_table', $model::tableName());
        $model->active = false;
        $model->clear();
        self::assertTrue($model->active);
        self::assertNull($model->name);
        Tools::log()->clear();
    }

    public function testLifecycleAndExampleControllerExecute(): void
    {
        $originalUser = Session::get('user');
        $this->expectOutputString('');
        $init = new Init();
        $init->init();
        $init->update();
        $init->uninstall();
        $page = new class ('ExampleController') extends ExampleController {
            protected function auth(): bool
            {
                $user = new User();
                $user->nick = 'admin';
                $user->admin = true;
                Session::set('user', $user);
                return true;
            }
        };
        try {
            $page->run();
        } finally {
            Session::set('user', $originalUser);
        }
        self::assertSame('admin', $page->getPageData()['menu']);
        self::assertSame('example-controller', $page->getPageData()['title']);
    }

    public function testListControllerBuildsModelView(): void
    {
        if (!class_exists('FacturaScripts\\Dinamic\\Model\\ExampleModel')) {
            class_alias(ExampleModel::class, 'FacturaScripts\\Dinamic\\Model\\ExampleModel');
        }
        $page = new ListExampleModel('ListExampleModel');
        self::assertSame('example-models', $page->getPageData()['title']);
        $method = new ReflectionMethod($page, 'createViews');
        $method->setAccessible(true);
        $method->invoke($page);
        self::assertArrayHasKey('ListExampleModel', $page->views);
        self::assertInstanceOf(ExampleModel::class, $page->views['ListExampleModel']->model);
    }
}
