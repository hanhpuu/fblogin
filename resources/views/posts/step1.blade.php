@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <h1>Add New Post</h1>
        <hr>
        <form action="{{route('step1')}}" method="post">
            {{ csrf_field() }}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    {{session('error')}}
                </div>
            @endif
            <div class="form-group">
                <label for="content">Post content</label>
                <textarea type="text" class="form-control" id="content"  name="content"></textarea>
            </div>


            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

@endsection
