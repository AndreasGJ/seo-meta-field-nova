@php
    if (isset($page) && $page && method_exists($page, 'getSeoMeta')) {
        $seo = $page->buildSeoForCurrentLocale();
    } elseif (isset($seo) && $seo && is_array($seo) && isset($seo['title'])) {
        $seo = $seo;
    } else {
        $seo = [
            'title' => config('seo.default_seo_title'),
            'description' => config('seo.default_seo_description'),
            'keywords' => config('seo.default_seo_keywords')
        ];
    }
    if(!empty($seo['params'])){
        if(!empty($seo['params']->title_format)){
            $seo['title'] = str_replace(':text', $seo['title'], $seo['params']->title_format);
        }

    }
@endphp

<title>{{ $seo['title'] }}</title>

@if(config('seo.seo_status'))
    @if(isset($seo['description']) && $seo['description'])
    <meta name="description" content="{{ $seo['description'] }}" />
    @endif

    @if(isset($seo['keywords']) && $seo['keywords'])
    <meta name="keywords" content="{{ $seo['keywords'] }}" />
    @endif

    <meta property="og:title" content="{{ $seo['title'] }}" />
    <meta property="og:description" content="{{ $seo['description'] }}" />

    @if(!empty($seo['image_path']))
    <meta property="og:image" content="{{ $seo['image_path'] }}" />
    @endif
@else
<meta name="robots" content="{{ !empty($seo['follow_type']) && config('seo.seo_status') ? $seo['follow_type'] : 'noindex, nofollow' }}" />
@endif
