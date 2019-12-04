@extends('layouts.default')

@section('content')
    @if ($is_editor)
        <div class='row'>
            <div class='col-md-12'>
                {!! action_bar(array(
                    array('title' => trans('langEditUnitSection'),
                          'url' => $editUrl,
                          'icon' => 'fa fa-edit',
                          'level' => 'primary-label',
                          'button-class' => 'btn-success'),
                    array('title' => trans('langAdd') . ' ' . trans('langInsertExercise'),
                          'url' => $insertBaseUrl . 'exercise',
                          'icon' => 'fa fa-pencil-square-o',
                          'level' => 'secondary',
                          'show' => !is_module_disable(MODULE_ID_EXERCISE)),
                    array('title' => trans('langAdd') . ' ' . trans('langInsertDoc'),
                          'url' => $insertBaseUrl . 'doc',
                          'icon' => 'fa fa-folder-open-o',
                          'level' => 'secondary',
                          'show' => !is_module_disable(MODULE_ID_DOCS)),
                    array('title' => trans('langAdd') . ' ' . trans('langInsertText'),
                          'url' => $insertBaseUrl . 'text',
                          'icon' => 'fa fa-file-text-o',
                          'level' => 'secondary'),
                    array('title' => trans('langAdd') . ' ' . trans('langInsertLink'),
                          'url' => $insertBaseUrl . 'link',
                          'icon' => 'fa fa-link',
                          'level' => 'secondary',
                          'show' => !is_module_disable(MODULE_ID_LINKS)),
                    array('title' => trans('langAdd') . ' ' . trans('langLearningPath1'),
                          'url' => $insertBaseUrl . 'lp',
                          'icon' => 'fa fa-ellipsis-h',
                          'level' => 'secondary',
                          'show' => !is_module_disable(MODULE_ID_LP)),
                    array('title' => trans('langAdd') . ' ' . trans('langInsertVideo'),
                          'url' => $insertBaseUrl . 'video',
                          'icon' => 'fa fa-film',
                          'level' => 'secondary',
                          'show' => !is_module_disable(MODULE_ID_VIDEO)),
                    array('title' => trans('langAdd') . ' ' . trans('langInsertForum'),
                          'url' => $insertBaseUrl . 'forum',
                          'icon' => 'fa fa-comments',
                          'level' => 'secondary'),
                    array('title' => trans('langAdd') . ' ' . trans('langInsertEBook'),
                          'url' => $insertBaseUrl . 'ebook',
                          'icon' => 'fa fa-book',
                          'level' => 'secondary',
                          'show' => !is_module_disable(MODULE_ID_EBOOK)),
                    array('title' => trans('langAdd') . ' ' . trans('langInsertWork'),
                          'url' => $insertBaseUrl . 'work',
                          'icon' => 'fa fa-flask',
                          'level' => 'secondary',
                          'show' => !is_module_disable(MODULE_ID_ASSIGN)),
                    array('title' => trans('langAdd') . ' ' . trans('langInsertPoll'),
                          'url' => $insertBaseUrl . 'poll',
                          'icon' => 'fa fa-question-circle',
                          'level' => 'secondary',
                          'show' => !is_module_disable(MODULE_ID_QUESTIONNAIRE)),
                    array('title' => trans('langAdd') . ' ' . trans('langInsertWiki'),
                          'url' => $insertBaseUrl . 'wiki',
                          'icon' => 'fa fa-wikipedia-w',
                          'level' => 'secondary',
                          'show' => !is_module_disable(MODULE_ID_WIKI)),
                    array('title' => trans('langAdd') . ' ' . trans('langInsertChat'),
                          'url' => $insertBaseUrl . 'chat',
                          'icon' => 'fa fa-exchange',
                          'level' => 'secondary',
                          'show' => !is_module_disable(MODULE_ID_CHAT)),
                    array('title' => trans('langAdd') . ' ' . trans('langInsertTcMeeting'),
                          'url' => $insertBaseUrl . 'tc',
                          'icon' => 'fa fa-exchange',
                          'level' => 'secondary',
                          'show' => (!is_module_disable(MODULE_ID_TC) && is_configured_tc_server()))
                    )) !!}
            </div>
        </div>
    @endif

    @if ($previousLink or $nextLink)
        <div class='row'>
            <div class='col-md-12'>
                <div class='form-wrapper course_units_pager clearfix'>
                    @if ($previousLink)
                        <a class='pull-left' title='{{ $previousTitle }}' href='{{ $previousLink}}'>
                            <span class='fa fa-arrow-left space-after-icon'></span>
                            {{ ellipsize($previousTitle, 30) }}
                        </a>
                    @endif
                    @if ($nextLink)
                        <a class='pull-right' title='{{ $nextTitle }}' href='{{ $nextLink}}'>
                            {{ ellipsize($nextTitle, 30) }}
                            <span class='fa fa-arrow-right space-before-icon'></span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class='row'>
        <div class='col-md-12'>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <div class='panel-title h3'>{{ $pageName }}
                        <h6 class='text-muted'>
                            {{ $course_start_week }}
                            {{ $course_finish_week }}
                        </h6>
                    </div>
                </div>
                <div class='panel-body'>
                    <div>
                        {!! standard_text_escape($comments) !!}
                    </div>
                    @if ($tags_list)
                        <div>
                            <small><span class='text-muted'>{{ trans('langTags') }}:</span> {!! $tags_list !!}</small>
                        </div>
                    @endif
                    <div class='unit-resources'>
                        {!! show_resources($unitId) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class='row'>
        <div class='col-md-12'>
            <div class='panel panel-default'>
                <div class='panel-body'>
                    <form class='form-horizontal' name='unitselect' action='{{ $urlAppend }}modules/units/' method='get'>
                        <div class='form-group'>
                            <label class='col-sm-8 control-label'>{{ trans('langCourseUnits') }}</label>
                            <div class='col-sm-4'>
                                <label class='sr-only' for='id'>{{ trans('langCourseUnits') }}</label>
                                <select name='id' id='id' class='form-control' onchange='document.unitselect.submit()'>
                                    @foreach ($units as $unit)
                                        <option value='{{ $unit->id }}' {{ $unit->id == $unitId ? 'selected' : '' }}>
                                            {{ ellipsize($unit->title, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

