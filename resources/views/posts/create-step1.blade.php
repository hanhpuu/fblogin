@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
<h1>Add New Post</h1>
    <hr>
    <form action="/posts/create-step1" method="post">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="content">Post content</label>
            <textarea type="text" class="form-control" id="content"  name="content"></textarea>
        </div>

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
</div>

@endsection
