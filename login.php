<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Profil Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen flex items-center justify-center p-4 bg-white border-[24px]" style="border-color: #D4C4A8;">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100 relative z-50">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-slate-900 rounded-2xl mx-auto flex items-center justify-center text-white text-2xl shadow-md mb-3">
                🔐
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Login Administrator</h2>
            <p class="text-sm text-gray-500 mt-1">Website Profil Sekolah</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-4 text-center">
                Username atau password salah!
            </div>
        <?php endif; ?>

        <form id="loginForm" action="proses-login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" id="username" placeholder="Masukkan username Anda" class="w-full text-sm border border-gray-200 bg-gray-50 px-4 py-3 rounded-xl focus:outline-none focus:border-slate-800 focus:bg-white transition duration-200">
                <p id="usernameError" class="text-xs text-red-500 mt-1 hidden">Username minimal harus 4 karakter!</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" class="w-full text-sm border border-gray-200 bg-gray-50 px-4 py-3 rounded-xl focus:outline-none focus:border-slate-800 focus:bg-white transition duration-200">
                <p id="passwordError" class="text-xs text-red-500 mt-1 hidden">Password tidak boleh kosong!</p>
            </div>

            <button type="submit" name="login" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm py-3.5 rounded-xl shadow-md transition duration-200 mt-2">
                Masuk ke Dashboard
            </button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            let usernameInput = document.getElementById('username');
            let passwordInput = document.getElementById('password');
            
            let usernameError = document.getElementById('usernameError');
            let passwordError = document.getElementById('passwordError');
            
            let isValid = true;

            // Validasi Username
            if (usernameInput.value.trim().length < 4) {
                usernameInput.classList.add('border-red-500');
                usernameError.classList.remove('hidden');
                isValid = false;
            } else {
                usernameInput.classList.remove('border-red-500');
                usernameError.classList.add('hidden');
            }

            // Validasi Password
            if (passwordInput.value.trim() === "") {
                passwordInput.classList.add('border-red-500');
                passwordError.classList.remove('hidden');
                isValid = false;
            } else {
                passwordInput.classList.remove('border-red-500');
                passwordError.classList.add('hidden');
            }

            // Jika form tidak lolos validasi, batalkan pengiriman data
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>