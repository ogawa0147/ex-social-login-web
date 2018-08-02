# snsログイン用

```
$ composer require laravel/socialite
$ composer require doctrine/dbal
$ composer require nesbot/carbon

$ composer require socialiteproviders/twitter
$ composer require socialiteproviders/yahoo
$ composer require socialiteproviders/google

$ composer remove abraham/twitteroauth
$ composer remove facebook/graph-sdk
$ composer remove facebook/php-sdk-v4
$ composer remove google/apiclient
```

# composer 実行後のキャッシュクリアなど

```
$ composer dump-autoload

$ php artisan config:clear
```

# Auth 作成など

```
$ php artisan make:auth
```

# マイグレーションなど

```
$ php artisan make:migration prepare_users_table_for_social_authentication --table users
$ php artisan make:model UserSocialAccount --migration
#$ php artisan make:migration create_admins_table
$ php artisan make:model Admin --migration
$ php artisan make:model Define --migration
$ php artisan make:model UserProfile --migration
$ php artisan make:model UserActivation --migration
$ php artisan make:migration add_agreed_to_social_accounts_table --table social_accounts

$ php artisan migrate

# 一番最後に実行したマイグレーションが無かったことになる
$ php artisan migrate:rollback

# マイグレーションを３世代前にロールバックする
$ php artisan migrate:rollback --step=3

# 全てのマイグレーションをロールバックする
$ php artisan migrate:reset

# 全てのマイグレーションをロールバックし、再度マイグレーションを実行する
$ php artisan migrate:refresh
```

# コントローラ作成など

```
$ php artisan make:controller 'Auth\SocialAccountController'
```

# メール機能など

```
$ php artisan make:mail Mailer
```

# シーダーなど

```
$ php artisan make:seeder DefineSeeder
$ php artisan db:seed
```
