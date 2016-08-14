@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">

        <div class="col-md-12">
            <h1>Past Events</h1>
        </div>

        <div class="row">

        @foreach($events->data as $key => $value)
            
            
                
                <div class="col-md-4 col-sm-6 col-xs-12">

                    <div class="col-md-12 event-item">

                        <div class="col-md-12 event-item-image">
                            @if(isset($value->image))
                                <img src='{{ $value->image }}' class="img-responsive"/>
                            @else
                                <img src='/images/blank.jpg' class="img-responsive"/>
                            @endif
                        </div>

                        <div class="col-md-12 event-item-content">
                            @if(isset($value->name->fi))
                               <h3><a href="/events/{{ $value->id }}">{{ $value->name->fi }}</a></h3>
                            @endif

                            @if(isset($value->start_time))
                                <p><strong>Start time:</strong> {{ date("M-d-Y l h:i A",strtotime($value->start_time)) }}</p>
                            @endif

                            @if(isset($value->end_time))
                                <p><strong>End time:</strong> {{ date("M-d-Y l h:i A",strtotime($value->end_time)) }}</p>
                            @endif
                            
                            @if(isset($value->location_extra_info->fi))
                                <p><strong>Location:</strong> {{ $value->location_extra_info->fi }}</p>
                            @endif

                            <hr/>

                            @if(isset($value->short_description->fi))
                                <p>{{ strip_tags($value->short_description->fi) }}</p>
                            @else
                                <p><i>No event description.</i></p>
                            @endif

                            @if(isset($value->info_url->fi))
                                <p><strong>Info URL:</strong> <a href="{{ $value->info_url->fi }}" target="_blank">{{ parse_url( $value->info_url->fi , 1) }}</a></p>
                            @endif
                        </div>

                    </div>

                </div>

            
        @endforeach

        </div>


    </div>
</div>

@endsection
