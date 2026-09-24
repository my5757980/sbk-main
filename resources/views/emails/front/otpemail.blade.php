@component('mail::message')
    __Hi {{$data['name']}},__<br><br>
    use mention below otp :<br><br>
    <p>
        Otp code : {!! $data['otp'] !!}
    </p>
    Thanks,<br>
    @lang('Best Regards'),<br>
    {{ config('app.name') }}
@endcomponent