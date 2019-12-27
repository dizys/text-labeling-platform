<?php


namespace app\api\controller;


use think\Controller;

class Dataset extends Controller
{
    protected $middleware = [
        'APIAuth',
    ];

    public function getList(\app\base\model\User $user)
    {
        $labeled = input('get.labeled/d', 0) !== 0;
        $page = input('get.page/d', 1);
        $perPage = input('get.per_page/d', 15);

        if ($page === 1) {
            $itemCount = \app\base\model\Dataset::buildListQuery($labeled, $user->id)->count();
            if ($itemCount === 0) {
                $totalPage = 0;
            } else {
                $totalPage = ceil($itemCount / $perPage);
            }
        } else {
            $itemCount = null;
            $totalPage = null;
        }

        $list = \app\base\model\Dataset::buildListQuery($labeled, $user->id)
                                       ->page($page, $perPage)->select();

        return success([
            'total_count' => $itemCount,
            'total_page' => $totalPage,
            'list' => $list,
        ]);
    }

    public function label(\app\base\model\User $user)
    {
        $itemId = input('post.item_id/d');
        $labels = input('post.labels');

        if (!$itemId) {
            return failure('ID不能为空');
        }

        $item = \app\base\model\Dataset::get($itemId);

        if (!$item) {
            return failure('保存失败,找不到该条数据');
        }

        $item->labels = $labels;
        $item->label_time = time();
        $item->user_id = $user->id;
        $item->save();

        $user->setInc('total_count');
        $user->recordToday();
        $user->save();

        return success();
    }

}