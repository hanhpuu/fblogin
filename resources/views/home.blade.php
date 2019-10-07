@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="pb-3">
            <form action="{{route('folder.upload.clear')}}" method="post">
                <input type="hidden" name="_method" value="delete" />
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                @if(!$bitlyToken)
                    <a class="btn btn-primary" href="{{$bitlyLoginLink}}">Login With Bitly</a>
                @else
                    <a class="btn btn-primary" href="{{route('step1')}}">New Posts</a>
                @endif
                <button type="submit" class="btn btn-danger">Clear Upload Directory</button>

            </form>
        </div>

        @if(!empty($reports))
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Action Download</th>
                        <th>Action Remove</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $id => $report)
                        <tr>
                            <td>{{date('d-m-Y H:i:s', $id)}}</td>
                            <td>
                                <a class="btn btn-success" href="{{route('report.download', ['id' => $id])}}">Download</a>
                            </td>
                            <td>
                                <form action="{{route('report.remove', ['id' => $id])}}" method="post">
                                    <button type="submit" class="btn btn-danger">Remove</button>
                                    <input type="hidden" name="_method" value="delete" />
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>
</div>
@endsection
