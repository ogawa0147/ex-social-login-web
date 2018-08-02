@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">利用規約・プライバシーポリシー</div>

                <div class="panel-body">
                    <div class="form-group">
                        {{ $agreement }}
                    </div>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> 同意する
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="panel-group">
                        <div class="col-md-6 col-md-offset-4">
                            <a class="btn btn-primary" href="{{ url('/signup/agreed') }}">アカウント作成</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
