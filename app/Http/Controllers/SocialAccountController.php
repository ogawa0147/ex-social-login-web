<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocialAccount;

class SocialAccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:user');
    }

    /**
     * ログイン処理
     *
     * @param String $provider [e.g. facebook]
     * @return Redirect
     */
    public function redirectToProvider($provider)
    {
        return Socialite::with($provider)->redirect();
    }

    /**
     * コールバック処理
     *
     * https://github.com/laravel/socialite
     * https://socialiteproviders.github.io/
     *
     * @param SocialAccountService $service
     * @param String $provider [e.g. facebook]
     * @return Redirect
     */
    public function handleProviderCallback($provider)
    {
        try
        {
            $provider_user = Socialite::with($provider)->user();
        }
        catch (\Exception $e)
        {
            return redirect('/logout');
        }

        // provider_id, signup_type 保存
        \Session::put('provider_id', $provider_user->getId());

        // 現在のプロバイダーIDに関連したSNSアカウント取得
        $user_social_account = UserSocialAccount::find(['provider_name' => $provider, 'provider_id' => $provider_user->getId()]);
        if ($user_social_account)
        {
            // 利用規約画面で承認しているかどうか
            if ($user_social_account->agreed == UserSocialAccount::AGREED_OK)
            {
                // 承認済みでUserデータが存在する場合
                if ($user_social_account->user)
                {
                    // ログイン リダイレクト
                    auth()->login($user_social_account->user, true);
                    return redirect()->to('/home');
                }
            }
        }
        else
        {
            // UserSocialAccount 作成
            $user_social_account = UserSocialAccount::create([
                'provider_id'   => $provider_user->getId(),
                'provider_name' => $provider,
                'email'         => $provider_user->getEmail(),
                'name'          => $provider_user->getName(),
            ]);
        }

        return redirect()->to('/signup/social');
    }

    private function getConfig($provider)
    {
        try
        {
            $client_id = \Config::get('services.' . $provider . '.client_id');
            $client_secret = \Config::get('services.' . $provider . '.client_secret');
            $redirect_url = \Config::get('services.' . $provider . '.redirect');
            $additional = ['site' => 'meta.stackoverflow.com'];
            return new \SocialiteProviders\Manager\Config($client_id, $client_secret, $redirect_url, $additional);
        }
        catch (\Exception $e)
        {
            return null;
        }
    }
}
