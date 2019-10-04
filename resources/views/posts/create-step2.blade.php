@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
    <h1>Add New Product - Step 2</h1>
    <hr>

    <form action="/posts/create-step2" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <h3>Please Select Zip File</h3><br/><br/>

        <div class="form-group">

{{--            <input type="file" name="postimg" id="postimg" accept=".csv">--}}
{{--            <small id="fileHelp" class="form-text text-muted">Please upload a valid csv file.</small>--}}

            <form method="post" enctype="multipart/form-data">
                <input type="file" name="zipFile" accept=".zip"/>
                <small id="fileHelp" class="form-text text-muted">Please upload a valid zip file.</small>
                <br />
                <input type="submit" name="btnZip" class="btn btn-info" value="Upload" />
            </form>
            <br />

        </div>
        <button type="submit" class="btn btn-primary">Review Product Details</button>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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
