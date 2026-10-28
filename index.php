<?php
// Panggil koneksi SQLite dari database.php
$pdo = require_once __DIR__ . '/database.php';

// Mendapatkan method HTTP dan Endpoint
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$requestUri = str_replace($scriptName, '', $path);
$requestUri = trim($requestUri, '/');

// Fungsi helper JSON response
function sendResponse($status, $message, $data = null) {
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code($status);
    $response = ["status" => $status, "message" => $message];
    if ($data !== null) {
        $response["data"] = $data;
    }
    echo json_encode($response);
    exit;
}

// Middleware Autentikasi API
function authenticate() {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if ($authHeader !== 'Bearer secret-token-123') {
        sendResponse(401, 'Unauthorized: Token tidak valid atau tidak disertakan.');
    }
}

// --- ROUTING API & WEB VIEW ---

// 1. WEB UI (Professional SaaS Dashboard & Login Page)
if ($requestUri === '' || $requestUri === 'index.php') {
    ?>
    <!DOCTYPE html>
    <html lang="id" class="h-full bg-slate-900">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Product Management System | Login & Dashboard</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="h-full text-slate-100 bg-slate-950 flex flex-col selection:bg-indigo-500 selection:text-white">

        <!-- LOGIN VIEW CONTAINER -->
        <div id="loginView" class="fixed inset-0 bg-slate-950 z-50 flex items-center justify-center p-4 hidden">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-8 shadow-2xl relative">
                <div class="text-center mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/25 mx-auto mb-4">
                        <i class="fa-solid fa-lock text-white text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-white tracking-tight">Selamat Datang Kembali</h2>
                    <p class="text-xs text-slate-400 mt-1">Data akun diverifikasi langsung dari database SQLite</p>
                </div>

                <form onsubmit="handleLogin(event)" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1.5">Email Admin</label>
                        <input type="email" id="loginEmail" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition" value="admin@test.com">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1.5">Password</label>
                        <input type="password" id="loginPassword" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition" value="password123">
                    </div>
                    <button type="submit" class="w-full mt-2 bg-indigo-600 hover:bg-indigo-500 text-white py-3 rounded-xl font-medium text-sm transition shadow-lg shadow-indigo-600/25">
                        Masuk ke Dashboard
                    </button>
                </form>
                <div class="mt-6 p-3 bg-slate-950/60 border border-slate-800 rounded-xl text-center text-xs text-slate-400">
                    💡 Info Akun DB: <code class="text-indigo-400">admin@test.com</code> | Pass: <code class="text-indigo-400">password123</code>
                </div>
            </div>
        </div>

        <!-- DASHBOARD VIEW CONTAINER -->
        <div id="dashboardView" class="flex-1 flex flex-col hidden">
            <!-- Top Navigation Bar -->
            <header class="border-b border-slate-800 bg-slate-950/80 backdrop-blur-md sticky top-0 z-30">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                            <i class="fa-solid fa-cube text-white text-lg"></i>
                        </div>
                        <div>
                            <span class="font-bold text-slate-100 tracking-tight text-base block">Inventaris</span>
                            <span class="text-xs text-slate-400 font-medium">Database-Backed Dashboard</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span id="userSessionInfo" class="text-xs text-slate-300 font-medium hidden sm:inline-block"></span>
                        <button onclick="handleLogout()" class="px-3.5 py-1.5 bg-slate-900 hover:bg-rose-500/10 hover:text-rose-400 hover:border-rose-500/20 border border-slate-800 rounded-xl text-xs font-medium text-slate-300 transition flex items-center gap-2">
                            <i class="fa-solid fa-right-from-bracket"></i> Keluar
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-900/60 p-6 rounded-2xl border border-slate-800/80 shadow-xl shadow-black/20">
                    <div>
                        <h1 class="text-xl font-bold text-white tracking-tight">Manajemen Katalog Produk</h1>
                        <p class="text-sm text-slate-400 mt-1">Kelola data inventaris produk, harga, dan status ketersediaan secara real-time.</p>
                    </div>
                    <button onclick="openModal()" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2.5 rounded-xl font-medium text-sm transition shadow-lg shadow-indigo-600/25 active:scale-95">
                        <i class="fa-solid fa-plus"></i> Tambah Produk Baru
                    </button>
                </div>

                <!-- Stats Overview Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="bg-slate-900/80 border border-slate-800 p-5 rounded-2xl flex items-center justify-between relative overflow-hidden group">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Produk</p>
                            <h3 id="statTotal" class="text-3xl font-extrabold text-white mt-1.5">0</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xl">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                    </div>
                    <div class="bg-slate-900/80 border border-slate-800 p-5 rounded-2xl flex items-center justify-between relative overflow-hidden group">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Stok Tersedia</p>
                            <h3 id="statAvailable" class="text-3xl font-extrabold text-emerald-400 mt-1.5">0</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <div class="bg-slate-900/80 border border-slate-800 p-5 rounded-2xl flex items-center justify-between relative overflow-hidden group">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Stok Habis</p>
                            <h3 id="statOut" class="text-3xl font-extrabold text-rose-400 mt-1.5">0</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 text-xl">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                    </div>
                </div>

                <!-- Controls: Search & Filters -->
                <div class="bg-slate-900/80 border border-slate-800 p-4 rounded-2xl flex flex-col md:flex-row gap-4 justify-between items-center">
                    <div class="w-full md:w-96 relative">
                        <i class="fa-solid fa-search absolute left-4 top-3.5 text-slate-400 text-sm"></i>
                        <input type="text" id="searchBox" placeholder="Cari nama produk atau kategori..." oninput="loadProducts()" class="w-full pl-11 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    <div class="w-full md:w-auto flex items-center gap-3">
                        <select id="filterStatus" onchange="loadProducts()" class="w-full md:w-44 px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                            <option value="">Semua Status</option>
                            <option value="tersedia">Tersedia</option>
                            <option value="habis">Habis</option>
                        </select>
                    </div>
                </div>

                <!-- Data Table Container -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-800 bg-slate-950/50 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Informasi Produk</th>
                                    <th class="px-6 py-4">Kategori</th>
                                    <th class="px-6 py-4">Harga</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody" class="divide-y divide-slate-800/60 text-sm">
                                <!-- Injected by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <!-- Modal Form (Tambah / Edit) -->
        <div id="productModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center px-4 z-50">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative">
                <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-800">
                    <h3 id="modalTitle" class="text-base font-bold text-white">Tambah Produk Baru</h3>
                    <button onclick="closeModal()" class="text-slate-400 hover:text-white transition"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>
                <form id="productForm" onsubmit="saveProduct(event)" class="space-y-4">
                    <input type="hidden" id="productId">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1.5">Nama Produk</label>
                        <input type="text" id="name" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1.5">Deskripsi</label>
                        <textarea id="description" rows="2" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition resize-none"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase mb-1.5">Harga (IDR)</label>
                            <input type="number" id="price" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase mb-1.5">Kategori</label>
                            <input type="text" id="category" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1.5">Status Ketersediaan</label>
                        <select id="status" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                            <option value="tersedia">Tersedia</option>
                            <option value="habis">Habis</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" onclick="closeModal()" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-medium text-sm transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-medium text-sm transition shadow-lg shadow-indigo-600/25">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Custom Toast Notification Container -->
        <div id="toast" class="fixed bottom-5 right-5 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
            <div id="toastContent" class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-2xl border text-sm font-medium">
                <i id="toastIcon" class="text-base"></i>
                <span id="toastMessage">Pesan notifikasi</span>
            </div>
        </div>

        <script>
            function checkAuth() {
                const token = localStorage.getItem('auth_token');
                const userEmail = localStorage.getItem('user_email');
                if (!token) {
                    document.getElementById('loginView').classList.remove('hidden');
                    document.getElementById('dashboardView').classList.add('hidden');
                } else {
                    document.getElementById('loginView').classList.add('hidden');
                    document.getElementById('dashboardView').classList.remove('hidden');
                    if(userEmail) {
                        document.getElementById('userSessionInfo').innerHTML = `<i class="fa-regular fa-user text-indigo-400 mr-1.5"></i> ${userEmail}`;
                    }
                    loadProducts();
                }
            }

            async function handleLogin(e) {
                e.preventDefault();
                const email = document.getElementById('loginEmail').value;
                const password = document.getElementById('loginPassword').value;

                try {
                    const res = await fetch('api/login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email, password })
                    });
                    const json = await res.json();

                    if (res.ok) {
                        localStorage.setItem('auth_token', json.data.token);
                        localStorage.setItem('user_email', json.data.email);
                        showToast('Login berhasil!', 'success');
                        checkAuth();
                    } else {
                        showToast(json.message || 'Login gagal.', 'error');
                    }
                } catch (err) {
                    showToast('Terjadi kesalahan koneksi.', 'error');
                }
            }

            function handleLogout() {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user_email');
                showToast('Berhasil keluar.', 'success');
                checkAuth();
            }

            function showToast(message, type = 'success') {
                const toast = document.getElementById('toast');
                const content = document.getElementById('toastContent');
                const icon = document.getElementById('toastIcon');
                const msg = document.getElementById('toastMessage');

                msg.innerText = message;
                if (type === 'success') {
                    content.className = "flex items-center gap-3 px-4 py-3 rounded-xl shadow-2xl border bg-slate-900 border-emerald-500/30 text-emerald-400";
                    icon.className = "fa-solid fa-circle-check text-emerald-400";
                } else {
                    content.className = "flex items-center gap-3 px-4 py-3 rounded-xl shadow-2xl border bg-slate-900 border-rose-500/30 text-rose-400";
                    icon.className = "fa-solid fa-circle-exclamation text-rose-400";
                }

                toast.classList.remove('translate-y-20', 'opacity-0');
                setTimeout(() => {
                    toast.classList.add('translate-y-20', 'opacity-0');
                }, 3000);
            }

            async function loadStats() {
                try {
                    const res = await fetch('api/products/stats');
                    const json = await res.json();
                    if(json.status === 200) {
                        document.getElementById('statTotal').innerText = json.data.total_products;
                        document.getElementById('statAvailable').innerText = json.data.available_products;
                        document.getElementById('statOut').innerText = json.data.out_of_stock_products;
                    }
                } catch(e) { console.error(e); }
            }

            async function loadProducts() {
                const search = document.getElementById('searchBox').value;
                const status = document.getElementById('filterStatus').value;
                let url = `api/products?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
                
                try {
                    const res = await fetch(url);
                    const json = await res.json();
                    
                    const tbody = document.getElementById('productTableBody');
                    tbody.innerHTML = '';

                    if (json.status === 200 && json.data.products.length > 0) {
                        json.data.products.forEach(p => {
                            const isAvailable = p.status.toLowerCase() === 'tersedia';
                            const badgeClass = isAvailable 
                                ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' 
                                : 'bg-rose-500/10 text-rose-400 border-rose-500/20';
                            
                            tbody.innerHTML += `
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="px-6 py-4 font-mono text-slate-400">#${p.id}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-100">${escapeHtml(p.name)}</div>
                                        <div class="text-xs text-slate-400 truncate max-w-xs mt-0.5">${escapeHtml(p.description)}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-300 font-medium">${escapeHtml(p.category)}</td>
                                    <td class="px-6 py-4 font-mono font-semibold text-slate-200">Rp ${Number(p.price).toLocaleString('id-ID')}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold capitalize border ${badgeClass}">
                                            <span class="w-1.5 h-1.5 rounded-full ${isAvailable ? 'bg-emerald-400' : 'bg-rose-400'}"></span>
                                            ${p.status}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center space-x-1">
                                        <button onclick='editProduct(${JSON.stringify(p)})' class="p-2 text-slate-400 hover:text-indigo-400 hover:bg-slate-800 rounded-xl transition" title="Edit Produk">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button onclick="deleteProduct(${p.id})" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-xl transition" title="Hapus Produk">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada data produk yang ditemukan.</td></tr>`;
                    }
                    loadStats();
                } catch(e) { console.error(e); }
            }

            function escapeHtml(str) {
                return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
            }

            function openModal(product = null) {
                document.getElementById('productModal').classList.remove('hidden');
                if (product) {
                    document.getElementById('modalTitle').innerText = 'Edit Data Produk';
                    document.getElementById('productId').value = product.id;
                    document.getElementById('name').value = product.name;
                    document.getElementById('description').value = product.description;
                    document.getElementById('price').value = product.price;
                    document.getElementById('category').value = product.category;
                    document.getElementById('status').value = product.status;
                } else {
                    document.getElementById('modalTitle').innerText = 'Tambah Produk Baru';
                    document.getElementById('productForm').reset();
                    document.getElementById('productId').value = '';
                }
            }

            function closeModal() {
                document.getElementById('productModal').classList.add('hidden');
            }

            function editProduct(p) {
                openModal(p);
            }

            async function saveProduct(e) {
                e.preventDefault();
                const id = document.getElementById('productId').value;
                const token = localStorage.getItem('auth_token');
                const data = {
                    name: document.getElementById('name').value,
                    description: document.getElementById('description').value,
                    price: parseFloat(document.getElementById('price').value),
                    category: document.getElementById('category').value,
                    status: document.getElementById('status').value
                };

                const url = id ? `api/products/${id}` : 'api/products';
                const method = id ? 'PUT' : 'POST';

                try {
                    const res = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`
                        },
                        body: JSON.stringify(data)
                    });

                    const json = await res.json();
                    if (res.ok) {
                        closeModal();
                        loadProducts();
                        showToast(json.message, 'success');
                    } else if (res.status === 401) {
                        showToast('Sesi habis, silakan login ulang.', 'error');
                        handleLogout();
                    } else {
                        showToast(json.message || 'Gagal menyimpan data.', 'error');
                    }
                } catch(err) {
                    showToast('Terjadi kesalahan koneksi.', 'error');
                }
            }

            async function deleteProduct(id) {
                if (confirm('Apakah Anda yakin ingin menghapus produk ini dari database?')) {
                    const token = localStorage.getItem('auth_token');
                    try {
                        const res = await fetch(`api/products/${id}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${token}` }
                        });
                        const json = await res.json();
                        if (res.ok) {
                            loadProducts();
                            showToast('Produk berhasil dihapus.', 'success');
                        } else if (res.status === 401) {
                            showToast('Sesi habis, silakan login ulang.', 'error');
                            handleLogout();
                        } else {
                            showToast(json.message || 'Gagal menghapus produk.', 'error');
                        }
                    } catch(e) {
                        showToast('Terjadi kesalahan koneksi.', 'error');
                    }
                }
            }

            checkAuth();
        </script>
    </body>
    </html>
    <?php
    exit;
}

// 2. REST API ENDPOINTS (JSON Backend - Verifikasi Akun dari Database SQLite)
elseif ($requestUri === 'api/login' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';

    // Query untuk mencari user berdasarkan email di database SQLite
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek apakah user ditemukan dan password-nya cocok
    if ($user && password_verify($password, $user['password'])) {
        sendResponse(200, 'Login berhasil', [
            'token' => 'secret-token-123',
            'email' => $user['email'],
            'role' => $user['role']
        ]);
    } else {
        sendResponse(400, 'Email atau password salah.');
    }
}

elseif ($requestUri === 'api/products') {
    if ($method === 'GET') {
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $whereClauses = [];
        $params = [];

        if ($search !== '') {
            $whereClauses[] = "(name LIKE ? OR category LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status !== '') {
            $whereClauses[] = "status = ?";
            $params[] = $status;
        }

        $whereSql = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM products $whereSql");
        $countStmt->execute($params);
        $totalData = $countStmt->fetchColumn();
        $totalPages = ceil($totalData / $limit);

        $sql = "SELECT * FROM products $whereSql LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        
        $paramIndex = 1;
        foreach ($params as $val) {
            $stmt->bindValue($paramIndex++, $val, PDO::PARAM_STR);
        }
        $stmt->bindValue($paramIndex++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendResponse(200, 'Berhasil mengambil daftar produk', [
            'total_data' => (int)$totalData,
            'total_pages' => (int)$totalPages,
            'current_page' => $page,
            'limit' => $limit,
            'products' => $products
        ]);
    } 
    elseif ($method === 'POST') {
        authenticate();
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['name']) || empty($input['description']) || !isset($input['price']) || empty($input['category']) || empty($input['status'])) {
            sendResponse(422, 'Validasi Gagal: Semua field wajib diisi.');
        }

        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            trim($input['name']),
            trim($input['description']),
            (float)$input['price'],
            trim($input['category']),
            strtolower(trim($input['status']))
        ]);

        $newId = $pdo->lastInsertId();
        $fetchStmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $fetchStmt->execute([$newId]);
        
        sendResponse(201, 'Produk berhasil ditambahkan', $fetchStmt->fetch(PDO::FETCH_ASSOC));
    }
}

elseif (preg_match('/^api\/products\/(\d+)$/', $requestUri, $matches)) {
    $id = (int)$matches[1];
    $checkStmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $checkStmt->execute([$id]);
    $product = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        sendResponse(404, 'Produk tidak ditemukan.');
    }

    if ($method === 'PUT') {
        authenticate();
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['name']) || empty($input['description']) || !isset($input['price']) || empty($input['category']) || empty($input['status'])) {
            sendResponse(422, 'Validasi Gagal: Semua field wajib diisi.');
        }

        $updateStmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ?, status = ? WHERE id = ?");
        $updateStmt->execute([
            trim($input['name']),
            trim($input['description']),
            (float)$input['price'],
            trim($input['category']),
            strtolower(trim($input['status'])),
            $id
        ]);

        $fetchStmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $fetchStmt->execute([$id]);
        
        sendResponse(200, 'Produk berhasil diperbarui', $fetchStmt->fetch(PDO::FETCH_ASSOC));
    } 
    elseif ($method === 'DELETE') {
        authenticate();
        $delStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $delStmt->execute([$id]);
        sendResponse(200, 'Produk berhasil dihapus', $product);
    }
}

elseif ($requestUri === 'api/products/stats' && $method === 'GET') {
    $total = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $tersedia = $pdo->query("SELECT COUNT(*) FROM products WHERE LOWER(status) = 'tersedia'")->fetchColumn();
    $habis = $pdo->query("SELECT COUNT(*) FROM products WHERE LOWER(status) = 'habis'")->fetchColumn();

    sendResponse(200, 'Statistik berhasil dimuat', [
        'total_products' => (int)$total,
        'available_products' => (int)$tersedia,
        'out_of_stock_products' => (int)$habis
    ]);
}

else {
    sendResponse(404, 'Endpoint tidak ditemukan.');
}
?>