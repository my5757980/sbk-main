@component('mail::message')
    __Hi {!! $user['email'] !!},__<br><br>
    You are receiving this email because we received a password reset request for your account.<br><br>
    {!! 'Your reset password code generated is: ' . '__' . $user['otp'] . '__' !!}<br><br>
    If you did not request a password reset, no further action is required.<br><br>
    Please note that this is an auto generated email. Please do not reply to this email.<br><br>
    @lang('Best Regards'),<br>
    {{ config('app.name') }}
@endcomponent
