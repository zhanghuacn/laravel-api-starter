<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class PostRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
            {
                return [
                    'id' => ['required,exists:posts,id']
                ];
            }
            case 'POST':
            {
                return [
                    'title' => ['required', 'max:32', 'unique:posts,title'],
                ];
            }
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
            default:
            {
                return [

                ];
            }
        }
    }

    public function messages()
    {
        return [
            'id.required' => '帖子ID必须填写',
            'id.exists' => '帖子不存在',
            'title.unique' => '帖子名已经存在',
            'title.required' => '帖子名不能为空',
            'title.max' => '帖子名最大长度为32个字符',
        ];
    }
}
