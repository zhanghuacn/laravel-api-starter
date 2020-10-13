<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\IPostService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostRequest;
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
        return $this->response->success($request->all());
    }

    public function show($id)
    {
        $post = $this->postService->show($id);
        return $this->response->success($post);
    }

    public function store(PostRequest $request)
    {
        return $this->response->success($request);
    }
}
