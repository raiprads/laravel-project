@extends('layouts.app')

@section('title'," | My Favorites")

@section('content')

<div class="container">
    <div class="row">

        <div class="col-md-12">
            <h1>My Favorites</h1>
        </div>

        @if($events->isEmpty())

            <div class="row">
                
                <div class="col-md-12 event-item-horizontal">
                    <p>You don't have any events in here.</p>
                </div>

            </div>

        @else

            @foreach($events as $key => $value)
                
                <div class="row">
                    
                    <div class="col-md-12 event-item-horizontal">

                        <div class="col-md-4 col-sm-4 event-item-image">
                            @if(!empty($value->image))
                                <img src='{{ $value->image }}' class="img-responsive"/>
                            @else
                                <img src='/images/blank.jpg' class="img-responsive"/>
                            @endif
                        </div>

                        <div class="col-md-8 col-sm-8 event-item-content">
                            @if(isset($value->title))
                               <h3><a href="/events/{{ $value->listing_id }}">{{ $value->title }}</a></h3>
                            @endif
                        
                            @if(!empty($value->start_time))
                                <p><strong>Start time:</strong> {{ date("M-d-Y l h:i A",strtotime($value->start_time)) }}</p>
                            @endif

                            @if(!empty($value->end_time))
                                <p><strong>End time:</strong> {{ date("M-d-Y l h:i A",strtotime($value->end_time)) }}</p>
                            @endif
                        
                            
                            @if(!empty($value->location))
                                <p><strong>Location:</strong> {{ $value->location }}</p>
                            @endif

                            <hr/>

                            @if(!empty($value->short_description))
                                <p>{{ strip_tags($value->short_description) }}</p>
                            @else
                                <p><i>No event description.</i></p>
                            @endif

                            @if(!empty($value->info_url))
                                <p><strong>Info URL:</strong> <a href="{{ $value->info_url }}" target="_blank">{{ parse_url( $value->info_url , 1) }}</a></p>
                            @endif
                        </div>

                    </div>


                </div>

            @endforeach

        @endif


    </div>
</div>
@endsection
