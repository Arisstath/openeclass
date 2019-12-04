@extends('layouts.default')

@section('content')
    {!! isset($action_bar) ?  $action_bar : '' !!}
    <div class='form-wrapper'>
        <form class='form-horizontal' role='form' name='serverForm' action='{{ $_SERVER['SCRIPT_NAME'] }}' method='post'>
        <fieldset>
            <div class='form-group'>
                <label for='api_url_form' class='col-sm-3 control-label'>API URL:</label>
                <div class='col-sm-9'>
                    <input class='form-control' type='text' id='api_url_form' name='api_url_form' value='{{ isset($server) ? $server->api_url : "" }}'>
                </div>
            </div>
            <div class='form-group'>
                <label for='key_form' class='col-sm-3 control-label'>{{ trans('langPresharedKey') }}:</label>
                <div class='col-sm-9'>
                    <input class='form-control' type='text' name='key_form' value='{{ isset($server) ? $server->server_key : "" }}'>
                </div>
            </div>
            <div class='form-group'>
                <label for='max_rooms_form' class='col-sm-3 control-label'>{{ trans('langMaxRooms') }}:</label>
                <div class='col-sm-9'>
                    <input class='form-control' type='text' id='max_rooms_for' name='max_rooms_form' value='{{ isset($server) ? $server->max_rooms : "" }}'>
                </div>
            </div>
            <div class='form-group'>
                <label for='max_rooms_form' class='col-sm-3 control-label'>{{ trans('langMaxUsers') }}:</label>
                <div class='col-sm-9'>
                    <input class='form-control' type='text' id='max_users_form' name='max_users_form' value='{{ isset($server) ? $server->max_users : "" }}'>
                </div>
            </div>
            <div class='form-group'>
                <label class='col-sm-3 control-label'>{{ trans('langBBBEnableRecordings') }}:</label>
                <div class="col-sm-9">
                    <div class='radio'>
                        <label>
                            <input  type='radio' id='recordings_on' name='enable_recordings' value='true'{{ $enabled_recordings ? ' checked' : '' }}>
                            {{ trans('langYes') }}
                        </label>
                    </div>                
                    <div class='radio'>
                        <label>
                            <input  type='radio' id='recordings_off' name='enable_recordings' value='false'{{ $enabled_recordings ? '' : ' checked' }}>
                            {{ trans('langNo') }}
                        </label>
                    </div>                    
                </div>
            </div>
            <div class='form-group'>
                <label class='col-sm-3 control-label'>{{ trans('langActivate') }}:</label>
                <div class="col-sm-9">
                    <div class='radio'>
                        <label>
                            <input  type='radio' id='enabled_true' name='enabled' value='true'{{ $enabled ? ' checked' : '' }}>
                            {{ trans('langYes') }}
                        </label>
                    </div>                
                    <div class='radio'>
                        <label>
                            <input  type='radio' id='enabled_false' name='enabled' value='false'{{ $enabled ? '' : ' checked' }}>
                            {{ trans('langNo') }}
                        </label>
                    </div>                      
                </div>
            </div>
            <div class='form-group'>
                <label class='col-sm-3 control-label'>{{ trans('langBBBServerOrder') }}:</label>
                <div class='col-sm-9'>
                    <input class='form-control' type='text' name='weight' value='{{ isset($server) ? $server->weight : "" }}'>
                </div>
            </div>
            <div class='form-group'>
                <label class='col-sm-3 control-label'>{{ trans('langUseOfTc') }}:</label>
                <div class="col-sm-9">
                    <select class='form-control' name='tc_courses[]' multiple class='form-control' id='select-courses'>                        
                        {!! $listcourses !!}
                    </select>            
                    <a href='#' id='selectAll'>{{ trans('langJQCheckAll') }}</a> | <a href='#' id='removeAll'>{{ trans('langJQUncheckAll') }}</a>
                </div>
            </div>
            @if (isset($server))
                <input class='form-control' type = 'hidden' name = 'id_form' value='{{ getIndirectReference($bbb_server) }}'>
            @endif
            <div class='form-group'>
                <div class='col-sm-offset-3 col-sm-9'>
                    <input class='btn btn-primary' type='submit' name='submit' value='{{ trans('langAddModify') }}'>
                </div>
            </div>
        </fieldset>
        </form>
    </div>
    <script language="javaScript" type="text/javascript">
        var chkValidator  = new Validator("serverForm");
        chkValidator.addValidation("key_form","req","{{ trans('langBBBServerAlertKey') }}");
        chkValidator.addValidation("api_url_form","req","{{ trans('langBBBServerAlertAPIUrl') }}");
        chkValidator.addValidation("max_rooms_form","req","{{ trans('langBBBServerAlertMaxRooms') }}");
        chkValidator.addValidation("max_rooms_form","numeric","{{ trans('langBBBServerAlertMaxRooms') }}");
        chkValidator.addValidation("max_users_form","req","{{ trans('langBBBServerAlertMaxUsers') }}");
        chkValidator.addValidation("max_users_form","numeric","{{ trans('langBBBServerAlertMaxUsers') }}");
        chkValidator.addValidation("weight","req","{{ trans('langBBBServerAlertOrder') }}");
        chkValidator.addValidation("weight","numeric","{{ trans('langBBBServerAlertOrder') }}");
    </script>  
@endsection