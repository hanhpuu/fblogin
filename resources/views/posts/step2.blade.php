@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <h1>Choose your pages</h1>
        <hr>
        <form action="{{route('step2')}}" method="post">
            {{ csrf_field() }}
            @foreach ($pages as $page)
                <div class="form-group">
                    <input type="radio" name="page_id" value="{{$page['id']}}" required/>
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
    </div>
</div>
@endsection
