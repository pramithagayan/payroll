@extends('twill::layouts.main')

@php
    $emptyMessage = $emptyMessage ?? twillTrans('twill::lang.dashboard.empty-message');
    $isDashboard = true;
    $translate = true;
@endphp

@push('extra_css')
    @if(app()->isProduction())
        <link href="{{ twillAsset('main-dashboard.css') }}" rel="preload" as="style" crossorigin/>
    @endif
    @unless(config('twill.dev_mode', false))
        <link href="{{ twillAsset('main-dashboard.css') }}" rel="stylesheet" crossorigin/>        
    @endunless
@endpush

@push('extra_js_head')
    @if(app()->isProduction())
        <link href="{{ twillAsset('main-dashboard.js') }}" rel="preload" as="script" crossorigin/>
    @endif
@endpush

@section('appTypeClass', 'body--dashboard')

@section('primaryNavigation')
    @if (config('twill.enabled.search', false))
        <div class="dashboardSearch" id="searchApp" v-cloak>
            <a17-search endpoint="{{ route(config('twill.dashboard.search_endpoint')) }}" type="dashboard" placeholder="{{ twillTrans('twill::lang.dashboard.search-placeholder') }}"></a17-search>
        </div>
    @endif
@stop

@section('content')
    <div class="dashboard">
        <!--<a17-shortcut-creator :entities="{{ json_encode($shortcuts ?? []) }}"></a17-shortcut-creator>-->

        <div class="container">
            <div class="flex flex-wrap align-items-center">
                <input type="file" 
                class="filepond"
                name="filepond" 
                multiple 
                data-allow-reorder="true"
                data-max-file-size="3MB"
                data-max-files="10"
                style="opacity: 0;"
                >
            </div>
        </div>
    </div>
@stop

@section('initialStore')
    window['{{ config('twill.js_namespace') }}'].STORE.datatable = {}

    window['{{ config('twill.js_namespace') }}'].STORE.datatable.mine = {!! json_encode($myActivityData) !!}
    window['{{ config('twill.js_namespace') }}'].STORE.datatable.all = {!! json_encode($allActivityData) !!}

    window['{{ config('twill.js_namespace') }}'].STORE.datatable.data = window['{{ config('twill.js_namespace') }}'].STORE.datatable.all
    window['{{ config('twill.js_namespace') }}'].STORE.datatable.columns = {!! json_encode($tableColumns) !!}
@stop


@push('extra_js')
    <script src="{{ twillAsset('main-dashboard.js') }}" crossorigin></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        const inputElement = document.querySelector('input[type="file"]');
        const pond = FilePond.create( inputElement, {
            labelIdle: `Drag & Drop Your Payroll Files Here<span class="small">You may click here to select a file</span>`,
            allowRevert: false
        } );
        FilePond.setOptions({
            server: {
                url: '/payroll',
                process: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }
            }
        });
    </script>
@endpush
