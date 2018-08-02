@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ __('captions.sent_email') }}</div>

                <div class="panel-body">
                    <div class="form-group">
                        {{ $email }}
                    </div>
                    <div class="form-group">
                        宛にメールを送信しました。
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
