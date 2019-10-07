@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <a href="{{$fbLoginLink}}" class="btn btn-primary">Login with Facebook</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
