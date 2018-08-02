<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Crypt;
use Carbon\Carbon;
use App\Models\Define;
use App\Models\User;
use App\Models\UserActivation;
use App\Models\UserSocialAccount;
use App\Helpers\MailService;

class SignupController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $signup_type = \Session::get('signup_type');
        switch ($signup_type)
        {
            case 'general':
                return view('signup.index')->with([
                    'name'        => '',
                    'email'       => '',
                    'signup_type' => $signup_type,
                ]);

            case 'social':
                $provider_id = \Session::get('provider_id');
                $user_social_account = UserSocialAccount::find(['provider_id' => $provider_id]);
                return view('signup.index')->with([
                    'name'        => $user_social_account->name,
                    'email'       => $user_social_account->email,
                    'signup_type' => $signup_type,
                ]);
        }
    }

    /**
     * 通常ルート
     *
     * @param
     * @return Redirect
     */
    public function general()
    {
        \Session::put('signup_type', 'general');
        return redirect()->to('/signup/agreement');
    }

    /**
     * SNSルート
     *
     * @param
     * @return Redirect
     */
    public function social()
    {
        \Session::put('signup_type', 'social');

        $provider_id = \Session::get('provider_id');
        $user_social_account = UserSocialAccount::find(['provider_id' => $provider_id]);

        // 利用規約画面で承認しているかどうか
        if ($user_social_account->agreed == UserSocialAccount::AGREED_NG)
        {
            return redirect()->to('/signup/agreement');
        }

        return redirect()->to('/signup');
    }

    /**
     * 利用規約に同意しているかチェック
     *
     * @param
     * @return Redirect
     */
    public function agreed()
    {
        $signup_type = \Session::get('signup_type');
        switch ($signup_type)
        {
            case 'general':
                return redirect()->to('/signup');

            case 'social':
                $provider_id = \Session::get('provider_id');
                $user_social_account = UserSocialAccount::find(['provider_id' => $provider_id]);
                if (!$user_social_account)
                {
                    return redirect()->to('/login');
                }
                $user_social_account->fill(['agreed' => UserSocialAccount::AGREED_OK])->save();
                return redirect()->to('/signup');
        }
    }

    /**
     * 利用規約・プライバシーポリシー
     *
     * @param
     * @return View
     */
    public function agreement()
    {
        $view = view('signup.agreement');

        // $policy = Define::key('signup.mail.policy')->value;
        // $terms = Define::key('signup.mail.terms')->value;
        $agreement = '利用規約とプライバシーポリシーの中身';

        $view->with('agreement', $agreement);
        return $view;
    }

    /**
     * SNS 情報登録
     *
     * @param Request $request
     * @return Redirect
     */
    public function registerToSocial(Request $request)
    {
        // バリデーションチェック
        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        $name = $request->input('name');
        $email = $request->input('email');

        $provider_id = \Session::get('provider_id');
        $user_social_account = UserSocialAccount::find(['provider_id' => $provider_id]);

        // User 作成
        $user = User::create([
            'name'  => $name,
            'email' => $email,
        ]);

        // UserProfile 作成
        $user->profile()->create([
            'user_id' => $user->id,
        ]);

        // UserSocialAccount 更新
        $user_social_account->fill(['user_id' => $user->id])->save();

        auth()->login($user, true);

        return redirect()->to('/home');
    }

    /**
     * 登録
     * 通常の場合はメールを送信
     * SNSの場合は登録
     *
     * @param Request $request
     * @return Redirect
     */
    public function sendmail(Request $request)
    {
        // バリデーションチェック
        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        $email = $request->input('email');

        $subject = Define::key('signup.mail.subject')->value;
        $body = Define::key('signup.mail.body')->value;
        $expire_seconds = Define::key('signup.mail.expire.seconds')->value;

        // 保存データ
        $params = [
            'token'      => Crypt::encrypt($email),
            'email'      => $email,
            'expires_in' => Carbon::now()->addSecond($expire_seconds),
            'status'     => UserActivation::STATUS_MAIL_SEND,
        ];

        // 作成 or 更新
        $activation = UserActivation::find(['email' => $email]);
        if ($activation)
        {
            $activation->fill($params)->save();
        }
        else
        {
            $activation = UserActivation::create($params);
        }

        // 本文の加工
        $body = str_replace(UserActivation::STRING_ACTIVATE_TOKEN, $activation->token, $body);

        // メール送信
        MailService::forge()->from(MailService::FROM_INFO)
                            ->to($email)
                            ->subject($subject)
                            ->with($body)
                            ->send();

        // email,token保存
        \Session::put('email', $activation->email);
        \Session::put('token', $activation->token);

        return redirect('/signup/sentmail');
    }

    /**
     * メール送信後
     *
     * @param
     * @return View
     */
    public function sentmail()
    {
        $email = \Session::get('user_activations.email');
        $view = view('signup.sentmail');
        $view->with('email', $email);
        return $view;
    }

    /**
     * アクティベート画面
     *
     * @param Request $request
     * @return View
     */
    public function activate(Request $request)
    {
        $view = view('signup.activate');
        $view->with(['message' => '']);

        $token = $request->get('token');

        // tokenが存在しない
        if (!$token)
        {
            return $view->with([
                'message' => \Lang::get('errors.ERROR00001'),
            ]);
        }

        $activation = UserActivation::find(['token' => $token]);

        // tokenに紐づくデータが存在しない
        if (!$activation->exists)
        {
            return $view->with([
                'message' => \Lang::get('errors.ERROR00001'),
            ]);
        }

        // アクティベート済み
        if ($activation->status == UserActivation::STATUS_ACTIVATION_COMPLETE)
        {
            return $view;
        }

        // 期限切れチェック
        if (!$activation->expires_in->isFuture() || $activation->status == UserActivation::STATUS_EXPIRES_IN)
        {
            // 期限切れ更新
            $activation->fill(['status' => UserActivation::STATUS_EXPIRES_IN])->save();

            return $view->with([
                'message' => \Lang::get('errors.ERROR00002'),
            ]);
        }

        // アクティベート済み更新
        $activation->fill(['status' => UserActivation::STATUS_ACTIVATION_COMPLETE])->save();

        return $view;
    }

    /**
     * 通常ルート情報登録
     *
     * @param Request $request
     * @return View
     */
    public function registerToGeneral(Request $request)
    {
        $view = view('signup.register_general');

        // POST
        if ($request->isMethod('post'))
        {
            // バリデーションチェック
            $this->validate($request, [
                'name'     => 'required|string|max:255',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $name = $request->input('name');
            $password = $request->input('password');

            $token = \Session::get('token');

            // tokenが存在しない
            if (!$token)
            {
                return $view->with([
                    'message' => \Lang::get('errors.ERROR00001'),
                ]);
            }

            $activation = UserActivation::find(['token' => $token]);

            // tokenに紐づくデータが存在しない
            if (!$activation)
            {
                return $view->with([
                    'message' => \Lang::get('errors.ERROR00001'),
                ]);
            }

            // メールアドレスからUser取得
            $user = User::find(['email' => $activation->email]);

            // userが存在する
            if ($user)
            {
                return $view->with([
                    'message' => \Lang::get('errors.ERROR00001'),
                ]);
            }

            // User作成
            $user = User::create([
                'name'     => $name,
                'email'    => $activation->email,
                'password' => bcrypt($password),
            ]);

            // UserProfile作成
            $user->profile()->create([
                'user_id' => $user->id,
            ]);

            // UserActivation更新
            $activation->update([
                'user_id' => $user->id
            ]);

            auth()->login($user, true);

            return redirect()->to('/home');
        }

        return $view;
    }

}
