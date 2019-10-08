@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="/post/step3" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <h3>Please Upload All Files</h3>
                    <hr>
                    <br/><br/>
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
                            <input type="file" name="file[]" multiple id="allFiles">
                            <small id="fileHelp" class="form-text text-muted">Please upload all files.</small>
                            <br/>
                            <input type="submit" class="btn btn-info" value="Upload"/>
                        </form>
                        <br/>
                    </div>
                </form>
                <br/>
            </div>
        </div>
    </div>
@endsection
