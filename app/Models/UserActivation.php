<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserActivation extends Model
{
    use SoftDeletes;

    protected $table = 'user_activations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'email',
        'expires_in',
        'status',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expires_in',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [];

    const STATUS_INIT = 0;
    const STATUS_MAIL_SEND = 1;
    const STATUS_ACTIVATION_COMPLETE = 2;
    const STATUS_EXPIRES_IN = 5;

    const STRING_ACTIVATE_TOKEN = '__ACTIVATE_TOKEN__';

    /**
     * リレーション
     *
     * @param
     * @return App\Models\User
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public static function forge()
    {
        return new static();
    }

    /**
     * データ取得
     *
     * @param Array 
     * @return Object
     */
    public static function find(array $attributes = [])
    {
        return static::where($attributes)->first();
    }

    /**
     * データ作成
     *
     * @param Array 
     * @return Object
     */
    public static function create(array $attributes = [])
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    /**
     * 作成 or 更新
     *
     * @param Array
     * @return Object
     */
    public function createOrUpdate(array $attributes = [])
    {
        if ($this->exists)
        {
            $this->fill($attributes)->save();
            $model = $this;
        }
        else
        {
            $model = new static($attributes);
            $model->save();
        }
        return $model;
    }

}
