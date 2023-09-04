<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Routing\Route;
use League\Fractal\TransformerAbstract;

use function Spatie\Snapshots\assertMatchesSnapshot;

test('response()->noContent() call support', function () {
    \Illuminate\Support\Facades\Route::get('api/test', [Foo_Test::class, 'index']);

    \Dedoc\Scramble\Scramble::routes(fn (Route $r) => $r->uri === 'api/test');
    $openApiDocument = app()->make(\Dedoc\Scramble\Generator::class)();

    assertMatchesSnapshot($openApiDocument);
});
class Foo_Test
{
    public function index()
    {
        return response()->noContent();
    }
}

test('response()->json() call support with phpdoc help', function () {
    \Illuminate\Support\Facades\Route::get('api/test', [Foo_TestTwo::class, 'index']);

    \Dedoc\Scramble\Scramble::routes(fn (Route $r) => $r->uri === 'api/test');
    $openApiDocument = app()->make(\Dedoc\Scramble\Generator::class)();

    assertMatchesSnapshot($openApiDocument);
});
class Foo_TestTwo
{
    public function index()
    {
        return response()->json([
            /** @var array{msg: string, code: int} */
            'error' => $var,
        ], 500);
    }
}

test('multiple responses support', function () {
    \Illuminate\Support\Facades\Route::get('api/test', [Foo_TestThree::class, 'index']);

    \Dedoc\Scramble\Scramble::routes(fn (Route $r) => $r->uri === 'api/test');
    $openApiDocument = app()->make(\Dedoc\Scramble\Generator::class)();

    assertMatchesSnapshot($openApiDocument);
});
class Foo_TestThree
{
    public function index()
    {
        try {
            something_some();
        } catch (\Throwable $e) {
            return response()->json([
                /** @var array{msg: string, code: int} */
                'error' => $var,
            ], 500);
        }
        if ($foo) {
            return response()->json(['foo' => 'one']);
        }

        return response()->json(['foo' => 'bar']);
    }
}

test('manually annotated responses support', function () {
    \Illuminate\Support\Facades\Route::get('api/test', [Foo_TestFour::class, 'index']);

    \Dedoc\Scramble\Scramble::routes(fn (Route $r) => $r->uri === 'api/test');
    $openApiDocument = app()->make(\Dedoc\Scramble\Generator::class)();

    assertMatchesSnapshot($openApiDocument);
});
class Foo_TestFour
{
    public function index()
    {
        if ($foo) {
            /**
             * Advanced comment.
             *
             * With more description.
             *
             * @status 201
             *
             * @body array{foo: string}
             */
            return response()->json(['foo' => 'one']);
        }

        // Simple comment.
        return response()->json(['foo' => 'bar']);
    }
}

test('laravel responder success support', function () {
    \Illuminate\Support\Facades\Route::get('api/test', [Foo_TestFive::class, 'index']);

    \Dedoc\Scramble\Scramble::routes(fn (Route $r) => $r->uri === 'api/test');
    $openApiDocument = app()->make(\Dedoc\Scramble\Generator::class)();

    assertMatchesSnapshot($openApiDocument);
});
class Foo_TestFive
{
    public function index()
    {
        $user = [];
        return responder()->success($user, UserTransformer::class)->respond(201, [
            'Authorization' => 'Bearer ghjkiuygvbnmkuyg'
        ]);
    }
}

/**
 * @mixin User
 */
class UserTransformer extends TransformerAbstract
{
    public function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'testString' => 'iasdihas',
            'testNumber' => 2333
        ];
    }
}

test('laravel responder error support', function () {
    \Illuminate\Support\Facades\Route::get('api/test', [Foo_TestSix::class, 'index']);

    \Dedoc\Scramble\Scramble::routes(fn (Route $r) => $r->uri === 'api/test');
    $openApiDocument = app()->make(\Dedoc\Scramble\Generator::class)();

    assertMatchesSnapshot($openApiDocument);
});

class Foo_TestSix
{
    public function index()
    {
        $user = [];
        return responder()->error($user, UserTransformer::class)->respond(404, [
            'Authorization' => 'Bearer ghjkiuygvbnmkuyg'
        ]);
    }
}
