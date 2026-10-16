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

  /* Input bar: the message field carries a menu button on its right edge (like a bot menu). */
  #hx-form{display:flex;align-items:center;gap:8px;padding:8px 10px;background:var(--wa-bar)}
  #hx-form .hx-field{flex:1;min-width:0;display:flex;align-items:center;height:42px;border-radius:21px;background:#fff;padding-right:4px}
  #hx-form .hx-field:focus-within{box-shadow:0 0 0 2px rgba(0,168,132,.35)}
  #hx-input{flex:1;min-width:0;height:100%;border:0;background:transparent;padding:0 6px 0 16px;outline:none;font-size:15px;color:var(--wa-text);font-family:inherit}
  #hx-input::placeholder{color:var(--wa-muted)}
  #hx-menu-btn{position:relative;flex:none;width:34px;height:34px;padding:0;border:0;border-radius:50%;background:none;color:#54656F;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s,color .15s}
  #hx-menu-btn:hover,#hx-menu-btn[aria-expanded="true"]{background:#E7F6F3;color:var(--wa-green)}
  #hx-menu-btn:focus-visible{outline:2px solid var(--wa-accent);outline-offset:1px}
  #hx-menu-btn:disabled{opacity:.45;cursor:default}
  #hx-menu-btn svg{width:22px;height:22px;fill:currentColor}
  /* Small dot until the visitor has opened the menu once, so it is not missed. */
  #hx-menu-btn .hx-dot{position:absolute;top:5px;right:5px;width:9px;height:9px;border-radius:50%;background:#25D366;box-shadow:0 0 0 2px #fff}
  #hx-menu-btn.seen .hx-dot{display:none}
  #hx-send{flex:none;width:42px;height:42px;padding:0;border:0;border-radius:50%;background:var(--wa-accent);color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s}
  #hx-send:hover{background:#029478}
  #hx-send:focus-visible{outline:2px solid var(--wa-green);outline-offset:2px}
  #hx-send:disabled{opacity:.55;cursor:default}
  #hx-send svg{width:21px;height:21px;margin-left:2px}

  /* The menu: a panel that slides up ABOVE the message field, between the conversation and the
     input. The input bar never moves; the conversation just gets a little shorter. */
  #hx-menu{flex:none;background:var(--wa-bar);border-top:1px solid transparent;max-height:0;overflow:hidden;visibility:hidden;transition:max-height .28s ease,visibility .28s,border-color .28s}
  #hx-menu.open{max-height:216px;visibility:visible;border-top-color:#E1E5E8}
  #hx-menu .hx-menu-inner{display:flex;flex-direction:column;gap:8px;padding:10px 10px 2px}
  #hx-menu .hx-menu-head{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:#54656F}
  #hx-menu .hx-menu-head[hidden]{display:none}
  #hx-menu .hx-menu-head button{flex:none;width:30px;height:30px;padding:0;border:0;border-radius:50%;background:#fff;color:#54656F;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:var(--wa-shadow)}
  #hx-menu .hx-menu-head button:hover{color:var(--wa-green)}
  #hx-menu-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px;max-height:160px;overflow-y:auto;scrollbar-width:thin}
  #hx-menu .hx-item{display:flex;align-items:center;justify-content:flex-start;gap:10px;min-height:44px;padding:6px 10px 6px 8px;border:0;border-radius:12px;background:#fff;color:var(--wa-text);font:inherit;font-size:13.5px;font-weight:600;line-height:1.2;text-align:left;cursor:pointer;box-shadow:var(--wa-shadow);transition:background .15s,transform .15s}
  #hx-menu .hx-item:hover,#hx-menu .hx-item:focus-visible{background:#F0FAF8;outline:none}
  #hx-menu .hx-item:active{transform:scale(.97)}
  #hx-menu .hx-item:disabled{opacity:.55;cursor:default;transform:none}
  #hx-menu .hx-item:last-child:nth-child(odd){grid-column:1 / -1}
  #hx-menu .hx-ico{flex:none;width:30px;height:30px;border-radius:50%;background:#E7F6F3;color:var(--wa-green);display:flex;align-items:center;justify-content:center}
  #hx-menu .hx-ico svg{width:17px;height:17px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
  #hx-menu .hx-rupee{font-size:16px;font-weight:800;line-height:1}
  #hx-menu .hx-lbl{flex:1;min-width:0}
  #hx-menu .hx-more{flex:none;width:14px;height:14px;fill:none;stroke:#8696A0;stroke-width:2.6;stroke-linecap:round;stroke-linejoin:round}
  /* While the panel is open the menu button turns into a "back to typing" keyboard icon, as in Telegram. */
  #hx-menu-btn .hx-ic-kb{display:none}
  #hx-menu-btn[aria-expanded="true"] .hx-ic-grid{display:none}
  #hx-menu-btn[aria-expanded="true"] .hx-ic-kb{display:block}

  /* Nudge: looks like an incoming message preview beside the launcher. Purely a
     come-on - it never opens the chat on its own and stops for good once the
     visitor opens the chat or dismisses it. */
  #hx-nudge{position:fixed;right:20px;bottom:90px;z-index:59;max-width:230px;background:#fff;border-radius:10px 10px 0 10px;box-shadow:0 2px 14px rgba(11,20,26,.28);padding:8px 30px 9px 12px;font-size:14.2px;line-height:1.35;color:var(--wa-text);cursor:pointer;opacity:0;visibility:hidden;transform:translateY(10px) scale(.6);transform-origin:bottom right;transition:opacity .4s cubic-bezier(.34,1.56,.64,1),transform .4s cubic-bezier(.34,1.56,.64,1),visibility .4s}
  #hx-nudge.on{opacity:1;visibility:visible;transform:translateY(0) scale(1)}
  #hx-nudge::after{content:"";position:absolute;right:-8px;bottom:0;width:8px;height:13px;background:#fff;clip-path:polygon(0 0,0 100%,100% 100%)}
  #hx-nudge .hx-nudge-from{display:block;font-size:12.5px;font-weight:600;color:var(--wa-green);margin-bottom:1px}
  #hx-nudge-close{position:absolute;top:4px;right:4px;width:22px;height:22px;border:0;background:none;color:#8696A0;cursor:pointer;border-radius:50%;font-size:16px;line-height:1;display:flex;align-items:center;justify-content:center}
  #hx-nudge-close:hover{background:#F0F2F5;color:#54656F}

  @media (prefers-reduced-motion:reduce){.hx-typing span,#hx-chat-btn.wobble{animation:none}#hx-chat-btn,#hx-nudge,#hx-menu-btn,#hx-send{transition:none}#hx-menu{transition:none}}

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

  </div>
  <div id="hx-menu" aria-hidden="true">
    <div class="hx-menu-inner">
      <div class="hx-menu-head" id="hx-menu-headrow" hidden>
        <button type="button" id="hx-menu-back" aria-label="Back">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
        </button>
        <span id="hx-menu-title"></span>
      </div>
      <div id="hx-menu-list"></div>
    </div>
  </div>
  <form id="hx-form" autocomplete="off">
    <div class="hx-field">
      <input id="hx-input" type="text" maxlength="1000" placeholder="Type a message" aria-label="Your message">
      <button type="button" id="hx-menu-btn" aria-haspopup="true" aria-expanded="false" aria-controls="hx-menu" aria-label="Open menu" title="Menu">
        <svg class="hx-ic-grid" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h7v7H4V4Zm9 0h7v7h-7V4ZM4 13h7v7H4v-7Zm9 0h7v7h-7v-7Z"/></svg>
        <svg class="hx-ic-kb" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm2 3v2h2V8H5Zm4 0v2h2V8H9Zm4 0v2h2V8h-2Zm4 0v2h2V8h-2ZM5 12v2h2v-2H5Zm4 0v2h2v-2H9Zm4 0v2h2v-2h-2Zm4 0v2h2v-2h-2ZM8 15.5v1.5h8v-1.5H8Z"/></svg>
        <span class="hx-dot"></span>
      </button>
    </div>
    <button type="submit" id="hx-send" aria-label="Send message">
      <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.4 20.4 20.85 12.9a1 1 0 0 0 0-1.84L3.4 3.6a1 1 0 0 0-1.39.91L2 9.12c0 .5.37.93.87.99L17 12 2.87 13.88c-.5.07-.87.5-.87 1l.01 4.61c0 .71.73 1.2 1.39.91Z"/></svg>
    </button>
  </form>
</div>

<script>
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var btn = $('hx-chat-btn'), box = $('hx-chat'), msgs = $('hx-msgs'),
      form = $('hx-form'), input = $('hx-input'), sendBtn = $('hx-send'),
      status = $('hx-status'), menu = $('hx-menu'), menuBtn = $('hx-menu-btn');
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
  var path = [], menuOpen = false;
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
    busy = true; sendBtn.disabled = true; setStatus(); renderChips();
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
    menuOpen = true;
    renderChips();
    // Came from another page with a conversation going: add the new project's welcome at the end.
    welcomeProject();
  }
  // Menu: the visitor's drill-down list, shown only while open. Leaves post their message,
  // branches open the next level inside the same popup.
  var MENU_SEEN = 'hx-chat-menu-seen';
  // One consistent outline set: 24px grid, one stroke weight, round caps and joins, drawn in the
  // text colour. Emoji were dropped because they look different on every device. The budget
  // uses a real rupee sign, which Indian visitors read faster than a drawn coin.
  var SVG = {
    home: '<path d="M3 11.5 12 4l9 7.5M5.5 10v9.5h13V10M10 19.5v-5h4v5"/>',
    building: '<path d="M5 20V6.5l7-3 7 3V20M3 20h18M9 9.5h2M13 9.5h2M9 13h2M13 13h2M10.5 20v-3.5h3V20"/>',
    flag: '<path d="M6 21V4M6 4.5c3-1.5 5 1.5 8 0s3-1 4-1V13c-1 0-1 .5-4 1.5S9 12 6 13.5"/>',
    book: '<path d="M12 6.5C10.5 5 8 4.5 4.5 4.5V18c3.5 0 6 .5 7.5 2 1.5-1.5 4-2 7.5-2V4.5C16 4.5 13.5 5 12 6.5ZM12 6.5V20"/>',
    phone: '<path d="M5 4h3.5l1.7 4.3-2.2 1.4a11 11 0 0 0 5.3 5.3l1.4-2.2 4.3 1.7V18a2 2 0 0 1-2 2A13 13 0 0 1 3 6a2 2 0 0 1 2-2Z"/>',
    info: '<circle cx="12" cy="12" r="9"/><path d="M12 11v5.2M12 7.8v.4"/>',
    tag: '<path d="M3.5 12.2V4.5h7.7l9 9a1.5 1.5 0 0 1 0 2.1l-5.6 5.6a1.5 1.5 0 0 1-2.1 0l-9-9ZM8 8.5h.01"/>',
    pin: '<path d="M12 21s6.5-5.6 6.5-11a6.5 6.5 0 1 0-13 0C5.5 15.4 12 21 12 21ZM12 12.2a2.2 2.2 0 1 0 0-4.4 2.2 2.2 0 0 0 0 4.4Z"/>',
    compass: '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5 5-2Z"/>',
    bed: '<path d="M3 18V7M3 14h18v4M21 14v-2.5A2.5 2.5 0 0 0 18.5 9H11v5M7 11.5h.01"/>',
    grid: '<path d="M4 4h6.5v6.5H4V4Zm9.5 0H20v6.5h-6.5V4ZM4 13.5h6.5V20H4v-6.5Zm9.5 0H20V20h-6.5v-6.5Z"/>'
  };
  // First screen: by what the entry says. Deeper screens: by the question being asked.
  var BY_LABEL = {
    'find a home': 'home', 'commercial spaces': 'building', 'by project stage': 'flag', 'by budget': 'rupee',
    'guides & articles': 'book', 'talk to our team': 'phone', 'about this project': 'info',
    'prices & unit sizes': 'tag', "what's nearby": 'pin', 'explore other projects': 'compass'
  };
  var BY_PROMPT = [[/city/i, 'pin'], [/size|bhk/i, 'bed'], [/budget|price/i, 'rupee'], [/stage/i, 'flag']];
  function iconFor(label, parent) {
    if (/^any\b/i.test(label)) return 'grid';
    if (!parent) return BY_LABEL[label.toLowerCase()] || null;
    var q = parent.prompt || '';
    for (var i = 0; i < BY_PROMPT.length; i++) { if (BY_PROMPT[i][0].test(q)) return BY_PROMPT[i][1]; }
    return null;
  }
  function iconSvg(name) {
    if (name === 'rupee') return '<span class="hx-ico hx-rupee" aria-hidden="true">₹</span>';
    return name && SVG[name]
      ? '<span class="hx-ico" aria-hidden="true"><svg viewBox="0 0 24 24">' + SVG[name] + '</svg></span>' : '';
  }
  var MORE = '<svg class="hx-more" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>';
  var list = $('hx-menu-list'), headRow = $('hx-menu-headrow'), mTitle = $('hx-menu-title'), mBack = $('hx-menu-back');
  var touchUI = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;
  try { if (sessionStorage.getItem(MENU_SEEN) === '1') menuBtn.classList.add('seen'); } catch (e) {}

  function closeMenu() { if (menuOpen) { menuOpen = false; path = []; renderChips(); } }
  // The panel grows under the field while `menuOpen` and shrinks away again; the chat above
  // simply gets a little shorter, so keep the latest message in view while that happens.
  function stick() { msgs.scrollTop = msgs.scrollHeight; }
  menu.addEventListener('transitionend', function (e) { if (e.propertyName === 'max-height') stick(); });
  function renderChips() {
    var root = MENU.children || [];
    // Stays open while the assistant is "typing" (its welcome, a reply): the buttons just wait their turn.
    menuBtn.disabled = !root.length;
    var open = menuOpen && root.length > 0;
    menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    menuBtn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    menu.classList.toggle('open', open);
    menu.setAttribute('aria-hidden', open ? 'false' : 'true');
    stick();
    if (!open) return;

    list.innerHTML = '';
    var node = path[path.length - 1];
    headRow.hidden = !node;
    if (node) mTitle.textContent = node.prompt || node.label;
    (node ? node.children : root).forEach(function (n) {
      var branch = !!(n.children && n.children.length);
      var b = document.createElement('button'); b.type = 'button'; b.className = 'hx-item';
      b.disabled = busy;
      b.innerHTML = iconSvg(iconFor(n.label, node)) + '<span class="hx-lbl">' + esc(n.label) + '</span>' + (branch ? MORE : '');
      b.onclick = function () {
        if (branch) { path.push(n); renderChips(); list.scrollTop = 0; }
        else { menuOpen = false; path = []; renderChips(); send(n.send); }
      };
      list.appendChild(b);
    });
  }
  menuBtn.onclick = function () {
    menuOpen = !menuOpen; path = [];
    if (menuOpen) {
      menuBtn.classList.add('seen');
      try { sessionStorage.setItem(MENU_SEEN, '1'); } catch (err) {}
    }
    renderChips();
    if (!menuOpen) input.focus();
  };
  mBack.onclick = function () { path.pop(); renderChips(); };
  // Starting to type means they know what they want: the panel slides away. It stays open until then.
  input.addEventListener('input', function () { if (menuOpen && input.value.trim() !== '') closeMenu(); });
  // On a phone the on-screen keyboard and this panel would fight for the same space.
  input.addEventListener('focus', function () { if (touchUI && menuOpen) closeMenu(); });

  function toggle(open) {
    box.classList.toggle('open', open); btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) {
      if (!msgs.children.length) render();
      else if (!busy) { menuOpen = true; path = []; renderChips(); welcomeProject(); }
      if (!touchUI) input.focus();
      msgs.scrollTop = msgs.scrollHeight;
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
    busy = true; sendBtn.disabled = true; menuOpen = false; path = []; setStatus(); renderChips();
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
  box.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    if (menuOpen) { closeMenu(); menuBtn.focus(); return; }
    toggle(false); btn.focus();
  });
  // While the assistant is still "typing", keep what the visitor wrote instead of dropping it.
  form.onsubmit = function (e) {
    e.preventDefault();
    if (busy) return;
    var t = input.value; input.value = ''; send(t);
  };
})();
</script>
@endif
