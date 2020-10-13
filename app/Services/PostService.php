<?php


namespace App\Services;


use App\Contracts\Repositories\PostRepository;
use App\Contracts\Services\IPostService;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class PostService implements IPostService
{
    private $postRepository;

    /**
     * PostService constructor.
     * @param $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * 新增
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request)
    {
        return $this->postRepository->create($request->all());
    }

    public function show($id)
    {
        return $this->postRepository->find($id);
    }

    public function search(Request $request)
    {
        return $this->postRepository->filter($request->all())->paginate(15);
    }
}
