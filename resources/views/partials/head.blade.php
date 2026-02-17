<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.tailwindcss.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/3.1.3/css/select.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/searchpanes/2.3.5/css/searchPanes.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.6/css/buttons.dataTables.css">


@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
