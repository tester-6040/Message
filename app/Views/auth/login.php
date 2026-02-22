<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-5xl grid md:grid-cols-2 rounded-3xl shadow-2xl overflow-hidden bg-white">
        <div class="bg-gradient-to-br from-pink-500 to-rose-500 p-10 text-white">
            <h1 class="text-4xl font-bold mb-4">PinkSecret Messenger</h1>
            <p class="opacity-95">Private chatting with end-to-end style encrypted text at rest, media sharing, typing status, and notifications — all with a romantic pink theme.</p>
        </div>
        <div class="p-8">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold">Login</h2>
                <form id="loginForm" class="space-y-3 mt-4">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="login">
                    <input class="w-full border rounded-xl px-4 py-3" name="email" type="email" placeholder="Email" required>
                    <input class="w-full border rounded-xl px-4 py-3" name="password" type="password" placeholder="Password" required>
                    <button class="w-full bg-blush text-white rounded-xl py-3 font-semibold">Sign In</button>
                </form>
            </div>
            <div>
                <h2 class="text-xl font-semibold">Create account</h2>
                <form id="registerForm" class="space-y-3 mt-4">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="register">
                    <input class="w-full border rounded-xl px-4 py-3" name="name" placeholder="Name" required>
                    <input class="w-full border rounded-xl px-4 py-3" name="email" type="email" placeholder="Email" required>
                    <input class="w-full border rounded-xl px-4 py-3" name="password" type="password" placeholder="Password (6+)" required>
                    <button class="w-full bg-pink-500 text-white rounded-xl py-3 font-semibold">Register</button>
                </form>
            </div>
            <p id="authMsg" class="mt-4 text-sm"></p>
        </div>
    </div>
</div>
<script>
async function submitForm(form) {
    const res = await fetch('index.php', { method: 'POST', body: new FormData(form) });
    const data = await res.json();
    document.getElementById('authMsg').textContent = data.message || 'Done';
    if (res.ok) window.location.href = 'chat.php';
}
document.getElementById('loginForm').addEventListener('submit', e => { e.preventDefault(); submitForm(e.target); });
document.getElementById('registerForm').addEventListener('submit', e => { e.preventDefault(); submitForm(e.target); });
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
