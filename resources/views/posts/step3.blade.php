@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
    <h1>Upload Zip File</h1>
    <hr>

    <form action="/post/step3" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <h3>Please Select Zip File</h3><br/><br/>
        @if(session('error'))
            <div class="alert alert-danger" role="alert">
                {{session('error')}}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="form-group">


            <form method="post" enctype="multipart/form-data">
                <input type="file" name="file" accept=".zip"/>
                <small id="fileHelp" class="form-text text-muted">Please upload a valid zip file.</small>
                <br />
                <input type="submit" class="btn btn-info" value="Upload" />
            </form>
            <br />

        </div>
        <button type="submit" class="btn btn-primary">Review Product Details</button>
    </form><br/>
    @if(isset($post->postImg))
    <form action="/posts/remove-image" method="post">
        {{ csrf_field() }}
    <button type="submit" class="btn btn-danger">Remove Image</button>
    </form>
    @endif
</div>
</div>
</div>
@endsection
