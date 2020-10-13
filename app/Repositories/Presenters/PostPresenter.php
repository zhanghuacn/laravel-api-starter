<?php

namespace App\Repositories\Presenters;

use App\Transformers\PostTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class PostPresenter.
 *
 * @package namespace App\Presenters;
 */
class PostPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new PostTransformer();
    }
}
