{{-- Floating chat assistant (POST /chatbot). Self-contained: no build step needed.
     Uses Gemini when GEMINI_API_KEY is set, otherwise the server answers in basic
     (rule-based) mode. CHATBOT_ENABLED=false hides it. --}}
@if (config('chatbot.enabled'))
<style>
  /* Button art follows the favicon: gold house on the navy rounded square, with a smiling face for the window. */
  #hx-chat-btn{position:fixed;right:20px;bottom:20px;z-index:60;width:60px;height:60px;padding:0;border:0;border-radius:14px;background:none;box-shadow:0 8px 22px rgba(13,23,101,.4);cursor:pointer;transition:transform .2s,box-shadow .2s}
  #hx-chat-btn:hover{transform:translateY(-2px);box-shadow:0 12px 26px rgba(13,23,101,.5)}
  #hx-chat-btn:focus-visible{outline:3px solid #DDA10D;outline-offset:3px}
  #hx-chat-btn svg{display:block;width:100%;height:100%}
  #hx-chat-btn .hx-ic-close{display:none}
  /* An occasional, slow blink on the button's face (its SVG is inline, not <use>, so this reaches the eyes). */
  #hx-chat-btn .hx-eyes{transform-box:fill-box;transform-origin:center;animation:hx-blink 5s infinite}
  @keyframes hx-blink{0%,92%,100%{transform:scaleY(1)}95%{transform:scaleY(.1)}}
  /* A quick wobble on the launcher, played once (via JS) right as the speech
     cloud pops in, so the icon and the message read as one moment. */
  #hx-chat-btn.wobble{animation:hx-wobble .55s ease-in-out}
  @keyframes hx-wobble{0%,100%{transform:rotate(0)}20%{transform:rotate(-9deg)}40%{transform:rotate(7deg)}60%{transform:rotate(-5deg)}80%{transform:rotate(3deg)}}
  #hx-chat-btn[aria-expanded="true"] .hx-ic-open{display:none}
  #hx-chat-btn[aria-expanded="true"] .hx-ic-close{display:block}
  #hx-chat{position:fixed;right:20px;bottom:88px;z-index:60;width:370px;max-width:calc(100vw - 24px);height:520px;max-height:calc(100vh - 110px);background:#fff;border-radius:14px;box-shadow:0 10px 40px rgba(0,0,0,.25);display:none;flex-direction:column;overflow:hidden;font-size:14px}
  #hx-chat.open{display:flex}
  #hx-chat .hx-head{background:#0D1765;color:#fff;padding:12px 14px;display:flex;justify-content:space-between;align-items:center;gap:10px}
  #hx-chat .hx-brand{display:flex;align-items:center;gap:10px;min-width:0}
  #hx-chat .hx-brand svg{flex:none;width:36px;height:36px;border-radius:9px;box-shadow:0 0 0 1px rgba(255,255,255,.18)}
  #hx-chat .hx-head strong{display:block;font-size:15px}
  #hx-chat .hx-head small{display:block;opacity:.8;font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  #hx-chat .hx-brand > div{min-width:0}
  #hx-chat .hx-actions button{display:flex;align-items:center;justify-content:center;width:30px;height:30px;padding:0;border-radius:6px}
  #hx-chat .hx-actions button:hover{background:rgba(255,255,255,.12)}
  #hx-chat .hx-actions{display:flex;align-items:center;flex:none}
  #hx-chat .hx-head button{background:none;border:0;color:#fff;cursor:pointer;line-height:1;padding:4px 6px;font-size:13px;opacity:.85}
  #hx-chat .hx-head button:hover{opacity:1}
  #hx-msgs{flex:1;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:8px;background:#F7F6FF}
  .hx-m{max-width:85%;padding:8px 12px;border-radius:12px;white-space:pre-wrap;overflow-wrap:anywhere;line-height:1.45}
  .hx-m a{color:#000080;text-decoration:underline}
  .hx-bot{background:#fff;align-self:flex-start;border:1px solid #e5e7eb;border-bottom-left-radius:4px}
  .hx-user{background:#000080;color:#fff;align-self:flex-end;border-bottom-right-radius:4px}
  .hx-user a{color:#fff}
  .hx-err{border-color:#f5c26b;background:#fffaf0}
  .hx-retry{margin-top:6px;display:inline-block;background:none;border:1px solid #000080;color:#000080;border-radius:6px;padding:2px 10px;font-size:12px;cursor:pointer}
  .hx-typing{display:flex;gap:4px;padding:12px}
  .hx-typing span{width:6px;height:6px;border-radius:50%;background:#9ca3af;animation:hx-b 1s infinite}
  .hx-typing span:nth-child(2){animation-delay:.15s}.hx-typing span:nth-child(3){animation-delay:.3s}
  @keyframes hx-b{0%,80%,100%{opacity:.3;transform:translateY(0)}40%{opacity:1;transform:translateY(-3px)}}
  @media (prefers-reduced-motion:reduce){.hx-typing span,#hx-chat-btn .hx-eyes,#hx-chat-btn{animation:none}#hx-chat-btn{transition:none}}
  #hx-chips{display:flex;flex-wrap:wrap;gap:6px;padding:0 12px 10px;background:#F7F6FF}
  #hx-chips button{background:#fff;border:1px solid #000080;color:#000080;border-radius:999px;padding:4px 10px;font-size:12px;cursor:pointer}
  #hx-chips button:hover{background:#000080;color:#fff}
  #hx-chips .hx-prompt{width:100%;font-size:12px;font-weight:700;color:#374151}
  #hx-chips .hx-back{border-color:#9ca3af;color:#4b5563}
  #hx-chips .hx-more{border-style:dashed}
  #hx-chips:empty{display:none}
  #hx-form{display:flex;gap:8px;padding:10px;border-top:1px solid #e5e7eb;background:#fff}
  #hx-input{flex:1;min-width:0;border:1px solid #d1d5db;border-radius:8px;padding:8px 10px;outline:none;font-size:14px}
  #hx-input:focus{border-color:#000080}
  #hx-form button{background:#DAA520;color:#fff;border:0;border-radius:8px;padding:0 14px;font-weight:700;cursor:pointer}
  #hx-form button:disabled{opacity:.6;cursor:default}
  #hx-note{font-size:11px;color:#6b7280;text-align:center;padding:0 10px 8px;background:#fff}
  @media (max-width:480px){#hx-chat{right:12px;bottom:88px;height:calc(100vh - 116px)}#hx-chat-btn{right:14px;bottom:14px}}
  /* Narrow phones: tighter header so the subtitle stays on one line. */
  @media (max-width:400px){#hx-chat .hx-head{padding:10px 8px 10px 10px;gap:4px}#hx-chat .hx-brand{gap:8px}#hx-chat .hx-brand svg{width:32px;height:32px}#hx-chat .hx-head small{font-size:11px}#hx-chat .hx-actions button{width:26px}}

  /* Comic-style thought-cloud nudge: pops up FAST beside the launcher to invite
     a click, then cycles to the next line. Bold outline + flat hard shadow +
     two trailing bubbles read as a cartoon thought cloud pointing at the icon.
     Purely a come-on - it never opens the chat on its own and stops for good
     once the visitor opens the chat or dismisses it. */
  #hx-nudge{position:fixed;right:24px;bottom:96px;z-index:59;max-width:220px;background:#fff;border:3px solid #0D1765;border-radius:22px 22px 22px 6px;box-shadow:4px 4px 0 #0D1765;padding:12px 30px 12px 14px;font-size:13.5px;font-weight:600;line-height:1.4;color:#111827;cursor:pointer;opacity:0;visibility:hidden;transform:translateY(10px) scale(.5);transform-origin:bottom right;transition:opacity .5s cubic-bezier(.34,1.56,.64,1),transform .5s cubic-bezier(.34,1.56,.64,1),visibility .5s}
  #hx-nudge.on{opacity:1;visibility:visible;transform:translateY(0) scale(1)}
  #hx-nudge:hover{box-shadow:5px 5px 0 #0D1765}
  /* The two trailing bubbles that finish the thought-cloud shape, dwindling down toward the launcher. */
  #hx-nudge::before,#hx-nudge::after{content:"";position:absolute;background:#fff;border:3px solid #0D1765;border-radius:50%}
  #hx-nudge::before{width:15px;height:15px;right:30px;bottom:-21px}
  #hx-nudge::after{width:9px;height:9px;right:18px;bottom:-32px}
  #hx-nudge-close{position:absolute;top:5px;right:5px;width:22px;height:22px;border:0;background:none;color:#9ca3af;cursor:pointer;border-radius:6px;font-size:15px;line-height:1;display:flex;align-items:center;justify-content:center}
  #hx-nudge-close:hover{background:#f3f4f6;color:#374151}
  @media (prefers-reduced-motion:reduce){#hx-nudge{transition:none}}
  @media (max-width:480px){#hx-nudge{right:14px;bottom:96px;max-width:calc(100vw - 84px)}}
</style>

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <symbol id="hx-logo" viewBox="0 0 48 48">
    <rect width="48" height="48" rx="11" fill="#0D1765"/>
    <g fill="none" stroke="#DDA10D" stroke-width="4.2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M8 22.5 24 9l16 13.5"/>
      <path d="M13 25.5v4.5c0 6.4 4.9 10.5 11 10.5s11-4.1 11-10.5v-4.5"/>
    </g>
    <rect x="31.2" y="9.6" width="3.8" height="6.6" rx=".6" fill="#DDA10D"/>
    <g class="hx-eyes" fill="#DDA10D"><circle cx="19.6" cy="25.2" r="2.3"/><circle cx="28.4" cy="25.2" r="2.3"/></g>
    <path d="M19.8 30.6c2.4 2.4 6 2.4 8.4 0" fill="none" stroke="#DDA10D" stroke-width="2.4" stroke-linecap="round"/>
  </symbol>
</svg>

<button id="hx-chat-btn" type="button" aria-label="Chat with Homax Assistant" aria-expanded="false" aria-controls="hx-chat">
  <svg class="hx-ic-open" viewBox="0 0 48 48" aria-hidden="true">
    <rect width="48" height="48" rx="11" fill="#0D1765"/>
    <g fill="none" stroke="#DDA10D" stroke-width="4.2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M8 22.5 24 9l16 13.5"/>
      <path d="M13 25.5v4.5c0 6.4 4.9 10.5 11 10.5s11-4.1 11-10.5v-4.5"/>
    </g>
    <rect x="31.2" y="9.6" width="3.8" height="6.6" rx=".6" fill="#DDA10D"/>
    <g class="hx-eyes" fill="#DDA10D"><circle cx="19.6" cy="25.2" r="2.3"/><circle cx="28.4" cy="25.2" r="2.3"/></g>
    <path d="M19.8 30.6c2.4 2.4 6 2.4 8.4 0" fill="none" stroke="#DDA10D" stroke-width="2.4" stroke-linecap="round"/>
  </svg>
  <svg class="hx-ic-close" viewBox="0 0 48 48" aria-hidden="true">
    <rect width="48" height="48" rx="11" fill="#0D1765"/>
    <path d="M17 17l14 14M31 17 17 31" stroke="#DDA10D" stroke-width="4" stroke-linecap="round"/>
  </svg>
</button>
<div id="hx-nudge" role="status">
  <button type="button" id="hx-nudge-close" aria-label="Dismiss">&times;</button>
  <span id="hx-nudge-text"></span>
</div>
<div id="hx-chat" role="dialog" aria-label="Homax Homes assistant">
  <div class="hx-head">
    <div class="hx-brand">
      <svg viewBox="0 0 48 48" aria-hidden="true"><use href="#hx-logo"/></svg>
      <div><strong>Homax Assistant</strong><small>Ask about projects, prices &amp; locations</small></div>
    </div>
    <div class="hx-actions">
      <button type="button" id="hx-chat-reset" title="Start a new chat" aria-label="Start a new chat">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
      </button>
      <button type="button" id="hx-chat-close" title="Close" aria-label="Close chat">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </div>
  </div>
  <div id="hx-msgs" aria-live="polite"></div>
  <div id="hx-chips"></div>
  <form id="hx-form" autocomplete="off">
    <input id="hx-input" type="text" maxlength="1000" placeholder="Type your question..." aria-label="Your message">
    <button type="submit">Send</button>
  </form>
  <div id="hx-note">AI answers can be wrong. Please confirm details with our team.</div>
</div>

<script>
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var btn = $('hx-chat-btn'), box = $('hx-chat'), msgs = $('hx-msgs'), chips = $('hx-chips'),
      form = $('hx-form'), input = $('hx-input'), sendBtn = form.querySelector('button');
  var url = @json(route('chatbot')), csrf = @json(csrf_token());
  var STORE = 'hx-chat-v1', MAX_SENT = 12, busy = false;
  var WELCOME = 'Hi! I\'m the Homax Homes assistant. Ask me about our projects, locations, prices or possession dates.';
  // Drill-down menu built from the live listings (App\Services\Chatbot\Suggestions):
  // a node with `children` opens a level, a node with `send` posts that message.
  var MENU = @json(app(\App\Services\Chatbot\Suggestions::class)->forRequest(request()));
  var path = [], menuOpen = true;

  // History survives page navigation within the tab. Storage can be blocked, so every access is guarded.
  var history = [];
  try { history = JSON.parse(sessionStorage.getItem(STORE) || '[]'); if (!Array.isArray(history)) history = []; } catch (e) { history = []; }
  function save() { try { sessionStorage.setItem(STORE, JSON.stringify(history.slice(-30))); } catch (e) {} }

  function esc(s) { return s.replace(/[&<>"']/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
  function add(role, text, cls) {
    var d = document.createElement('div');
    d.className = 'hx-m ' + (role === 'user' ? 'hx-user' : 'hx-bot') + (cls ? ' ' + cls : '');
    // Escape first, then linkify only http(s) URLs, so model output can never inject HTML.
    d.innerHTML = esc(text).replace(/(https?:\/\/[^\s<]+[^\s<.,;:!?)'"])/g, '<a href="$1" target="_blank" rel="noopener">$1</a>');
    msgs.appendChild(d); msgs.scrollTop = msgs.scrollHeight;
    return d;
  }
  function render() {
    msgs.innerHTML = '';
    add('model', WELCOME);
    history.forEach(function (m) { add(m.role, m.text); });
    path = []; menuOpen = !history.length;
    renderChips();
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
      chip('\u2190 Back', function () { path.pop(); renderChips(); }, 'hx-back');
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
    if (open) { if (!msgs.children.length) render(); input.focus(); msgs.scrollTop = msgs.scrollHeight; }
  }
  function typing() {
    var d = document.createElement('div'); d.className = 'hx-m hx-bot hx-typing';
    d.innerHTML = '<span></span><span></span><span></span>'; d.setAttribute('aria-label', 'Assistant is typing');
    msgs.appendChild(d); msgs.scrollTop = msgs.scrollHeight; return d;
  }
  function fail(text, retryText) {
    var d = add('model', text, 'hx-err');
    if (retryText) {
      var r = document.createElement('button'); r.type = 'button'; r.className = 'hx-retry'; r.textContent = 'Try again';
      r.onclick = function () { d.remove(); send(retryText, true); };
      d.appendChild(document.createElement('br')); d.appendChild(r);
    }
  }

  function send(text, isRetry) {
    text = (text || '').trim();
    if (!text || busy) return;
    busy = true; sendBtn.disabled = true; menuOpen = false; path = []; chips.innerHTML = '';
    if (!isRetry) add('user', text);
    var outgoing = history.concat([{role: 'user', text: text}]).slice(-MAX_SENT);
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
        dots.remove();
        var j = res.body;
        if (res.status === 419) return fail('Your session expired. Please refresh the page and try again.');
        if (res.status === 200 && j.reply) {
          history.push({role: 'user', text: text}, {role: 'model', text: j.reply}); save();
          return add('model', j.reply);
        }
        fail(j.reply || 'Something went wrong. Please try again.', res.status === 429 ? null : text);
      })
      .catch(function () { dots.remove(); fail('Could not reach the server. Check your connection and try again.', text); })
      .finally(function () { clearTimeout(timer); busy = false; sendBtn.disabled = false; renderChips(); input.focus(); });
  }

  // Speech-bubble nudge: a few lines inviting the visitor to ask for help,
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
  var nudgeIndex = 0, nudgeTimer = null, nudgeDone = true;
  try { nudgeDone = sessionStorage.getItem(NUDGE_STORE) === '1' || history.length > 0; } catch (e) {}

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
    // One wobble on the launcher itself, timed to the cloud's pop so the two
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
  form.onsubmit = function (e) { e.preventDefault(); var t = input.value; input.value = ''; send(t); };
})();
</script>
@endif
