@extends('layouts.app')

@section('content')
    <section class="hero">
        <p class="eyebrow">@lang('blue.home.eyebrow')</p>
        <h1 class="hero__title">@lang('blue.home.title')</h1>
        <p class="hero__lead">@lang('blue.home.lead')</p>
        <p class="hero__note">@lang('blue.home.foundation_note')</p>
    </section>

    <section class="foundation-status" aria-label="@lang('blue.home.status_label')">
        <h2 class="section-title">@lang('blue.home.status_title')</h2>
        <ul class="foundation-status__list">
            <li>@lang('blue.home.status_database')</li>
            <li>@lang('blue.home.status_auth')</li>
            <li>@lang('blue.home.status_rtl')</li>
            <li>@lang('blue.home.status_assets')</li>
        </ul>
        <p class="foundation-status__empty">@lang('blue.home.empty_state')</p>
    </section>
@endsection
