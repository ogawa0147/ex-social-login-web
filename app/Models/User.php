<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function forge()
    {
        return new static();
    }

    /**
     * 複数SNSアカウント取得
     * 1対多のリレーション
     *
     * @param
     * @return App\Models\SocialAccount
     */
    public function accounts()
    {
        return $this->hasMany('App\Models\UserSocialAccount');
    }

    /**
     * リレーション
     *
     * @param
     * @return App\Models\UserProfile
     */
    public function profile()
    {
        return $this->hasOne('App\Models\UserProfile');
    }

    /**
     * リレーション
     *
     * @param
     * @return App\Models\UserActivation
     */
    public function activation()
    {
        return $this->hasOne('App\Models\UserActivation');
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
