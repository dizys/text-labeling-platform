<?php


namespace app\index\controller;


use app\base\model\Dataset;
use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
use think\Controller;

class Label extends Controller
{
    protected $middleware = [
        'IndexAuth',
    ];

    public function home(\app\base\model\User $user)
    {
        $this->assign('username', $user->username);

        if (is_timestamp_today($user->today_timestamp)) {
            $today_count = $user->today_count ?: 0;
        } else {
            $today_count = 0;
        }
        $total_count = $user->total_count ?: 0;

        $this->assign('today_count', $today_count);
        $this->assign('total_count', $total_count);

        $team_count = Dataset::buildListQuery(true)->count();
        $project_total = Dataset::count('*');
        $this->assign('team_count', $team_count);
        $this->assign('project_total', $project_total);

        return $this->fetch();
    }

    public function labeling(\app\base\model\User $user)
    {
        $itemId = input('get.id/d');

        if (!$itemId) {
            $item = Dataset::buildListQuery(false)->find();

            if (!$item) {
                $this->success('标注全部完成啦', url('index/label/home'));
            } else {
                $this->redirect(url('index/label/labeling') . "?id=$item->id");
            }

            return;
        }

        /**
         * @var Dataset $item
         */
        $item = Dataset::get($itemId);

        if (!$item) {
            $this->error('找不到对应标注文本', url('index/label/home'));
            return;
        }

        $this->assign('item_id', $itemId);
        $this->assign('title', "#{$itemId}标注 - 文本关键词标注系统");
        $this->assign('labeled', !!$item->labels);

        Jieba::init();
        Finalseg::init();

        $metaLabels = \app\base\model\Label::select();

        $this->assign('data', json_encode([
            'content' => $item->content,
            'labels' => $item->labels ?: null,
            'content_segments' => Jieba::cut($item->content),
            'metaLabels' => $metaLabels,
        ]));

        return $this->fetch();
    }

    public function test()
    {
        Jieba::init();
        Finalseg::init();
        $list = Jieba::cut('巴基斯坦西南部在当地时间24日下午4时发生规模7.7强震，这次地震连印度首都新德里都能感受到摇晃，此次强震造成许多建筑物损毁，至少93死、200人多人受伤。此外，还导致南部海岸冒出一座"前所未见"的小岛！', true);
        dump($list);

        return 'ok';
    }
}