<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Define extends Model
{
    use SoftDeletes;

    protected $table = 'defines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
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
     * データ取得 and キャッシュ保存
     *
     * @param String $key
     * @return Object
     */
    public static function key($key)
    {
        $store_key = $key;
        $cached = Cache::remember($key, 30, function () use ($store_key) {
            $define = static::where('key', $store_key)->first();
            return json_encode($define);
        });
        return json_decode($cached);
    }

    /**
     * キャッシュ全削除
     *
     * @param
     * @return
     */
    public function clearCacheAll()
    {
        Cache::flush();
    }

    /**
     * キャッシュクリア
     *
     * @param
     * @return
     */
    public function clearCache($key)
    {
        Cache::forget($key);
    }
}
