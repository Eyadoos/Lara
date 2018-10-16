@extends('layouts.master')

@section('title')
	{{ "KW" . $date['week'] . ": " . utf8_encode(strftime("%d. %b", strtotime($weekStart))) }} - {{ utf8_encode(strftime("%d. %b", strtotime($weekEnd))) }}
@stop

@section('content')
    <div class="container-fluid pb-3">
        <div class="row">

{{-- Prev/next week selector --}}
            <div class="col-xs-12 col-md-4 m-auto p-auto btn-group">
                <a class="btn hidden-print"
                   href="{{ Request::getBasePath() }}/calendar/{{$date['previousWeek']}}">
                    <i class="fas fa-chevron-left"></i>
                </a>

                <span class="row align-items-center mx-auto px-auto">
                    <h6 class="week-mo-so m-0 text-center">
                        {{ "KW" . $date['week']}}:
                        <br class="d-block d-sm-none">
                        {{ utf8_encode(strftime("%a %d. %B", strtotime($weekStart))) }} -
                        <br class="d-block d-sm-none">
                        {{ utf8_encode(strftime("%a %d. %B", strtotime($weekEnd . '- 2 days'))) }}
                    </h6>

                    <h6 class="week-mi-di m-0 text-center hide">
                        {{ "KW" . $date['week']}}:
                        <br class="d-block d-sm-none">
                        {{ utf8_encode(strftime("%a %d. %B", strtotime($weekStart . '+  2 days'))) }} -
                        <br class="d-block d-sm-none">
                        {{ utf8_encode(strftime("%a %d. %B", strtotime($weekEnd))) }}
                    </h6>
                </span>

                <a class="btn hidden-print"
                   href="{{ Request::getBasePath() }}/calendar/{{$date['nextWeek']}}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>

{{-- Section filter --}}
            <div class="col-xs-12 col-md-8 p-0 m-0 d-print-none" id="section-filter">
                @include('partials.filter')

{{-- Week filters --}}
        		<div class="btn-group p-auto m-auto float-right pt-2">
        			{{-- show time button Ger.: Zeiten einblenden --}}
        			<button class="btn btn-sm hidden-print" type="button" id="toggle-shift-time">
                        {{ trans('mainLang.shiftTime') }}
                    </button>

        			{{-- hide taken shifts button Ger.: Vergebenen Diensten ausblenden --}}
        			<button class="btn btn-sm hidden-print" type="button" id="toggle-taken-shifts">
                        {{ trans('mainLang.hideTakenShifts') }}
                    </button>

        			{{-- show/hide all comment fields --}}
        			<button class="btn btn-sm hidden-print" type="button" id="toggle-all-comments">
                        {{ trans('mainLang.comments') }}
                    </button>

        			{{-- week: Monday - Sunday button Ger.: Woche: Montag - Sonntag --}}
        			<button class="btn btn-sm btn-primary hidden-print" type="button" id="toggle-week-start">
                        {{ trans('mainLang.weekStart') }}
                    </button>
        		</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="container-fluid" >
{{-- weekdays --}}
            @if (!$events->isEmpty())
                <div class="isotope">

                    @foreach($events as $clubEvent)
                        {{-- Filter: we add a css class later below if a club is mentioned in filter data --}}
                        {{-- we compare the current week number with the week the event happens in
                                         to catch and hide any events on mondays and tuesdays (day < 3) next week
                                         in Mo-So or alternatively mondays/tuesdays this week in Mi-Di view. --}}
                        @php
                        $elementClass = 'element-item private section-filter';
                        foreach($sections as $section){
                            if(in_array( $section->id, $clubEvent->showToSectionIds() )){
                            $elementClass.=" section-" . $section->id;
                            }
                        }
                        if ( date('W', strtotime($clubEvent->evnt_date_start)) === $date['week']
                                  && date('N', strtotime($clubEvent->evnt_date_start)) < 3 ) {
                                  $elementClass.=' week-mo-so';
                        } elseif (date("W", strtotime($clubEvent->evnt_date_start) )
                                      === date("W", strtotime("next Week".$weekStart))
                                      && date('N', strtotime($clubEvent->evnt_date_start)) < 3) {
                                      $elementClass.=' week-mi-di hide';
                        }
                        if($clubEvent->evnt_is_private){
                           $elementClass.=' private';
                        }

                        @endphp
                        <div class="{{$elementClass}}">
                        {{-- guests see private events as placeholders only, so check if user is logged in --}}
                        @guest
                            @if($clubEvent->evnt_is_private)
                             @include('partials.weekCellHidden')
                            {{-- show public events, but protect members' entries from being changed by guests --}}
                            @else
                              @include('partials.weekCellProtected')
                            @endif
                        {{-- show everything for members --}}
                        @else
                            {{-- members see both private and public events, but still need to manage color scheme --}}
                         @include('partials.weekCellFull')
                        @endguest
                        </div>
                    @endforeach

                    @foreach($surveys as $survey)
                        @if ( date('W', strtotime($survey->deadline)) === $date['week']
                         &&  date('N', strtotime($survey->deadline)) < 3 )
                            <div class="element-item section-filter section-survey week-mo-so ">
                        @elseif ( date("W", strtotime($survey->deadline) ) === date("W", strtotime("next Week".$weekStart))
                         &&      date('N', strtotime($survey->deadline)) < 3 )
                            <div class="element-item section-filter section-survey week-mi-di hide">
                        @else
                            <div class="element-item section-filter section-survey">
                        @endif
                            @include('partials.weekCellSurvey')
                        </div>
                    @endforeach

                        {{-- hack: empty day at the beginning,
                             prevents isotope collapsing to a single column if the very first element is hidden
                             by creating an invisible block and putting it out of the way via negative margin --}}
                        <div class="grid-sizer" style="margin-bottom: -34px;"></div>
                        {{-- end of hack --}}
                    </div>
                </div>
            </div>

            @else
                <br>
                </div>
                <div class="panel" style="margin: 16px;">
                    <div class="card-header">
                        <h5>{{ trans('mainLang.noEventsThisWeek') }}</h5>
                    </div>
                </div>

                <div class="isotope" style="margin: 6px;">
                    @if(count($surveys)>0)
                        @foreach($surveys as $survey)
                            @if ( date('W', strtotime($survey->deadline)) === $date['week']
                             &&  date('N', strtotime($survey->deadline)) < 3 )
                                <div class="element-item section-filter section-survey week-mo-so ">
                            @elseif ( date("W", strtotime($survey->deadline) ) === date("W", strtotime("next Week".$weekStart))
                             &&      date('N', strtotime($survey->deadline)) < 3 )
                                <div class="element-item section-filter section-survey week-mi-di hide">
                            @else
                                <div class="element-item section-filter section-survey">
                            @endif
                                @include('partials.weekCellSurvey')
                            </div>
                        @endforeach
                    @endif
                    {{-- hack: empty day at the beginning,
                         prevents isotope collapsing to a single column if the very first element is hidden
                         by creating an invisible block and putting it out of the way via negative margin --}}
                    <div class="grid-sizer" style="margin-bottom: -34px;"></div>
                    {{-- end of hack --}}
                </div>

            @endif
        </div>

        <div class="col-md-12 col-xs-12">
            {{-- Legend --}}
            @include("partials.legend")

            {{-- filter hack --}}
            <span id="week-view-marker" hidden>&nbsp;</span>
        </div>
    </div>
@stop
