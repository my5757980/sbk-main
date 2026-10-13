{{--
    The live chat's corner of the website (the SBK chat's WordPress mu-plugin,
    sbk-live-chat.php): the round button, the panel that IS the chat, the red
    number, the WhatsApp-style card, the ringing card, and the heartbeat that
    makes a member of staff green while they are merely reading the website.

    Included by the front layout on every page, and by the admin layout for the
    desk. The chat is on this same domain, so the session already says who is
    here - no handover token is needed.
--}}
@php
    $chatMe = app(\App\Services\Chat\ChatService::class)->staff();
    $chatIsDesk = $chatMe !== null;
@endphp
@if (!($chatDeskOnly ?? false) || $chatIsDesk)
<style>
/* SBK live chat - the corner of the website. The colours are the logo's own:
   navy #18335e, the mark's red #bc1e2d, the water's blue #0370cd. */
#sbk-chat-fab{
  position:fixed;left:20px;bottom:20px;z-index:99998;
  display:inline-flex;align-items:center;justify-content:center;
  width:56px;height:56px;padding:0;border:0;border-radius:999px;cursor:pointer;
  background:linear-gradient(160deg,#2a4c82 0%,#18335e 58%,#12294d 100%);
  color:#fff!important;text-decoration:none!important;
  font:650 14.5px/1 "Inter","Segoe UI",-apple-system,BlinkMacSystemFont,Roboto,Arial,sans-serif;
  box-shadow:0 8px 26px -8px rgba(9,22,40,.55),0 2px 6px rgba(9,22,40,.20),
             inset 0 1px 0 rgba(255,255,255,.10);
  transition:transform .18s cubic-bezier(.2,.8,.25,1),
             box-shadow .18s cubic-bezier(.2,.8,.25,1), filter .14s, opacity .18s;
}
#sbk-chat-fab .t{
  position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;
  clip:rect(0 0 0 0);white-space:nowrap;border:0;
}
#sbk-chat-fab:hover{
  transform:translateY(-2px);filter:brightness(1.10);
  box-shadow:0 16px 36px -10px rgba(9,22,40,.60),0 3px 8px rgba(9,22,40,.22),
             inset 0 1px 0 rgba(255,255,255,.14);
}
#sbk-chat-fab:active{transform:translateY(0)}
#sbk-chat-fab .i{display:inline-flex;align-items:center}
#sbk-chat-fab .i svg{animation:sbkNod 5.5s ease-in-out 2.5s 2 both;transform-origin:50% 70%}
@keyframes sbkNod{
  0%,88%,100%{transform:rotate(0)}
  91%{transform:rotate(-11deg)} 94%{transform:rotate(9deg)} 97%{transform:rotate(-4deg)}
}
#sbk-chat-n{
  position:absolute;top:-3px;right:-3px;
  min-width:21px;height:21px;padding:0 6px;border-radius:999px;
  background:#bc1e2d;color:#fff;font-size:11.5px;font-weight:750;line-height:1;
  display:none;align-items:center;justify-content:center;
  box-shadow:0 0 0 2px #fff,0 2px 8px -2px rgba(188,30,45,.9);
  animation:sbkPop .34s cubic-bezier(.16,1,.3,1) both;
}
#sbk-chat-n.on{display:inline-flex}
@keyframes sbkPop{
  0%{transform:scale(.4);opacity:0}
  62%{transform:scale(1.15);opacity:1}
  100%{transform:scale(1)}
}
#sbk-chat-panel{
  position:fixed;left:20px;bottom:20px;z-index:99999;
  width:404px;height:min(660px,calc(100vh - 40px));
  display:none;flex-direction:column;overflow:hidden;
  background:#fff;border-radius:18px;
  box-shadow:0 30px 80px -20px rgba(6,16,34,.55),0 4px 14px rgba(6,16,34,.18);
  transform-origin:bottom left;
}
#sbk-chat-panel.on{display:flex;animation:sbkPanelIn .26s cubic-bezier(.16,1,.3,1) both}
@keyframes sbkPanelIn{
  from{opacity:0;transform:translateY(14px) scale(.97)}
  to{opacity:1;transform:none}
}
#sbk-chat-panel .bar{
  flex:0 0 auto;display:flex;align-items:center;gap:10px;padding:11px 12px 11px 14px;
  background:linear-gradient(180deg,#2a4c82 -40%,#18335e 46%,#0e1f3d 100%);
  color:#fff;position:relative;
}
#sbk-chat-panel .bar::after{
  content:"";position:absolute;left:0;right:0;bottom:0;height:3px;
  background:linear-gradient(90deg,#bc1e2d 0%,#bc1e2d 46%,#0370cd 100%);
}
#sbk-chat-panel .bar img{height:22px;width:auto;display:block;
  background:#fff;padding:4px 8px;border-radius:8px}
