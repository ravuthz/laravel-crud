# Laravel Crud Trait and Service for API

## Install Package via Composer

```bash
composer require ravuthz/laravel-crud
```


## Usage

### Generate Crud Controller and Test
```bash
php artisan crud --help

# Generate crud model, controller, test, request, resource
php artisan crud:generate Post --test

# Generate only crud controller
php artisan crud:controller Post

# Generate only crud controller test
php artisan crud:controller-test Post --test
```

### Use by follow sample PostController and PostControllerTest

First create: 

- Create controller model, and migration via `php artisan make:model Post -mc` command
- Create request via `php artisan make:request PostRequest` command
- Create resource via `php artisan make:resource PostResource` command

Then you can use the following sample code to make it as crud controller and test.

PostController.php
```php
<?php

namespace App\Http\Controllers\Api;

use App\Crud\CrudController;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;

class PostController extends CrudController
{
    protected $model = Post::class;
    protected $storeRequest = PostRequest::class;
    protected $updateRequest = PostRequest::class;
    protected $resource = PostResource::class;

    protected function beforeSave($request, $model, $id = null)
    {
        // Override this method to add custom logic before saving
    }

    protected function afterSave($request, $model, $id = null)
    {
        // Override this method to add custom logic after saving
    }

}
```


PostControllerTest.php
```php
<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Crud\HasCrudControllerTest;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use HasCrudControllerTest;

    protected static bool $login = true;
    protected string $route = 'api/posts';

    protected function setUp(): void
    {
        parent::setUp();
        $this->onceSetUp();
    }

    protected function inputBody($id = null): array
    {
        $time = now()->format('Y-m-d_H:m:s.u');
        // some related data, attachment with some unique value with time here
        
        abort(500, 'Please implement inputBody method');

        if (!empty($id)) {
            return [
                // payload for test create here
            ];
        }

        return [
            // payload for test update here
        ];
    }
}

```
