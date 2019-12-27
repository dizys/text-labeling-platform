<?php


namespace app\base\model;


use think\Model;

/**
 * Class User
 * @package app\base\model
 * @property int    $id
 * @property string $username
 * @property string $password
 * @property int    $today_count
 * @property int    $today_timestamp
 * @property int    $total_count
 * @property int    $create_time
 * @property int    $update_time
 */
class User extends Model
{
    protected $hidden = ['password'];

    public function recordToday()
    {
        $timestamp = $this->getAttr('today_timestamp');

        if (is_timestamp_today($timestamp)) {
            $this->setInc('today_count');
        } else {
            $this->setAttr('today_count', 1);
            $this->today_timestamp = time();
        }
    }
}