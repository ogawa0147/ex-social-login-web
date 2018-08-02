<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function () {
    return redirect('/home');
});

switch (env('APP_DOMAIN'))
{
    case 'admin':
        Route::group(['middleware' => 'auth:admin'], function() {
            Route::get('/home', 'Admin\HomeController@index')->name('home');
        });

        Route::get('/login', 'Admin\LoginController@index')->name('login');

        Route::post('/login', 'Admin\LoginController@login')->name('login');
        Route::post('/logout', 'Admin\LoginController@logout')->name('logout');

        break;

    default:
        Route::group(['middleware' => 'auth:user'], function() {
            Route::get('/home', 'HomeController@index')->name('home');
        });

        Route::get('/login', 'Auth\LoginController@index')->name('login');

        Route::post('/login', 'Auth\LoginController@login')->name('login');
        Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

        Route::get('/login/social/{provider}', 'SocialAccountController@redirectToProvider');
        Route::get('/login/social/callback/{provider}', 'SocialAccountController@handleProviderCallback');

        Route::get('/services/policy', 'ServiceController@policy')->name('services.policy');
        Route::get('/services/terms', 'ServiceController@terms')->name('services.terms');

        Route::get('/signup', 'SignupController@index')->name('signup');

        Route::get('/signup/general', 'SignupController@general')->name('signup.general');
        Route::get('/signup/social', 'SignupController@social')->name('signup.social');

        Route::get('/signup/agreed', 'SignupController@agreed')->name('signup.agreed');
        Route::get('/signup/agreement', 'SignupController@agreement')->name('signup.agreement');

        Route::post('/signup/sendmail', 'SignupController@sendmail')->name('signup.sendmail');
        Route::get('/signup/sentmail', 'SignupController@sentmail')->name('signup.sentmail');
        Route::get('/signup/activate', 'SignupController@activate')->name('signup.activate');

        Route::match(['get', 'post'], '/signup/register/general', 'SignupController@registerToGeneral')->name('signup.register.general');
        Route::post('/signup/register/social', 'SignupController@registerToSocial')->name('signup.register.social');

        break;
}
