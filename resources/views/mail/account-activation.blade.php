@component('mail::message')
    __Hi {!! $data['email'] !!},__<br><br>
    Your account has been created successfully on {{ config('app.name') }}. Please verify your account using the code
    below:<br><br>
    {!! 'Your verification code generated is: ' . '__' . $data['otp'] . '__' !!}<br><br>
    Please note that this is an auto generated email. Please do not reply to this email.<br><br>
    @lang('Best Regards'),<br>
    {{ config('app.name') }}
@endcomponent
