<?php require __DIR__ . '/../layouts/header.php'; ?>
<div class="min-h-screen bg-gradient-to-br from-rose-100 via-pink-50 to-fuchsia-100">
    <div class="max-w-6xl mx-auto px-6 py-10">
        <div class="rounded-3xl bg-white/80 backdrop-blur-md shadow-xl p-8 md:p-14 grid md:grid-cols-2 gap-10 items-center border border-pink-100">
            <div>
                <p class="inline-flex bg-pink-100 text-pink-700 px-3 py-1 rounded-full text-xs font-semibold">Private • Elegant • Secure</p>
                <h1 class="text-5xl font-bold mt-4 leading-tight text-pink-700">For You, My Heart 💗</h1>
                <p class="mt-5 text-gray-700">A romantic, professional private messenger to share messages, photos, and videos with typing indicator, online status, and confidentiality-focused architecture.</p>
                <div class="mt-8 flex gap-3">
                    <a href="login.php" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 rounded-xl font-semibold">Start Secure Chat</a>
                    <a href="login.php" class="bg-white border border-pink-200 text-pink-700 px-6 py-3 rounded-xl font-semibold">Login / Register</a>
                </div>
            </div>
            <div class="rounded-3xl bg-gradient-to-br from-pink-500 to-rose-500 p-8 text-white shadow-xl">
                <h2 class="text-2xl font-bold">Features you asked</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    <li>✅ Private 1:1 chat in MVC + API architecture</li>
                    <li>✅ Image/video sharing with safe mime checks</li>
                    <li>✅ Typing indicator + online/offline status</li>
                    <li>✅ Session auth, CSRF, prepared statements, password hashing</li>
                    <li>✅ Pink professional UI experience</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
