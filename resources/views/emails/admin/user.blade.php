<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>

    <style>
        .footer {
            text-align: center;
            font-size: 0.9em;
            color: #777;
        }
    </style>

</head>

<body>
{{--@component('mail::message')--}}
    <div class="container">
        <div class="content">
            {!! $body!!}
        </div>
        @lang('Best Regards'),<br>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
{{--    @endcomponent--}}

</body>

</html>
