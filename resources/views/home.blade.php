@extends('layouts.app')

@section('title'," | Home")

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            



            <div id="myCarousel" class="carousel slide" data-ride="carousel">
              <!-- Indicators -->
              <ol class="carousel-indicators">

                @foreach($carousel as $key => $value)

                    <li data-target="#myCarousel" data-slide-to="{{$key}}" @if($key==0) class="active" @endif ></li>
                   
                @endforeach
              </ol>

              <!-- Wrapper for slides -->
              <div class="carousel-inner" role="listbox">
                @foreach($carousel as $key => $value)

                    <div class="item @if($key==0) active @endif ">
                      <img src="{{ $value->image }}" alt="
                        @if (isset($value->name->fi))
                          {{ $value->name->fi }}
                        @elseif (isset($value->name->en))
                          {{ $value->name->en }}
                        @endif
                        ">
                      <div class="carousel-caption">

                        @if (isset($value->name->fi))
                          <h3><a href="/events/{{ $value->id }}">{{ $value->name->fi }}</a></h3>
                        @elseif (isset($value->name->en))
                          <h3><a href="/events/{{ $value->id }}">{{ $value->name->en }}</a></h3>
                        @endif

                        @if (isset($value->short_description->fi))
                            <p>{{ strip_tags($value->short_description->fi) }}</p>
                        @elseif (isset($value->short_description->en))
                            <p>{{ strip_tags($value->short_description->en) }}</p>
                        @endif

                      </div>
                    </div>

                @endforeach

                
              </div>

              <!-- Left and right controls -->
              <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>





        </div>
 


        <div class="col-md-12">
          

            <h1 class="text-center">Welcome to Helsinki{eVents}</h1>


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
                            @else
                               <h3><a href="/events/{{ $value->id }}">{{ $value->name->en }}</a></h3>
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
                            @elseif (isset($value->short_description->en))
                                <p>{{ strip_tags($value->short_description->en) }}</p>
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