#sbk-chat-panel .bar b{
  font:660 14px/1.25 "Inter","Segoe UI",-apple-system,BlinkMacSystemFont,Roboto,Arial,sans-serif;
}
#sbk-chat-panel .bar span{
  display:block;font-weight:400;font-size:11px;color:#9db2cf;
  font-family:"Inter","Segoe UI",-apple-system,BlinkMacSystemFont,Roboto,Arial,sans-serif;
}
#sbk-chat-panel .bar .sp{margin-left:auto}
#sbk-chat-panel .bar button{
  background:transparent;border:0;color:#cfe0f4;cursor:pointer;padding:7px;
  border-radius:8px;display:inline-flex;line-height:0;
  transition:background .14s,color .14s,transform .14s;
}
#sbk-chat-panel .bar button:hover{background:rgba(255,255,255,.12);color:#fff}
#sbk-chat-panel .bar button:active{transform:scale(.93)}
#sbk-chat-panel iframe{flex:1;width:100%;border:0;display:block;background:#f4f6f9}

/* The red number on a "Live chat" link (the account menu). */
.sbk-chat-navdot{
  min-width:19px;height:19px;padding:0 5px;margin-left:6px;border-radius:999px;
  background:#bc1e2d;color:#fff!important;font-size:11px;font-weight:750;line-height:1;
  display:inline-flex;align-items:center;justify-content:center;
  box-shadow:0 2px 7px -2px rgba(188,30,45,.9);
  animation:sbkPop .34s cubic-bezier(.16,1,.3,1) both;
}
.sbk-chat-navdot[hidden]{display:none!important}

/* The card that says who wrote, and what - WhatsApp's pop-up. */
#sbk-chat-toast{
  position:fixed;left:20px;bottom:88px;z-index:99997;width:min(330px,calc(100vw - 40px));
  display:none;align-items:flex-start;gap:11px;padding:13px 14px;
  background:#fff;border-radius:14px;cursor:pointer;text-align:left;
  border:0;border-left:4px solid #bc1e2d;
  box-shadow:0 18px 44px -14px rgba(6,16,34,.45),0 2px 8px rgba(6,16,34,.14);
  font:400 13.5px/1.45 "Inter","Segoe UI",-apple-system,BlinkMacSystemFont,Roboto,Arial,sans-serif;
  color:#0f1c2e;
}
#sbk-chat-toast.on{display:flex;animation:sbkToastIn .3s cubic-bezier(.16,1,.3,1) both}
@keyframes sbkToastIn{
  from{opacity:0;transform:translateY(12px) scale(.97)}
  to{opacity:1;transform:none}
}
#sbk-chat-toast .av{
  flex:0 0 auto;width:38px;height:38px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  background:linear-gradient(140deg,#2a4c82,#18335e);
  color:#fff;font-size:13px;font-weight:730;letter-spacing:.4px;
}
#sbk-chat-toast .tx{min-width:0;flex:1}
#sbk-chat-toast .tx b{display:block;font-size:13.5px;font-weight:680;margin-bottom:1px;
  overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
#sbk-chat-toast .tx span{display:block;color:#54617a;
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
#sbk-chat-toast .x{
  flex:0 0 auto;background:transparent;border:0;color:#8b96aa;cursor:pointer;
  padding:2px;margin:-2px -2px 0 0;line-height:0;border-radius:6px;
}
#sbk-chat-toast .x:hover{background:#eef1f6;color:#0f1c2e}

@media (max-width:640px){
  #sbk-chat-fab{left:14px;bottom:14px;width:52px;height:52px}
  #sbk-chat-panel{right:0;left:0;bottom:0;top:0;width:auto;height:auto;border-radius:0}
  #sbk-chat-toast{left:14px;right:14px;bottom:78px;width:auto}
}
/* Small phones: the logo pushed "SBK Global Auto Trading" and its line under it onto
   four lines; the name already says SBK. */
