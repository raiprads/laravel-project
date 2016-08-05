@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">

        <div class="col-md-12">
            <h1>Latests Events</h1>
        </div>

        @foreach($events->data as $key => $value)
            
            <div class="col-md-4 col-sm-6 col-xs-12 ">
                
                <div class="col-sm-12 col-xs-12 event-item">

                    <div class="col-sm-12 col-xs-12 event-item-image">
                        @if(isset($value->image))
                            <img src='{{ $value->image }}' class="img-responsive"/>
                        @else
                            <img src='/images/blank.jpg' class="img-responsive"/>
                        @endif
                    </div>

                    <div class="col-sm-12 col-xs-12 event-item-content">
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

                        <p>https://api.hel.fi/linkedevents/v1/event/{{ $value->id }}/?format=json</p>
                    </div>

                </div>


            </div>

        @endforeach


    </div>
</div>
@endsection
