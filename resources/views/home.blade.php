@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ __('captions.dashboard') }}</div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="name" class="col-md-4 control-label">{{ __('captions.name') }}</label>
                        {{ Auth::user()->name }}
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-md-4 control-label">{{ __('captions.email') }}</label>
                        {{ Auth::user()->email }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
