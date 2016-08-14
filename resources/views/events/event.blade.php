@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">

        <div class="col-md-12">
            <h1>Events</h1>
        </div>

        <div class="row">

            <div class="col-lg-12 col-md-12 event-item">

            
                <div class="col-lg-6 col-md-6 col-sm-12 event-item-image-lg">
                    <div class="container-fluid">
                    @if(isset($event->images[0]->url))
                        <img src='{{ $event->images[0]->url }}' class="img-responsive center-block col-md-12 col-sm-12"/>
                    @else
                        <img src='/images/blank.jpg' class="img-responsive center-block col-md-12 col-sm-12"/>
                    @endif
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 event-item-content">
                    @if(isset($event->name->fi))
                       <h3>{{ $event->name->fi }}</h3>
                    @endif

                    @if(isset($event->start_time))
                        <p><strong>Start time:</strong> {{ date("M-d-Y l h:i A",strtotime($event->start_time)) }}</p>
                    @endif

                    @if(isset($event->end_time))
                        <p><strong>End time:</strong> {{ date("M-d-Y l h:i A",strtotime($event->end_time)) }}</p>
                    @endif
                    
                    @if(isset($event->location_extra_info->fi))
                        <p><strong>Location:</strong> {{ $event->location_extra_info->fi }}</p>
                    @endif

                    <hr/>

                    <input type="hidden" id="token" value="{{ csrf_token() }}">
                    
                    <div class="btn-group event-social-button" id="bookmark-buttons" role="group" aria-label="...">
                        <button type="button" class="btn btn-sm btn-default" id="favorite_{{ str_replace(':','_',$event->id) }}" {{ disableBookmarkButton('favorite', $event->id ) }}>
                            <span class="fa fa-btn fa-star"></span>
                            <span class="button_label">
                                {{ showNumberOfBookmarks('favorite', $event->id) }}
                            </span>
                        </button>

                        <button type="button" class="btn btn-sm btn-default" id="wish_{{ str_replace(':','_',$event->id) }}" {{ disableBookmarkButton('wish', $event->id ) }}>
                            <span class="fa fa-btn fa-check-square-o"></span>
                            <span class="button_label">
                                {{ showNumberOfBookmarks('wish', $event->id) }}
                            </span>
                        </button>

                        <button type="button" class="btn btn-sm btn-default" id="watch_{{ str_replace(':','_',$event->id) }}" {{ disableBookmarkButton('watch', $event->id ) }}>
                            <span class="fa fa-btn fa-eye"></span>
                            <span class="button_label">
                                {{ showNumberOfBookmarks('watch', $event->id) }}
                            </span>
                        </button>

                    </div>

                    <div id="bookmark-message"></div>

                    <hr/>

                    @if(isset($event->description->fi))
                        <p>{!! $event->description->fi !!}</p>
                    @else
                        <p><i>No event description.</i></p>
                    @endif

                    @if(isset($event->info_url->fi))
                        <p><strong>Info URL:</strong> <a href="{{ $event->info_url->fi }}" target="_blank">{{ parse_url( $event->info_url->fi , 1) }}</a></p>
                    @endif

                    <div class="btnBackToEvents">
                        <a href="{{ url('/events') }}">Back to Events</a>
                    </div>
                    
                </div>


            </div>

        </div>

        
    </div>
</div>
@endsection

