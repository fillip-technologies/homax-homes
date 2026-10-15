{{-- Floating chat assistant (POST /chatbot). Self-contained: no build step needed.
     Uses Gemini when GEMINI_API_KEY is set, otherwise the server answers in basic
     (rule-based) mode. CHATBOT_ENABLED=false hides it.
     Styled after a messaging app: green header, wallpaper, bubbles with tails,
     timestamps + ticks, quick-reply pills and a round send button. --}}
@if (config('chatbot.enabled'))
@php
    // The project whose page this is (null elsewhere): the chat greets the visitor by its name.
    $hxProject = null;
    try {
        $hxProp = app(\App\Services\Chatbot\Suggestions::class)->currentProperty(request());
        if ($hxProp) {
            $hxProject = [
                'id' => $hxProp->id,
                'title' => $hxProp->title,
                'developer' => $hxProp->developer_name ?: optional($hxProp->owner)->name,
            ];
        }
    } catch (\Throwable $e) {
        report($e);
    }
@endphp
<style>
  #hx-chat,#hx-chat-btn,#hx-nudge{--wa-green:#008069;--wa-accent:#00A884;--wa-out:#D9FDD3;--wa-in:#fff;--wa-wall:#EFEAE2;--wa-text:#111B21;--wa-muted:#667781;--wa-bar:#F0F2F5;--wa-tick:#53BDEB;--wa-shadow:0 1px .5px rgba(11,20,26,.13);font-family:"Segoe UI","Helvetica Neue",Helvetica,Arial,sans-serif}

  /* Launcher: round green chat button. */
  #hx-chat-btn{position:fixed;right:20px;bottom:20px;z-index:60;width:58px;height:58px;padding:0;border:0;border-radius:50%;background:var(--wa-accent);color:#fff;box-shadow:0 6px 16px rgba(11,20,26,.3);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:transform .2s,box-shadow .2s,background .2s}
  #hx-chat-btn:hover{transform:translateY(-2px);background:#029478;box-shadow:0 9px 22px rgba(11,20,26,.35)}
  #hx-chat-btn:focus-visible{outline:3px solid #fff;outline-offset:-6px;box-shadow:0 0 0 3px var(--wa-green),0 6px 16px rgba(11,20,26,.3)}
  #hx-chat-btn svg{display:block;width:30px;height:30px}
  #hx-chat-btn .hx-ic-open{width:36px;height:36px}
  #hx-chat-btn .hx-ic-close{display:none}
  #hx-chat-btn[aria-expanded="true"] .hx-ic-open{display:none}
  #hx-chat-btn[aria-expanded="true"] .hx-ic-close{display:block}
  /* A quick wobble played once (via JS) as the nudge pops in. */
  #hx-chat-btn.wobble{animation:hx-wobble .55s ease-in-out}
  @keyframes hx-wobble{0%,100%{transform:rotate(0)}20%{transform:rotate(-9deg)}40%{transform:rotate(7deg)}60%{transform:rotate(-5deg)}80%{transform:rotate(3deg)}}

  /* Window */
  #hx-chat{position:fixed;right:20px;bottom:90px;z-index:60;width:380px;max-width:calc(100vw - 24px);height:560px;max-height:calc(100vh - 112px);background:var(--wa-wall);border-radius:12px;box-shadow:0 12px 40px rgba(11,20,26,.35);display:none;flex-direction:column;overflow:hidden;font-size:14.2px;color:var(--wa-text)}
  #hx-chat.open{display:flex}

  /* Header: back arrow, avatar, contact name + presence, actions. */
  #hx-chat .hx-head{background:var(--wa-green);color:#fff;padding:8px 8px 8px 4px;display:flex;align-items:center;gap:4px;min-height:58px}
  #hx-chat .hx-head button{display:flex;align-items:center;justify-content:center;width:38px;height:38px;padding:0;border:0;border-radius:50%;background:none;color:#fff;cursor:pointer;flex:none}
  #hx-chat .hx-head button:hover{background:rgba(255,255,255,.12)}
  #hx-chat .hx-head button:focus-visible{outline:2px solid #fff;outline-offset:-2px}
  #hx-chat .hx-brand{display:flex;align-items:center;gap:10px;min-width:0;flex:1}
  #hx-chat .hx-avatar{flex:none;width:40px;height:40px;border-radius:50%;overflow:hidden;background:#fff;display:flex;box-shadow:0 0 0 1px rgba(0,0,0,.08)}
  #hx-chat .hx-avatar svg{width:100%;height:100%;display:block}
  #hx-chat .hx-brand > div{min-width:0}
  #hx-chat .hx-head strong{display:block;font-size:16.5px;font-weight:500;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  #hx-chat .hx-head small{display:block;font-size:13px;line-height:1.25;color:rgba(255,255,255,.82);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

  /* Conversation area: wallpaper behind the messages and the quick replies. */
  #hx-chat .hx-body{flex:1;min-height:0;display:flex;flex-direction:column;background-color:var(--wa-wall);background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200' viewBox='0 0 200 200'%3E%3Cg fill='none' stroke='%23000' stroke-opacity='.06' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 30l10-9 10 9v10H20z'/%3E%3Ccircle cx='95' cy='22' r='6'/%3E%3Cpath d='M150 18v12M144 24h12'/%3E%3Cpath d='M40 90q6-8 12 0t12 0'/%3E%3Crect x='120' y='80' width='18' height='14' rx='3'/%3E%3Cpath d='M170 100l8 8M178 100l-8 8'/%3E%3Ccircle cx='30' cy='150' r='4'/%3E%3Cpath d='M80 140l10-9 10 9v10H80z'/%3E%3Cpath d='M140 150q6-8 12 0t12 0'/%3E%3Cpath d='M60 185v10M55 190h10'/%3E%3Ccircle cx='175' cy='175' r='6'/%3E%3C/g%3E%3C/svg%3E")}
  #hx-msgs{flex:1;min-height:0;overflow-y:auto;padding:10px 12px 6px;display:flex;flex-direction:column;gap:2px;scrollbar-width:thin;scrollbar-color:rgba(11,20,26,.25) transparent}

  /* Centered info pills (day + AI disclaimer). */
  .hx-day{align-self:center;background:var(--wa-in);color:#54656F;font-size:12.5px;text-transform:uppercase;padding:5px 12px;border-radius:8px;box-shadow:var(--wa-shadow);margin:4px 0 8px}
  .hx-notice{align-self:center;max-width:92%;background:#FFEECD;color:#54656F;font-size:12.5px;line-height:1.4;text-align:center;padding:6px 10px;border-radius:8px;box-shadow:var(--wa-shadow);margin-bottom:8px}

  /* Bubbles */
  .hx-m{position:relative;max-width:82%;padding:6px 8px 7px 9px;border-radius:8px;box-shadow:var(--wa-shadow);white-space:pre-wrap;overflow-wrap:anywhere;line-height:1.4;font-size:14.2px;color:var(--wa-text)}
  .hx-m::after{content:"";display:block;clear:both}
  .hx-m a{color:#027EB5;text-decoration:underline}
  .hx-bot{background:var(--wa-in);align-self:flex-start;border-top-left-radius:0}
  .hx-user{background:var(--wa-out);align-self:flex-end;border-top-right-radius:0}
  /* Tail on the first bubble of a run; the rest of the run is plain rounded boxes. */
  .hx-bot::before,.hx-user::before{content:"";position:absolute;top:0;width:8px;height:13px}
  .hx-bot::before{left:-8px;background:var(--wa-in);clip-path:polygon(100% 0,100% 100%,0 0)}
  .hx-user::before{right:-8px;background:var(--wa-out);clip-path:polygon(0 0,100% 0,0 100%)}
  .hx-bot + .hx-bot,.hx-user + .hx-user{border-top-left-radius:8px;border-top-right-radius:8px}
  .hx-bot + .hx-bot::before,.hx-user + .hx-user::before{display:none}
  .hx-bot + .hx-user,.hx-user + .hx-bot,.hx-notice + .hx-bot{margin-top:8px}
  .hx-err{background:#FFF3F0;box-shadow:inset 3px 0 0 #E4572E,var(--wa-shadow)}
  .hx-bot.hx-err::before{background:#FFF3F0}
  .hx-retry{margin-top:6px;display:inline-block;background:#fff;border:1px solid var(--wa-green);color:var(--wa-green);border-radius:999px;padding:3px 12px;font-size:12.5px;font-weight:600;cursor:pointer}
  .hx-retry:hover{background:#E7F6F3}

  /* Time + delivery ticks, tucked into the bubble's bottom-right corner. */
  .hx-meta{float:right;margin:7px 0 -3px 12px;font-size:11px;line-height:1;color:var(--wa-muted);display:inline-flex;align-items:center;gap:3px;white-space:nowrap;user-select:none}
  .hx-tick{width:16px;height:11px;fill:none;stroke:currentColor;stroke-width:1.6;stroke-linecap:round;stroke-linejoin:round}
  .hx-user .hx-t2{display:none}
  .hx-user.hx-read .hx-t2{display:block}
  .hx-user.hx-read .hx-tick{color:var(--wa-tick)}

  /* "typing..." bubble */
  .hx-typing{display:flex;gap:4px;padding:13px 14px;align-items:center}
  .hx-typing::after{display:none}
  .hx-typing span{width:7px;height:7px;border-radius:50%;background:#8696A0;animation:hx-b 1.1s infinite}
  .hx-typing span:nth-child(2){animation-delay:.15s}.hx-typing span:nth-child(3){animation-delay:.3s}
  @keyframes hx-b{0%,80%,100%{opacity:.35;transform:translateY(0)}40%{opacity:1;transform:translateY(-3px)}}

  /* Quick replies */
  /* Two even columns so every row fills the width; an odd last chip spans the whole row. */
  #hx-chips{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px;padding:4px 12px 10px}
  #hx-chips:empty{display:none}
  #hx-chips button{background:var(--wa-in);border:0;color:var(--wa-green);border-radius:20px;padding:8px 12px;min-height:38px;font-size:13.5px;font-weight:600;line-height:1.25;text-align:center;cursor:pointer;box-shadow:var(--wa-shadow);font-family:inherit;display:flex;align-items:center;justify-content:center}
  #hx-chips button:last-of-type:nth-of-type(odd){grid-column:1 / -1}
  #hx-chips button:hover{background:#F5F6F6}
  #hx-chips button:focus-visible{outline:2px solid var(--wa-accent);outline-offset:1px}
  #hx-chips .hx-prompt{grid-column:1 / -1;font-size:12.5px;font-weight:600;color:#54656F}
  #hx-chips .hx-back{color:#54656F}
  #hx-chips .hx-more{background:rgba(255,255,255,.75)}

  /* Input bar */
  #hx-form{display:flex;align-items:center;gap:8px;padding:8px 10px;background:var(--wa-bar)}
  #hx-input{flex:1;min-width:0;height:42px;border:0;border-radius:21px;background:#fff;padding:0 16px;outline:none;font-size:15px;color:var(--wa-text);font-family:inherit}
  #hx-input::placeholder{color:var(--wa-muted)}
  #hx-input:focus{box-shadow:0 0 0 2px rgba(0,168,132,.35)}
  #hx-form button{flex:none;width:42px;height:42px;padding:0;border:0;border-radius:50%;background:var(--wa-accent);color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s}
  #hx-form button:hover{background:#029478}
  #hx-form button:focus-visible{outline:2px solid var(--wa-green);outline-offset:2px}
  #hx-form button:disabled{opacity:.55;cursor:default}
  #hx-form button svg{width:21px;height:21px;margin-left:2px}

  /* Nudge: looks like an incoming message preview beside the launcher. Purely a
     come-on - it never opens the chat on its own and stops for good once the
     visitor opens the chat or dismisses it. */
  #hx-nudge{position:fixed;right:20px;bottom:90px;z-index:59;max-width:230px;background:#fff;border-radius:10px 10px 0 10px;box-shadow:0 2px 14px rgba(11,20,26,.28);padding:8px 30px 9px 12px;font-size:14.2px;line-height:1.35;color:var(--wa-text);cursor:pointer;opacity:0;visibility:hidden;transform:translateY(10px) scale(.6);transform-origin:bottom right;transition:opacity .4s cubic-bezier(.34,1.56,.64,1),transform .4s cubic-bezier(.34,1.56,.64,1),visibility .4s}
  #hx-nudge.on{opacity:1;visibility:visible;transform:translateY(0) scale(1)}
  #hx-nudge::after{content:"";position:absolute;right:-8px;bottom:0;width:8px;height:13px;background:#fff;clip-path:polygon(0 0,0 100%,100% 100%)}
  #hx-nudge .hx-nudge-from{display:block;font-size:12.5px;font-weight:600;color:var(--wa-green);margin-bottom:1px}
  #hx-nudge-close{position:absolute;top:4px;right:4px;width:22px;height:22px;border:0;background:none;color:#8696A0;cursor:pointer;border-radius:50%;font-size:16px;line-height:1;display:flex;align-items:center;justify-content:center}
  #hx-nudge-close:hover{background:#F0F2F5;color:#54656F}

  @media (prefers-reduced-motion:reduce){.hx-typing span,#hx-chat-btn.wobble{animation:none}#hx-chat-btn,#hx-nudge{transition:none}}

  /* Phones: the conversation takes the whole screen, like a native chat. */
  @media (max-width:480px){
    #hx-chat{right:0;left:0;top:0;bottom:0;width:100%;max-width:none;height:100vh;height:100dvh;max-height:none;border-radius:0}
    #hx-form{padding-bottom:calc(8px + env(safe-area-inset-bottom,0px))}
    #hx-chat-btn{right:14px;bottom:14px}
    #hx-nudge{right:14px;bottom:84px;max-width:calc(100vw - 84px)}
  }
</style>

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  {{-- Bot avatar: the logo's own mark (all-gold roof, U body and four-pane window) on a white disc,
       so it reads like a contact photo and stays sharp down to 32px. --}}
  <symbol id="hx-logo" viewBox="0 0 48 48">
    <circle cx="24" cy="24" r="24" fill="#fff"/>
    <g transform="translate(24 24) scale(1.1) translate(-24 -24.5)" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="4.6">
      <path d="M9 22 24 9l15 13" stroke="#DAA520"/>
      <path d="M14 25v5c0 6 4.5 10 10 10s10-4 10-10v-5" stroke="#DAA520"/>
      <g fill="#DAA520" stroke="none">
        <rect x="19.4" y="22.4" width="4" height="4" rx=".8"/><rect x="24.6" y="22.4" width="4" height="4" rx=".8"/>
        <rect x="19.4" y="27.6" width="4" height="4" rx=".8"/><rect x="24.6" y="27.6" width="4" height="4" rx=".8"/>
      </g>
    </g>
  </symbol>
</svg>

<button id="hx-chat-btn" type="button" aria-label="Chat with Homax Assistant" aria-expanded="false" aria-controls="hx-chat">
  <svg class="hx-ic-open" viewBox="0 0 24 24" aria-hidden="true">
    <path fill="#fff" d="M12 2.5c-5.25 0-9.5 3.9-9.5 8.7 0 2.4 1.07 4.6 2.8 6.18-.16 1.45-.78 2.78-1.7 3.82a.6.6 0 0 0 .57.98c2.1-.38 3.78-1.2 4.9-2 .93.2 1.92.3 2.93.3 5.25 0 9.5-3.9 9.5-8.7S17.25 2.5 12 2.5Z"/>
    <g transform="translate(12 11.1) scale(.4) translate(-24 -24)" fill="none" stroke-width="5.2" stroke-linecap="round" stroke-linejoin="round" style="stroke:var(--wa-accent)">
      <path d="M9 22 24 9l15 13"/><path d="M14 25v5c0 6 4.5 10 10 10s10-4 10-10v-5"/>
    </g>
  </svg>
  <svg class="hx-ic-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true">
    <path d="M6 6l12 12M18 6 6 18"/>
  </svg>
</button>
<div id="hx-nudge" role="status">
  <button type="button" id="hx-nudge-close" aria-label="Dismiss">&times;</button>
  <span class="hx-nudge-from">Homax Assistant</span>
  <span id="hx-nudge-text"></span>
</div>
<div id="hx-chat" role="dialog" aria-label="Homax Homes assistant">
  <div class="hx-head">
    <button type="button" id="hx-chat-close" title="Close" aria-label="Close chat">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
    </button>
    <div class="hx-brand">
      <span class="hx-avatar"><svg viewBox="0 0 48 48" aria-hidden="true"><use href="#hx-logo"/></svg></span>
      <div><strong>Homax Assistant</strong><small id="hx-status">online</small></div>
    </div>
    <button type="button" id="hx-chat-reset" title="Start a new chat" aria-label="Start a new chat">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
    </button>
  </div>
  <div class="hx-body">
    <div id="hx-msgs" aria-live="polite"></div>
    <div id="hx-chips"></div>
  </div>
  <form id="hx-form" autocomplete="off">
    <input id="hx-input" type="text" maxlength="1000" placeholder="Type a message" aria-label="Your message">
    <button type="submit" aria-label="Send message">
      <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.4 20.4 20.85 12.9a1 1 0 0 0 0-1.84L3.4 3.6a1 1 0 0 0-1.39.91L2 9.12c0 .5.37.93.87.99L17 12 2.87 13.88c-.5.07-.87.5-.87 1l.01 4.61c0 .71.73 1.2 1.39.91Z"/></svg>
    </button>
  </form>
</div>

<script>
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var btn = $('hx-chat-btn'), box = $('hx-chat'), msgs = $('hx-msgs'), chips = $('hx-chips'),
      form = $('hx-form'), input = $('hx-input'), sendBtn = form.querySelector('button'),
      status = $('hx-status');
  var url = @json(route('chatbot')), csrf = @json(csrf_token());
  var STORE = 'hx-chat-v1', MAX_SENT = 12, busy = false, epoch = 0;
  // Greeting on any page: two short messages, like a person saying hello.
  var INTRO = ['Hi! 👋 I\'m the Homax Homes assistant.', 'Ask me about our projects, locations, prices or possession dates.'];
  var NOTICE = 'AI answers can be wrong. Please confirm details with our team.';
  // The project page the visitor is on (or null): the chat welcomes them to it.
  var PROJECT = @json($hxProject);
  var PROJECT_FOLLOW = 'I\'m the Homax Homes assistant. Ask me about prices, unit sizes, what\'s nearby or possession dates.';
  // Drill-down menu built from the live listings (App\Services\Chatbot\Suggestions):
  // a node with `children` opens a level, a node with `send` posts that message.
  var MENU = @json(app(\App\Services\Chatbot\Suggestions::class)->forRequest(request()));
  var path = [], menuOpen = true;
  // Two ticks, the second one only shown once the assistant has answered ("read").
  var TICKS = '<svg class="hx-tick" viewBox="0 0 16 11" aria-hidden="true"><path d="M1 5.8l2.9 2.9L9.6 1.5"/><path class="hx-t2" d="M6.4 8.1l.9.9 5.7-7.5"/></svg>';

  // History survives page navigation within the tab. Entries are
  // {role, text, t, parts?, local?, project?}: `parts` are the bubbles a reply was shown as,
  // `local` marks greetings made by the widget, which are never sent to the server.
  // Storage can be blocked, so every access is guarded.
  var history = [];
  try { history = JSON.parse(sessionStorage.getItem(STORE) || '[]'); if (!Array.isArray(history)) history = []; } catch (e) { history = []; }
  function save() { try { sessionStorage.setItem(STORE, JSON.stringify(history.slice(-30))); } catch (e) {} }
  function userTurns() { return history.filter(function (m) { return !m.local && m.role === 'user'; }).length; }

  function esc(s) { return s.replace(/[&<>"']/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
  function clock(ts) {
    try { return new Date(ts || Date.now()).toLocaleTimeString([], {hour: 'numeric', minute: '2-digit'}); } catch (e) { return ''; }
  }
  function setStatus() { status.textContent = busy ? 'typing…' : 'online'; }
  function add(role, text, cls, ts, read) {
    var d = document.createElement('div');
    d.className = 'hx-m ' + (role === 'user' ? 'hx-user' : 'hx-bot') + (cls ? ' ' + cls : '') + (read ? ' hx-read' : '');
    // Escape first, then linkify only http(s) URLs, so model output can never inject HTML.
    d.innerHTML = esc(text).replace(/(https?:\/\/[^\s<]+[^\s<.,;:!?)'"])/g, '<a href="$1" target="_blank" rel="noopener">$1</a>')
      + '<span class="hx-meta">' + esc(clock(ts)) + (role === 'user' ? TICKS : '') + '</span>';
    msgs.appendChild(d); msgs.scrollTop = msgs.scrollHeight;
    return d;
  }
  function pill(cls, text) {
    var d = document.createElement('div'); d.className = cls; d.textContent = text; msgs.appendChild(d);
  }
  function markRead() {
    var users = msgs.querySelectorAll('.hx-user');
    if (users.length) users[users.length - 1].classList.add('hx-read');
  }
  function typing() {
    var d = document.createElement('div'); d.className = 'hx-m hx-bot hx-typing';
    d.innerHTML = '<span></span><span></span><span></span>'; d.setAttribute('aria-label', 'Assistant is typing');
    msgs.appendChild(d); msgs.scrollTop = msgs.scrollHeight; return d;
  }

  // How long a person would take to type this: a beat plus a little per character, capped.
  function delayFor(text) { return Math.min(1500, 550 + text.length * 14); }

  // Shows `parts` one bubble at a time, each after a "typing..." pause. The first bubble
  // skips the pause unless opts.firstDelay is set (a reply already waited on the network).
  function say(parts, opts, done) {
    opts = opts || {};
    var mine = epoch, i = 0;
    busy = true; sendBtn.disabled = true; chips.innerHTML = ''; setStatus();
    (function next() {
      if (mine !== epoch) return;
      if (i >= parts.length) {
        busy = false; sendBtn.disabled = false; setStatus(); renderChips();
        if (done) done();
        return;
      }
      var text = parts[i];
      var wait = (i === 0 && !opts.firstDelay) ? 0 : delayFor(text);
      var dots = wait ? typing() : null;
      setTimeout(function () {
        if (mine !== epoch) return;
        if (dots) dots.remove();
        add('model', text);
        i++;
        next();
      }, wait);
    })();
  }

  function projectLine() {
    return 'Welcome to ' + PROJECT.title + '!' + (PROJECT.developer ? ' by ' + PROJECT.developer : '');
  }
  function welcomed() {
    return !!PROJECT && history.some(function (m) { return m.local && m.project === PROJECT.id; });
  }
  // Greets the visitor on this project's page, once per project per tab session.
  function welcomeProject() {
    if (!PROJECT || welcomed()) return false;
    // "I'm the assistant, ask me about..." is said once per session; later projects only get the welcome line.
    var introduced = history.some(function (m) { return m.local && m.intro; });
    var parts = introduced ? [projectLine()] : [projectLine(), PROJECT_FOLLOW];
    history.push({role: 'model', text: parts.join('\n\n'), parts: parts, t: Date.now(), local: true, project: PROJECT.id, intro: !introduced});
    save();
    say(parts, {firstDelay: true});
    return true;
  }

  function render() {
    epoch++;
    msgs.innerHTML = '';
    pill('hx-day', 'Today');
    pill('hx-notice', NOTICE);
    path = [];
    if (!history.length && PROJECT) {
      // Fresh chat on a project page: the project welcome replaces the generic hello.
      menuOpen = true; renderChips();
      welcomeProject();
      return;
    }
    // A chat that began with a project welcome already introduced itself; don't open with a second hello.
    if (!history.length || !history[0].local) {
      var first = history.length ? history[0].t : Date.now();
      INTRO.forEach(function (t) { add('model', t, '', first); });
    }
    history.forEach(function (m) {
      (m.parts && m.parts.length ? m.parts : [m.text]).forEach(function (t) { add(m.role, t, '', m.t, true); });
    });
    menuOpen = !userTurns();
    renderChips();
    // Came from another page with a conversation going: add the new project's welcome at the end.
    welcomeProject();
  }
  function chip(label, onclick, cls) {
    var c = document.createElement('button'); c.type = 'button'; c.textContent = label;
    if (cls) c.className = cls;
    c.onclick = onclick; chips.appendChild(c);
  }
  function renderChips() {
    chips.innerHTML = '';
    var root = MENU.children || [];
    if (busy || !root.length) return;
    // After the first answer the menu folds into one chip, so it doesn't crowd the chat.
    if (!menuOpen) return chip('Suggestions', function () { menuOpen = true; renderChips(); }, 'hx-more');
    var node = path[path.length - 1];
    if (node) {
      var p = document.createElement('div'); p.className = 'hx-prompt'; p.textContent = node.prompt || '';
      chips.appendChild(p);
      chip('← Back', function () { path.pop(); renderChips(); }, 'hx-back');
    }
    (node ? node.children : root).forEach(function (n) {
      chip(n.label, function () {
        if (n.children && n.children.length) { path.push(n); renderChips(); }
        else { path = []; menuOpen = false; send(n.send); }
      });
    });
    msgs.scrollTop = msgs.scrollHeight;
  }
  function toggle(open) {
    box.classList.toggle('open', open); btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) {
      if (!msgs.children.length) render();
      else if (!busy) welcomeProject();
      input.focus(); msgs.scrollTop = msgs.scrollHeight;
    }
  }
  function fail(text, retryText) {
    var d = add('model', text, 'hx-err');
    if (retryText) {
      var r = document.createElement('button'); r.type = 'button'; r.className = 'hx-retry'; r.textContent = 'Try again';
      r.onclick = function () { d.remove(); send(retryText, true); };
      d.appendChild(document.createElement('br')); d.appendChild(r);
    }
  }
  function endTurn() {
    busy = false; sendBtn.disabled = false; setStatus(); renderChips(); input.focus();
  }

  function send(text, isRetry) {
    text = (text || '').trim();
    if (!text || busy) return;
    var sentAt = Date.now();
    busy = true; sendBtn.disabled = true; menuOpen = false; path = []; chips.innerHTML = ''; setStatus();
    if (!isRetry) add('user', text, '', sentAt);
    // Only role + text of real turns go to the server; timestamps and widget greetings stay here.
    var outgoing = history.filter(function (m) { return !m.local; }).concat([{role: 'user', text: text}]).slice(-MAX_SENT)
      .map(function (m) { return {role: m.role, text: m.text}; });
    var dots = typing();
    var ctrl = window.AbortController ? new AbortController() : null;
    var timer = setTimeout(function () { if (ctrl) ctrl.abort(); }, 35000);

    fetch(url, {
      method: 'POST', credentials: 'same-origin', signal: ctrl ? ctrl.signal : undefined,
      headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest'},
      body: JSON.stringify({messages: outgoing, page: location.pathname})
    })
      .then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { return {status: r.status, body: j}; }); })
      .then(function (res) {
        clearTimeout(timer);
        dots.remove();
        var j = res.body;
        if (res.status === 419) { fail('Your session expired. Please refresh the page and try again.'); return endTurn(); }
        if (res.status === 200 && j.reply) {
          var parts = (j.messages && j.messages.length) ? j.messages : [j.reply];
          history.push({role: 'user', text: text, t: sentAt}, {role: 'model', text: j.reply, parts: parts, t: Date.now()}); save();
          markRead();
          // A reply arrives as several short messages, one after another.
          return say(parts, {}, function () { input.focus(); });
        }
        fail(j.reply || 'Something went wrong. Please try again.', res.status === 429 ? null : text);
        endTurn();
      })
      .catch(function () {
        clearTimeout(timer);
        dots.remove();
        fail('Could not reach the server. Check your connection and try again.', text);
        endTurn();
      });
  }

  // Message-preview nudge: a few lines inviting the visitor to ask for help,
  // shown one at a time with a pause between them, looping indefinitely.
  // Stops for good - and does not start at all - only once the visitor has
  // actually opened the chat or explicitly dismissed it this session; simply
  // watching it cycle never silences it on its own.
  var nudge = $('hx-nudge'), nudgeText = $('hx-nudge-text');
  var NUDGE_STORE = 'hx-chat-nudge-done', NUDGE_DELAY = 3000, NUDGE_SHOW = 5000, NUDGE_GAP = 1000;
  var NUDGES = [
    'Need help?',
    'Looking for a BHK?',
    'Not sure where to start?',
    'Questions on pricing?'
  ];
  if (PROJECT) NUDGES.unshift('Questions about ' + PROJECT.title + '?');
  var nudgeIndex = 0, nudgeTimer = null, nudgeDone = true;
  try { nudgeDone = sessionStorage.getItem(NUDGE_STORE) === '1' || userTurns() > 0; } catch (e) {}

  function stopNudging() {
    nudgeDone = true;
    if (nudgeTimer) { clearTimeout(nudgeTimer); nudgeTimer = null; }
    nudge.classList.remove('on');
    try { sessionStorage.setItem(NUDGE_STORE, '1'); } catch (e) {}
  }
  function showNudge() {
    if (nudgeDone || box.classList.contains('open')) return;
    nudgeText.textContent = NUDGES[nudgeIndex % NUDGES.length];
    nudgeIndex++;
    nudge.classList.add('on');
    // One wobble on the launcher itself, timed to the bubble's pop so the two
    // read as a single moment instead of separate, competing animations.
    btn.classList.add('wobble');
    setTimeout(function () { btn.classList.remove('wobble'); }, 600);
    nudgeTimer = setTimeout(function () {
      nudge.classList.remove('on');
      nudgeTimer = setTimeout(showNudge, NUDGE_GAP);
    }, NUDGE_SHOW);
  }
  if (!nudgeDone) nudgeTimer = setTimeout(showNudge, NUDGE_DELAY);

  nudge.onclick = function () { stopNudging(); toggle(true); };
  $('hx-nudge-close').onclick = function (e) { e.stopPropagation(); stopNudging(); };

  btn.onclick = function () { stopNudging(); toggle(!box.classList.contains('open')); };
  $('hx-chat-close').onclick = function () { toggle(false); btn.focus(); };
  $('hx-chat-reset').onclick = function () { if (busy) return; history = []; save(); render(); input.focus(); };
  box.addEventListener('keydown', function (e) { if (e.key === 'Escape') { toggle(false); btn.focus(); } });
  // While the assistant is still "typing", keep what the visitor wrote instead of dropping it.
  form.onsubmit = function (e) {
    e.preventDefault();
    if (busy) return;
    var t = input.value; input.value = ''; send(t);
  };
})();
</script>
@endif
