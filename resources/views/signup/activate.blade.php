@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">

                @if ($message)
                    <div class="panel-heading">メールアドレス認証失敗</div>
                    <div class="panel-body">
                        <div class="form-group">
                            {{ $message }}
                        </div>
                    </div>
                @else
                    <div class="panel-heading">メールアドレス認証完了</div>
                    <div class="panel-body">
                        <div class="form-group">
                            メールアドレス認証が完了しました。
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <a href="{{ route('signup.register.general') }}">登録にすすむ</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
