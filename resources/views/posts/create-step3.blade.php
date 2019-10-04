@extends('layouts.app')

@section('content')

    <h1>Choose your pages</h1>
    <hr>
    <form action="/getPageAccessToken" method="post">
        {{ csrf_field() }}
        @foreach ($adminPages as $page)
            <div class="form-group">
                <input type="hidden" name="page_name" value="{{$page['name']}}"/>
                <input type="hidden" name="page_access_token" value="{{$page['access_token']}}" id="tokenPage"/>
                <input type="radio" name="page_access_id" value="{{$page['id']}}" id="idPage"/>
                <label for="complete_yes">{{$page['name']}}</label>

            </div>
        @endforeach
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection
