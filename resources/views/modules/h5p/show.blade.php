@extends('layouts.default')

@push('head_scripts')
<link type="text/css" rel="stylesheet" media="all" href="{{ $urlServer }}js/h5p-standalone/styles/h5p.css" />
<script type="text/javascript" src="{{ $urlServer }}js/h5p-standalone/main.bundle.js"></script>
@endpush

@section('content')

{!! $action_bar !!}

    <div class="row">
        <div class="col-xs-12">
			<div id="h5p-container"></div>
        </div>
    </div>
	
    <script type="text/javascript">
        $(document).ready(function() {
            const el = document.getElementById('h5p-container');
            const options = {
                h5pJsonPath:  '{{ $workspaceUrl }}',
                librariesPath: '{{ $workspaceLibs }}',
                frameJs: '{{  $urlServer }}js/h5p-standalone/frame.bundle.js',
                frameCss: '{{  $urlServer }}js/h5p-standalone/styles/h5p.css',
                frame: true,
                copyright: true,
                icon: true,
                fullScreen: true,
                export: {!! ($content->reuse_enabled ? "true" : "false") !!},
                downloadUrl: '{{ $urlServer }}modules/h5p/reuse.php?course={{ $course_code }}&id={{ $content->id }}'
            };
            new H5PStandalone.H5P(el, options);
        });
    </script>
@endsection
