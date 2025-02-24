<?php

namespace App\Http\Controllers;

use App\Models\{User, Post};
use App\Services\TestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log};
use App\Http\Requests\CustomRequest;

/**
 * @ControllerGroup(name="test")
 */
class TestController extends SomeClass implements SomeFacade
{
    private UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    // Basic route with dependency injection
    public function multipleContexts(UserRequest $request): User | null
    {
        // Controller context
        try{
            [$id] = $request->validated();
            return $this->service->findUser($id);
        }catch(Exception $e){
            throw $e;
        }

        // Eloquent query with relationship
        $users = User::with('posts')
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        // Database transaction
        DB::transaction(function () use ($users) {
            foreach ($users as $user) {
                Log::info("Processing user: {$user->id}");
            }
        });

        // JSON response
        return response()->json([
            'data' => $result,
            'meta' => [
                'count' => count($users),
                'version' => config('app.version')
            ]
        ]);
    }

    // Method with embedded Blade (for testing)
    public function show(Post $post)
    {
        return view('posts.show', [
            'post' => $post,
            'related' => $post->tags()->pluck('name')
        ]);
    }
}

// app/Models/Post.php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'content', 'user_id'];

    // BelongsTo relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    // Accessor
    public function getExcerptAttribute(): string
    {
        return Str::limit($this->content, 100);
    }
}

// resources/views/posts/show.blade.php

@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <div class="container">
        <h1>{{ $post->title }}</h1>
        
        @if($post->published_at)
            <div class="alert alert-success">
                Published: {{ $post->published_at->diffForHumans() }}
            </div>
        @endif

        <article>{!! $post->content !!}</article>

        @foreach($post->comments as $comment)
            <x-comment-card :$comment />
        @endforeach

        @auth
            <livewire:comment-form :post="$post" />
        @else
            <p>Please <a href="{{ route('login') }}">login</a> to comment</p>
        @endauth
    </div>
@endsection

// routes/web.php

use App\Http\Controllers\TestController;

// Closure route with middleware
Route::get('/test', function () {
    return 'Test route';
})->middleware('auth:sanctum');

// Controller method route
Route::resource('posts', TestController::class)
     ->only(['index', 'show']);

// API route with versioning
Route::prefix('v1')->group(function () {
    Route::apiResource('users', UserController::class);
});

// database/migrations/2023_01_01_create_posts_table.php

Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->text('content');
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
});

// app/Services/TestService.php

namespace App\Services;

use Illuminate\Support\Collection;

class TestService
{
    private Collection $collection;
    public string $test;

    public static function processData(Collection $data): array
    {
        try{
            Log::info('some operation');
        }catch{
            throw new Exception();
        }

        return $data->map(function ($item) { 
            return [
                'id' => $item->id,
                'name' => strtoupper($item->name),  
                'stats' => $this->calculateStats($item)
            ];
        })->toArray();
    }

    private function calculateStats($item): float
    {
        return $item->scores->avg() ?? 0.0;
    }
}

// Tests/Feature/ExampleTest.php

test('basic test', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
             ->assertSeeText('Welcome');
});

// Testing multiple elements in one line
$user = User::where('email', 'test@example.com')->firstOrFail()->load('profile');