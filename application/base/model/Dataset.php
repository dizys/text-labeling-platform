<?php


namespace app\base\model;


use think\Model;

/**
 * Class Dataset
 * @package app\base\model
 * @property int         $id
 * @property string      $content
 * @property string|null $labels
 * @property int|null    $user_id
 * @property int|null    $label_time
 * @property int         $create_time
 * @property int         $update_time
 */
class Dataset extends Model
{
    public function user()
    {
        return $this->belongsTo('User');
    }

    public static function buildListQuery(bool $labeled, int $userId = null)
    {
        $query = self::with('user')
                     ->where($labeled ? "labels IS NOT NULL AND labels <> ''" : "labels IS NULL OR labels = ''");

        if ($labeled && $userId) {
            $query = $query->where('user_id', $userId);
        }

        return $query->order('label_time', 'desc')
                     ->order('update_time', 'desc')
                     ->order('id', 'desc');
    }
}