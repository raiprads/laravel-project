@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Welcome</div>

                <div class="panel-body">
                    <h1>Latests Events</h1>

                    @foreach($events->data as $key => $value)
                        
                        @if($key % 2 == 0)
                            <div class="row">
                        @endif

                        <div class="col-md-6">
                            @if(isset($value->name->fi))
                               <h3>{{ $value->name->fi }}</h3>
                            @endif

                            @if(isset($value->start_time))
                                <p>{{ date("d.m.Y",strtotime($value->start_time)) }}</p>
                            @endif

                            @if(isset($value->location_extra_info->fi))
                                <p>{{ $value->location_extra_info->fi }}</p>
                            @endif

                            @if(isset($value->short_description->fi))
                                <p>{{ $value->short_description->fi }}</p>
                            @endif

                            @if(isset($value->image))
                                <img src='{{ $value->image }}' class="img-responsive"/>
                            @endif
                        </div>

                        @if($key % 2 == 1)
                            </div>
                        @endif

                    @endforeach


                </div>

            </div>
        </div>
    </div>
</div>
@endsection
