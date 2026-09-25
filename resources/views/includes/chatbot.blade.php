{{-- Floating chat assistant (POST /chatbot). Self-contained: no build step needed.
     Uses Gemini when GEMINI_API_KEY is set, otherwise the server answers in basic
     (rule-based) mode. CHATBOT_ENABLED=false hides it. --}}
@if (config('chatbot.enabled'))
<style>
  #hx-chat-btn{position:fixed;right:20px;bottom:20px;z-index:60;width:56px;height:56px;border-radius:50%;border:0;background:#000080;color:#fff;box-shadow:0 6px 20px rgba(0,0,0,.3);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:transform .2s}
  #hx-chat-btn:hover{background:#000066;transform:scale(1.05)}
  #hx-chat{position:fixed;right:20px;bottom:88px;z-index:60;width:370px;max-width:calc(100vw - 24px);height:520px;max-height:calc(100vh - 110px);background:#fff;border-radius:14px;box-shadow:0 10px 40px rgba(0,0,0,.25);display:none;flex-direction:column;overflow:hidden;font-size:14px}
  #hx-chat.open{display:flex}
  #hx-chat .hx-head{background:#000080;color:#fff;padding:12px 14px;display:flex;justify-content:space-between;align-items:center}
  #hx-chat .hx-head strong{display:block;font-size:15px}
  #hx-chat .hx-head small{opacity:.8;font-size:12px}
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
  @media (prefers-reduced-motion:reduce){.hx-typing span{animation:none}#hx-chat-btn{transition:none}}
  #hx-chips{display:flex;flex-wrap:wrap;gap:6px;padding:0 12px 10px;background:#F7F6FF}
  #hx-chips button{background:#fff;border:1px solid #000080;color:#000080;border-radius:999px;padding:4px 10px;font-size:12px;cursor:pointer}
  #hx-chips button:hover{background:#000080;color:#fff}
  #hx-form{display:flex;gap:8px;padding:10px;border-top:1px solid #e5e7eb;background:#fff}
  #hx-input{flex:1;min-width:0;border:1px solid #d1d5db;border-radius:8px;padding:8px 10px;outline:none;font-size:14px}
  #hx-input:focus{border-color:#000080}
  #hx-form button{background:#DAA520;color:#fff;border:0;border-radius:8px;padding:0 14px;font-weight:700;cursor:pointer}
  #hx-form button:disabled{opacity:.6;cursor:default}
  #hx-note{font-size:11px;color:#6b7280;text-align:center;padding:0 10px 8px;background:#fff}
  @media (max-width:480px){#hx-chat{right:12px;bottom:84px;height:calc(100vh - 110px)}#hx-chat-btn{right:14px;bottom:14px}}
</style>

<button id="hx-chat-btn" type="button" aria-label="Chat with us" aria-expanded="false" aria-controls="hx-chat">
  <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
</button>
<div id="hx-chat" role="dialog" aria-label="Homax Homes assistant">
  <div class="hx-head">
    <div><strong>Homax Assistant</strong><small>Ask about projects, prices &amp; locations</small></div>
    <div>
      <button type="button" id="hx-chat-reset" title="Start a new chat">New chat</button>
      <button type="button" id="hx-chat-close" aria-label="Close chat" style="font-size:20px">&times;</button>
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
  var SUGGEST = /\/property\/\d+/.test(location.pathname)
    ? ['Tell me about this project', 'What unit sizes are available?', 'What is nearby?', 'Request a callback']
    : ['Show ready-to-move homes', '2 BHK under 80 Lakh', 'Which cities do you cover?', 'Request a callback'];

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
    chips.innerHTML = '';
    if (!history.length) SUGGEST.forEach(function (s) {
      var c = document.createElement('button'); c.type = 'button'; c.textContent = s;
      c.onclick = function () { send(s); }; chips.appendChild(c);
    });
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
    busy = true; sendBtn.disabled = true; chips.innerHTML = '';
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
      .finally(function () { clearTimeout(timer); busy = false; sendBtn.disabled = false; input.focus(); });
  }

  btn.onclick = function () { toggle(!box.classList.contains('open')); };
  $('hx-chat-close').onclick = function () { toggle(false); btn.focus(); };
  $('hx-chat-reset').onclick = function () { if (busy) return; history = []; save(); render(); input.focus(); };
  box.addEventListener('keydown', function (e) { if (e.key === 'Escape') { toggle(false); btn.focus(); } });
  form.onsubmit = function (e) { e.preventDefault(); var t = input.value; input.value = ''; send(t); };
})();
</script>
@endif
