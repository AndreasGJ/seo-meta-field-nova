@php
$seo = isset($page) && $page && method_exists($page, 'getSeoMeta') ? $page->getSeoMeta() : [
    'title' => config('app.name', 'Laravel'),
    'description' => null,
    'keywords' => null
];
if (isset($page) && $page && is_array($page) && isset($page['seo'])) {
    $seo = array_merge($seo, $page['seo']);
}

if(!empty($seo['params'])){
    if(!empty($seo['params']->title_format)){
        $seo['title'] = str_replace(':text', $seo['title'], $seo['params']->title_format);
    }

}
@endphp

<title>{{ $seo['title'] }}</title>

@if($seo['description'])
<meta name="description" content="{{ $seo['description'] }}" />
@endif

@if($seo['keywords'])
<meta name="keywords" content="{{ $seo['keywords'] }}" />
@endif

<meta property="og:title" content="{{ $seo['title'] }}" />
<meta property="og:description" content="{{ $seo['description'] }}" />

@if(!empty($seo['image_path']))
<meta property="og:image" content="{{ $seo['image_path'] }}" />
@endif

<meta name="robots" content="{{ !empty($seo['follow_type']) ? $seo['follow_type'] : 'noindex, nofollow' }}" />
