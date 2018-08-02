@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <!-- Normal -->
            <div class="panel panel-default">

                <div class="panel-heading">
                    <div class="form-group">
                        <div class="col-md-8">{{ __('captions.login') }}</div>
                        <div class="col-md-4"><a href="{{ route('signup.general') }}">またはアカウントの作成</a></div>
                    </div>
                </div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">{{ __('captions.email') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">{{ __('captions.password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('captions.remember_me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('captions.login') }}
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('captions.forgot_your_password') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- // Normal -->

            <!-- Socialite -->
            <div class="panel panel-default">

                <div class="panel-heading">他のサービスでログインする</div>

                <div class="panel-body">

                    <!-- Facebook OAuth -->
                    <div class="panel-group">
                        <a class="btn btn-success" href="{{ url('/login/social/facebook') }}">Sign in with Facebook</a>
                    </div>

                    <!-- Twitter OAuth -->
                    <div class="panel-group">
                        <a class="btn btn-success" href="{{ url('/login/social/twitter') }}">Sign in with Twitter</a>
                    </div>

                    <!-- Google OAuth -->
                    <div class="panel-group">
                        <a class="btn btn-success" href="{{ url('/login/social/google') }}">Sign in with Google</a>
                    </div>

                    <!-- Yahoo! OAuth -->
                    <div class="panel-group">
                        <a class="btn btn-success" href="{{ url('/login/social/yahoo') }}">Sign in with Yahoo!</a>
                    </div>

                </div>
            </div>
            <!-- // Socialite -->

        </div>
    </div>
</div>
@endsection
