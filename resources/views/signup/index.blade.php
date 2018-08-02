@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">

                @if ($signup_type == 'general')

                    <div class="panel-heading">{{ __('captions.send_email') }}</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('signup.sendmail') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">{{ __('captions.email') }}</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ $email }}" required>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        登録する
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                @elseif ($signup_type == 'social')

                    <div class="panel-heading">以下の情報で登録する</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('signup.register.social') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">{{ __('captions.name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ $name }}" required>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">{{ __('captions.email') }}</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ $email }}" required>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        登録する
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                @endif

            </div>

            <!-- Socialite -->
            <div class="panel panel-default">

                <div class="panel-heading">他のサービスで登録する</div>

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
