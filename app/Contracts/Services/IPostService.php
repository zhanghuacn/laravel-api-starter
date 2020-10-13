<?php


namespace App\Contracts\Services;


use App\Repositories\Models\Post;
use Illuminate\Http\Request;

/**
 * 帖子服务
 * Interface IPostService
 * @package App\Contracts\Services
 */
interface IPostService
{
    /**
     * 新增
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request);

    /**
     * 详情
     * @param $id
     * @return mixed
     */
    public function show($id);

    /**
     * 查询
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request);
}
