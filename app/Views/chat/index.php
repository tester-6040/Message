<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="h-screen p-4 md:p-6">
    <div class="h-full bg-white shadow-2xl rounded-3xl overflow-hidden grid grid-cols-1 md:grid-cols-4">
        <aside class="border-r p-4 bg-pink-50">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-lg text-pink-700">Chats</h2>
                <form method="post" action="login.php">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="logout">
                    <button class="text-xs bg-white border rounded-lg px-2 py-1">Logout</button>
                </form>
            </div>
            <p class="text-xs mt-1 text-gray-500">Hi <?= e($me['name']) ?> 👋</p>

            <div class="mt-4">
                <select id="partnerSelect" class="w-full border rounded-xl p-2">
                    <option value="">Start a new private chat</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= (int)$user['id'] ?>"><?= e($user['name']) ?> • <?= ((int)$user['is_online'] === 1 ? 'Online' : 'Offline') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <ul id="conversationList" class="mt-4 space-y-2 overflow-auto max-h-[70vh]">
                <?php foreach ($conversations as $c): ?>
                    <li>
                        <button class="w-full text-left bg-white p-3 rounded-xl border conversation-item" data-id="<?= (int)$c['id'] ?>" data-partner="<?= e($c['partner_name']) ?>">
                            <div class="flex justify-between items-center gap-2">
                                <p class="font-semibold text-sm"><?= e($c['partner_name']) ?></p>
                                <span class="text-[10px] px-2 py-0.5 rounded-full <?= ((int)$c['partner_online'] === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500') ?>"><?= ((int)$c['partner_online'] === 1 ? 'Online' : 'Offline') ?></span>
                            </div>
                            <p class="text-xs text-gray-500 truncate"><?= e($c['preview']) ?></p>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <main class="md:col-span-3 flex flex-col">
            <header class="border-b p-4">
                <div class="flex items-center gap-3">
                    <h3 id="chatTitle" class="font-semibold text-pink-700">Select or create a chat</h3>
                    <span id="onlineDot" class="hidden w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                    <span id="onlineText" class="text-xs text-gray-500"></span>
                </div>
                <p id="typingStatus" class="text-xs text-gray-500 h-4"></p>
            </header>
            <section id="messages" class="flex-1 p-4 overflow-auto bg-gradient-to-b from-white to-pink-50"></section>
            <form id="sendForm" class="p-4 border-t space-y-2" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" id="conversationId" name="conversation_id">
                <textarea name="text" id="messageText" class="w-full border rounded-xl p-3" rows="2" placeholder="Type your message..."></textarea>
                <div class="flex gap-2 items-center">
                    <input type="file" name="media" accept="image/jpeg,image/png,image/webp,video/mp4" class="text-sm">
                    <button class="bg-blush text-white px-5 py-2 rounded-xl font-semibold">Send</button>
                </div>
            </form>
        </main>
    </div>
</div>
<script>
const API = 'api.php';
let activeConversation = null;

function esc(s){return (s||'').replace(/[&<>"']/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;"}[m]));}

async function api(action, method='GET', formData=null){
    const url = `${API}?action=${action}` + (method==='GET' && formData?.get('conversation_id') ? `&conversation_id=${formData.get('conversation_id')}` : '');
    const res = await fetch(url,{method,body:method==='POST'?formData:null});
    return {ok:res.ok, data: await res.json()};
}

function renderMessages(list){
    const box = document.getElementById('messages');
    box.innerHTML = list.map(m => {
        const align = m.is_me ? 'justify-end' : 'justify-start';
        const bubble = m.is_me ? 'bg-pink-500 text-white' : 'bg-white border';
        let media = '';
        if (m.media_path) {
            media = m.media_type === 'image'
                ? `<img src="${m.media_path}" class="max-w-xs rounded-lg mt-2"/>`
                : `<video src="${m.media_path}" controls class="max-w-xs rounded-lg mt-2"></video>`;
        }
        return `<div class="flex ${align} mb-2"><div class="${bubble} rounded-2xl px-4 py-2 max-w-md"><p>${esc(m.text||'')}</p>${media}<span class="text-[10px] opacity-70">${m.created_at}</span></div></div>`;
    }).join('');
    box.scrollTop = box.scrollHeight;
}

function paintOnline(isOnline){
    const dot = document.getElementById('onlineDot');
    const text = document.getElementById('onlineText');
    dot.classList.remove('hidden');
    dot.className = `w-2.5 h-2.5 rounded-full ${isOnline ? 'bg-emerald-500' : 'bg-gray-300'}`;
    text.textContent = isOnline ? 'Online' : 'Offline';
}

async function loadMessages(){
    if (!activeConversation) return;
    const fd = new FormData(); fd.set('conversation_id', activeConversation);
    const {data} = await api('messages','GET',fd);
    renderMessages(data.messages || []);
    const typing = await api('typing-status','GET',fd);
    document.getElementById('typingStatus').textContent = typing.data.typing ? 'Typing...' : '';
    const online = await api('online-status', 'GET', fd);
    paintOnline(Boolean(online.data.online));
}

document.querySelectorAll('.conversation-item').forEach(btn => {
    btn.addEventListener('click', async () => {
        activeConversation = btn.dataset.id;
        document.getElementById('conversationId').value = activeConversation;
        document.getElementById('chatTitle').textContent = btn.dataset.partner;
        await loadMessages();
    });
});

document.getElementById('partnerSelect').addEventListener('change', async (e) => {
    if (!e.target.value) return;
    const fd = new FormData();
    fd.set('_csrf', '<?= e(csrf_token()) ?>');
    fd.set('partner_id', e.target.value);
    const {data, ok} = await api('start-conversation', 'POST', fd);
    if (ok) { activeConversation = data.conversation_id; document.getElementById('conversationId').value = activeConversation; await loadMessages(); window.location.href = 'chat.php'; }
});

document.getElementById('sendForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!activeConversation) return alert('Select a conversation first.');
    const fd = new FormData(e.target);
    fd.set('_csrf', '<?= e(csrf_token()) ?>');
    const {ok} = await api('send-message', 'POST', fd);
    if (ok) { e.target.reset(); document.getElementById('conversationId').value = activeConversation; await loadMessages(); notify('New message sent'); }
});

let typingTimer;
document.getElementById('messageText').addEventListener('input', async () => {
    if (!activeConversation) return;
    const fd = new FormData();
    fd.set('_csrf', '<?= e(csrf_token()) ?>');
    fd.set('conversation_id', activeConversation);
    fd.set('is_typing', '1');
    await api('typing', 'POST', fd);
    clearTimeout(typingTimer);
    typingTimer = setTimeout(async () => {
        fd.set('is_typing', '0');
        await api('typing', 'POST', fd);
    }, 1200);
});

function notify(text){
    if (!('Notification' in window)) return;
    if (Notification.permission === 'granted') new Notification(text);
    else if (Notification.permission !== 'denied') Notification.requestPermission();
}

const firstConversation = document.querySelector('.conversation-item');
if (firstConversation) firstConversation.click();
setInterval(loadMessages, 2500);
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
