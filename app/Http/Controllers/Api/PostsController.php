<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\IPostService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Repositories\Models\Post;
use Illuminate\Http\Request;

/**
 * Class PostsController.
 *
 * @package namespace App\Http\Controllers;
 */
class PostsController extends Controller
{
    private $postService;

    /**
     * PostsController constructor.
     * @param $postService
     */
    public function __construct(IPostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(PostRequest $request)
    {
        return $this->response->success(new PostCollection($this->postService->search($request)));
    }

    public function show($id)
    {
        return $this->response->success(new PostResource($this->postService->show($id)));
    }

    public function store(PostRequest $request)
    {
        return $this->response->success($request);
    }
}