@media (max-width:380px){
  #sbk-chat-panel .bar img{display:none}
}
@media (prefers-reduced-motion:reduce){
  #sbk-chat-fab,#sbk-chat-fab .i svg,#sbk-chat-n,#sbk-chat-panel{
    animation:none!important;transition:none!important}
}
@media print{ #sbk-chat-fab,#sbk-chat-panel{display:none!important} }
</style>

<button type="button" id="sbk-chat-fab"
        title="{{ $chatIsDesk ? 'Open the chat desk' : 'Chat with a person online' }}"
        aria-label="{{ $chatIsDesk ? 'Open the chat desk' : 'Chat with a person online' }}">
  <span class="i" aria-hidden="true">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 8.9 8.9 0 0 1-4-.9L3 21l1.9-4.6A8.4 8.4 0 0 1 12 3a8.4 8.4 0 0 1 9 8.5z"/>
      <path d="M8.5 11.5h.01M12 11.5h.01M15.5 11.5h.01"/>
    </svg>
  </span>
  <span class="t" id="sbk-chat-label">{{ $chatIsDesk ? 'Open the chat desk' : 'Chat with a person online' }}</span>
  <span id="sbk-chat-n">0</span>
</button>

<div id="sbk-chat-panel" role="dialog" aria-label="SBK live chat">
  <div class="bar">
    <img src="{{ asset('assets/chat/img/sbk-logo.png') }}" alt="SBK">
    <div>
      <b id="sbk-chat-title">{{ $chatIsDesk ? 'Chat desk' : 'SBK Global Auto Trading' }}</b>
      <span id="sbk-chat-sub">{{ $chatIsDesk ? 'Your conversations' : 'We usually reply within minutes' }}</span>
    </div>
    <div class="sp"></div>
    <button type="button" id="sbk-chat-open" title="Open in a full window">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
           stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M14 4h6v6M20 4l-8 8M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>
      </svg>
    </button>
    <button type="button" id="sbk-chat-close" title="Close">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
           stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 6l12 12M18 6L6 18"/>
      </svg>
    </button>
  </div>
  {{-- Microphone and camera for calls, autoplay so the other voice is heard at once. --}}
  <iframe id="sbk-chat-frame" title="SBK live chat" src="about:blank"
          allow="microphone; camera; autoplay; clipboard-write"></iframe>
</div>

<div id="sbk-chat-toast" role="status" aria-live="polite">
  <span class="av" id="sbk-toast-av">?</span>
  <span class="tx">
    <b id="sbk-toast-who">—</b>
    <span id="sbk-toast-text"></span>
  </span>
  <button type="button" class="x" id="sbk-toast-x" aria-label="Close">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
  </button>
</div>

<script>
(function () {
  var CHAT  = @json(route('chat.app'));
  var PING  = @json(route('chat.api.ping'));
  var DESK  = {{ $chatIsDesk ? 'true' : 'false' }};

  var fab    = document.getElementById('sbk-chat-fab');
  var panel  = document.getElementById('sbk-chat-panel');
  var frame  = document.getElementById('sbk-chat-frame');
  var dot    = document.getElementById('sbk-chat-n');
  var open   = false, loaded = false;
  if (!fab || !window.fetch) { return; }

  /* ---- "Live chat" links on the page (the account menu): a red number on
     each, and a click opens the panel rather than leaving the page. */
  var navDots = [];
  function adoptLinks() {
    var links = document.querySelectorAll('a[data-sbk-chat]');
    for (var i = 0; i < links.length; i++) {
      var a = links[i];
      if (a.getAttribute('data-sbk-adopted')) { continue; }
      a.setAttribute('data-sbk-adopted', '1');
      if (DESK) {
        var w = document.createTreeWalker(a, NodeFilter.SHOW_TEXT), t;
        while ((t = w.nextNode())) {
          if (t.nodeValue.indexOf('Live chat') !== -1) { t.nodeValue = t.nodeValue.replace('Live chat', 'Chat desk'); break; }
        }
      }
      var badge = document.createElement('span');
      badge.className = 'sbk-chat-navdot';
      badge.hidden = true;
      badge.textContent = '0';
      a.appendChild(badge);
      navDots.push(badge);
    }
  }
  adoptLinks();
  function setNavCount(n) {
    for (var i = 0; i < navDots.length; i++) {
      navDots[i].textContent = n > 99 ? '99+' : n;
      navDots[i].hidden = (n === 0);
    }
  }

  function show() {
    /* The chat is fetched only when somebody actually wants it - a page nobody
       clicks costs one button and nothing else. */
    if (!loaded) { frame.src = CHAT; loaded = true; }
    panel.classList.add('on');
    fab.style.opacity = '0';
    fab.style.pointerEvents = 'none';
    open = true;
    dot.classList.remove('on');
    setNavCount(0);
    stopRinging();
    hideToast();

    /* The desk's computer can announce a message with the site behind other
       work, but only once the browser has been told yes - after a click. */
    if (DESK && window.Notification && Notification.permission === 'default') {
      try { Notification.requestPermission(); } catch (e) {}
    }
  }
  function hide() {
    panel.classList.remove('on');
    fab.style.opacity = '';
    fab.style.pointerEvents = '';
    open = false;
    ask();
  }

  fab.addEventListener('click', show);
  document.addEventListener('click', function (ev) {
    var a = ev.target.closest ? ev.target.closest('a[data-sbk-chat]') : null;
    if (!a) { return; }
    if (ev.metaKey || ev.ctrlKey || ev.shiftKey || ev.button) { return; }
    ev.preventDefault();
    show();
  });
  document.getElementById('sbk-chat-close').addEventListener('click', hide);
  document.getElementById('sbk-chat-open').addEventListener('click', function () {
    window.open(CHAT, '_blank', 'noopener');
    hide();
  });
  document.addEventListener('keydown', function (ev) {
    if (ev.key === 'Escape' && open) { hide(); }
  });

  /* ---- the heartbeat and the number waiting, one call every ten seconds.
     A hidden tab asks nothing - except the desk's, twice a minute, so a
     message still reaches them while the site sits behind other work. */
  var hiddenAsk = 0;
  function ask() {
    if (document.hidden) {
      if (!DESK) { return; }
      var t = Date.now();
      if (t - hiddenAsk < 29000) { return; }
      hiddenAsk = t;
    }
    fetch(PING + '?from=site', { credentials: 'same-origin', cache: 'no-store' })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (j) {
        if (!j || !j.ok) { return; }
        var n = (open || !j.unread) ? 0 : j.unread;
        dot.textContent = n > 99 ? '99+' : n;
        dot.classList.toggle('on', n > 0);
        setNavCount(n);
        if (j.ringing && !open) { ringing(j.ringing); }
        else { stopRinging(); popup(j.last); }
      })
      .catch(function () { /* the badge stays as it was, which is correct */ });
  }

  /* ---- the WhatsApp card: once per message, only while the panel is shut;
     it closes itself after eight seconds, and a click opens the chat. */
  var toast    = document.getElementById('sbk-chat-toast');
  var toastAv  = document.getElementById('sbk-toast-av');
  var toastWho = document.getElementById('sbk-toast-who');
  var toastTx  = document.getElementById('sbk-toast-text');
  var shown    = 0, hideTimer = null, ac = null, primed = false;

  function initials(name) {
    var p = String(name || '').trim().split(/\s+/);
    return ((p[0] || '?')[0] + (p.length > 1 ? p[p.length - 1][0] : '')).toUpperCase();
  }

  function chime() {
    try {
      ac = ac || new (window.AudioContext || window.webkitAudioContext)();
      var o = ac.createOscillator(), g = ac.createGain();
      o.connect(g); g.connect(ac.destination);
      o.type = 'sine'; o.frequency.value = 880;
      g.gain.setValueAtTime(0.0001, ac.currentTime);
      g.gain.exponentialRampToValueAtTime(0.12, ac.currentTime + 0.01);
      g.gain.exponentialRampToValueAtTime(0.0001, ac.currentTime + 0.30);
      o.start(); o.stop(ac.currentTime + 0.32);
    } catch (e) {}
  }

  function hideToast() {
    toast.classList.remove('on');
    if (hideTimer) { clearTimeout(hideTimer); hideTimer = null; }
  }

  function popup(last) {
    if (!toast) { return; }
    /* The first answer only sets the mark: the card is for something
       ARRIVING, not for something that was already waiting. */
    if (!primed) {
      primed = true;
      shown  = last ? last.id : 0;
      return;
    }
    if (!last || open) { hideToast(); return; }
    if (last.id <= shown) { return; }
    shown = last.id;

    toastAv.textContent  = initials(last.from);
    toastWho.textContent = last.from || 'SBK';
    toastTx.textContent  = last.text || '';
    toast.classList.add('on');
    chime();

    if (hideTimer) { clearTimeout(hideTimer); }
    hideTimer = setTimeout(hideToast, 8000);

    /* With the site NOT in front, the desk's computer says it in its own
       notification area. Staff only. */
    if (DESK && (document.hidden || !document.hasFocus())
        && window.Notification && Notification.permission === 'granted') {
      try {
        var sys = new Notification(last.from || 'SBK', {
          body: last.text || 'New message', tag: 'sbk-chat-' + last.id, renotify: true });
        sys.onclick = function () { sys.close(); window.focus(); stopRinging(); hideToast(); show(); };
      } catch (e) {}
    }
  }

  /* ---- somebody is CALLING: the same card, but it stays up and rings until
     the call is answered, declined or given up on. The answer itself happens
     inside the chat - a browser hands a page the microphone only after a tap. */
  var ringingId = 0, ringTimer = null;
  function ringing(r) {
    if (!toast) { return; }
    if (ringingId !== r.id) {
      ringingId = r.id;
      toastAv.textContent  = initials(r.from);
      toastWho.textContent = (r.from || 'Somebody') + ' is calling…';
      toastTx.textContent  = (r.kind === 'video' ? 'Video call' : 'Voice call') + ' — tap to answer';
      toast.classList.add('on');
      if (hideTimer) { clearTimeout(hideTimer); hideTimer = null; }
      if (ringTimer) { clearInterval(ringTimer); }
      chime();
      ringTimer = setInterval(chime, 2000);
    }
  }
  function stopRinging() {
    if (!ringingId) { return; }
    ringingId = 0;
    if (ringTimer) { clearInterval(ringTimer); ringTimer = null; }
    hideToast();
  }

  toast.addEventListener('click', function () { stopRinging(); hideToast(); show(); });
  document.getElementById('sbk-toast-x').addEventListener('click', function (ev) {
    ev.stopPropagation();
    hideToast();
  });

  /* ---- is somebody else already standing in this corner? Anything fixed,
     floating above us and overlapping the button pushes us up, only while it
     is really there. Checked twice: widgets arrive after the page loads. */
  function stepAside() {
    fab.style.bottom = '';
    panel.style.bottom = '';
    var mine = fab.getBoundingClientRect();
    var myZ  = parseInt(getComputedStyle(fab).zIndex, 10) || 0;
    var hit  = false;
    var all  = document.querySelectorAll('iframe,div,button,a');
    for (var i = 0; i < all.length && !hit; i++) {
      var el = all[i];
      if (el === fab || fab.contains(el) || panel.contains(el) || panel === el || el === toast || toast.contains(el)) { continue; }
      var s = getComputedStyle(el);
      if (s.position !== 'fixed' || s.display === 'none' || s.visibility === 'hidden') { continue; }
      if ((parseInt(s.zIndex, 10) || 0) <= myZ) { continue; }
      var r = el.getBoundingClientRect();
      if (r.width < 30 || r.height < 30 || r.width > 500) { continue; }
      hit = !(r.right < mine.left || r.left > mine.right ||
              r.bottom < mine.top || r.top > mine.bottom);
    }
    fab.style.bottom   = hit ? '92px' : '';
    panel.style.bottom = hit ? '92px' : '';
  }
  setTimeout(stepAside, 1200);
  setTimeout(stepAside, 5000);
  window.addEventListener('resize', stepAside);

  ask();
  setInterval(ask, 10000);
  document.addEventListener('visibilitychange', function () { if (!document.hidden) { ask(); } });

  /* The chat asks to be closed when somebody signs out inside the panel. */
  window.addEventListener('message', function (ev) {
    if (ev.origin !== window.location.origin) { return; }
    if (ev.data === 'sbk-chat-close') { hide(); }
  });
})();
</script>
@endif
