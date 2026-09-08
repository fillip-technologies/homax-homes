<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Homax Homes')</title>
    <meta name="description" content="@yield('description', 'Homax Homes - Your Real Estate Partner')" />
    <meta name="keywords" content="@yield('keywords', 'real estate, property, buying, selling, renting')" />
    <meta name="author" content="@yield('author', 'Your Name')" />
    <meta property="og:title" content="@yield('og_title', 'RealFun')" />
    <meta property="og:description" content="@yield('og_description', 'Your Real Estate Partner')" />
    <meta property="og:image" content="@yield('og_image', asset('images/default.jpg'))" />
    <meta property="og:url" content="@yield('og_url', url()->current())" />
    <meta property="og:type" content="@yield('og_type', 'website')" />
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')" />
    <meta name="twitter:title" content="@yield('twitter_title', 'RealFun')" />
    <meta name="twitter:description" content="@yield('twitter_description', 'Your Real Estate Partner')" />
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/default.jpg'))" />
    <meta name="twitter:site" content="@yield('twitter_site', '@yourtwitterhandle')" />
    <meta name="twitter:creator" content="@yield('twitter_creator', '@yourtwitterhandle')" />
  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any" />
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}" />
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}" />
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />
  <meta name="theme-color" content="#5146C7" />

  <link rel="canonical" href="@yield('canonical', url()->current())" />

  @vite(['resources/css/app.css'])
  {{-- Optional: <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
  <style>
    :root {
      --font-display: "Aboreto", cursive;
      --font-body: "DM Sans", sans-serif;
    }

    body,
    .font-sans,
    button,
    input,
    select,
    textarea {
      font-family: var(--font-body);
    }

    h1,
    h2,
    h3,
    .font-serif,
    .font-display {
      font-family: var(--font-display);
      font-weight: 400;
    }

    h4,
    h5,
    h6 {
      font-family: var(--font-body);
      font-weight: 700;
    }
  </style>
  @yield('head')
    @include('includes.fonts')
</head>
<body>

  @include('includes.header')
    @yield('content')
  @include('includes.footer')

</body>
</html>


