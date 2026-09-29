{{--
    The way in (index.php of the SBK chat). A visitor gives name, e-mail and
    telephone once; a customer signed in on the website sees it only when their
    phone (or a real name) is missing, with their account's e-mail fixed.
--}}
@php
    $chatName = 'SBK Global Auto Trading';
    $css = @filemtime(public_path('assets/chat/chat.css'));
@endphp
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="robots" content="noindex">
<title>Chat with us — {{ $chatName }}</title>
<link rel="icon" href="{{ asset('assets/chat/img/favicon.png') }}">
<link rel="stylesheet" href="{{ asset('assets/chat/chat.css') }}?v={{ $css }}">
<script>
if (window.self !== window.top) { document.documentElement.className += ' framed'; }
</script>
</head>
<body>

<div class="gate">
  <div class="gate-card">

    <div class="gate-head">
      <img src="{{ asset('assets/chat/img/sbk-logo.png') }}" alt="{{ $chatName }}">
      <h1>Chat with our team</h1>
      <p>Tell us who you are and we will put you straight through.</p>
    </div>

    <div class="gate-body">
      @if ($err)
        <div class="alert">{{ $err }}</div>
      @endif

      <form method="POST" action="{{ route('chat.start') }}" autocomplete="on" novalidate>
        @csrf
        <div class="field">
          <label for="f-name">Your name</label>
          <input type="text" id="f-name" name="name" autocomplete="name"
                 placeholder="e.g. Muhammad Yaseen" maxlength="120"
                 value="{{ $name }}" required
                 {{ $fixedName !== '' ? 'readonly' : ($details && $name !== '' ? '' : 'autofocus') }}>
        </div>
        <div class="field">
          <label for="f-email">E-mail</label>
          <input type="email" id="f-email" name="email" autocomplete="email"
                 placeholder="you@example.com" maxlength="160"
                 value="{{ $email }}" required{{ $details ? ' readonly' : '' }}>
        </div>
        <div class="field">
          <label for="f-phone">Telephone</label>
          <input type="tel" id="f-phone" name="phone" autocomplete="tel"
                 placeholder="+92 300 0000000" maxlength="40"
                 value="{{ $phone }}" required{{ ($details && $name !== '') ? ' autofocus' : '' }}>
        </div>

        <button type="submit" class="btn btn-full">
          Start chatting
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M5 12h13M13 6l6 6-6 6"/>
          </svg>
        </button>
      </form>

      <p class="gate-note">
        We use these only to reply to you.<br>
        @if ($details)
          Signed in on the website as {{ $email }}.
        @else
          Already have an SBK account?
          <a href="{{ route('chat.login') }}">Sign in on the website</a>.
        @endif
      </p>
    </div>

  </div>
</div>

</body>
</html>
