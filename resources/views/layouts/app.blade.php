<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0" />

  @isset($title)
    <title>{{ $title }} | {{ config('app.name') }}</title>
  @else
    <title>{{ config('app.name') }}</title>
  @endisset
    <script>
      window.changeTheme = function (dark) {
        if (dark) {
          document.documentElement.classList.add('dark')
        } else {
          document.documentElement.classList.remove('dark')
        }

        localStorage.theme = dark ? 'dark' : 'light'
      }

      window.changeTheme(localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches))
    </script>

    <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
    <script src="{{ mix('/js/app.js') }}" defer></script>
  </head>
  <body class="text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900">
    @inertia
  </body>
</html>
