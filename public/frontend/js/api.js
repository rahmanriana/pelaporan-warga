export const API_BASE = '/api';

export function getToken() {
  return localStorage.getItem('access_token');
}

export function setSession({ token, user }) {
  if (token) localStorage.setItem('access_token', token);
  if (user) localStorage.setItem('user', JSON.stringify(user));
}

export function clearSession() {
  localStorage.removeItem('access_token');
  localStorage.removeItem('user');
}

export function getUser() {
  const raw = localStorage.getItem('user');
  if (!raw) return null;
  try {
    return JSON.parse(raw);
  } catch {
    return null;
  }
}

export function requireAuth() {
  const token = getToken();
  if (!token) {
    const next = encodeURIComponent(window.location.pathname + window.location.search);
    window.location.href = `/frontend/login.html?next=${next}`;
    return false;
  }
  return true;
}

export function qs(name) {
  return new URLSearchParams(window.location.search).get(name);
}

export async function apiRequest(path, { method = 'GET', body = null, auth = true, isForm = false } = {}) {
  const headers = {
    Accept: 'application/json',
  };

  if (!isForm) {
    headers['Content-Type'] = 'application/json';
  }

  if (auth) {
    const token = getToken();
    if (token) headers.Authorization = `Bearer ${token}`;
  }

  const resp = await fetch(`${API_BASE}${path}`, {
    method,
    headers,
    body: body && !isForm ? JSON.stringify(body) : body,
  });

  const text = await resp.text();
  let json;
  try {
    json = text ? JSON.parse(text) : null;
  } catch {
    json = { success: false, message: text || 'Invalid JSON response', data: null };
  }

  if (resp.status === 401) {
    // Token invalid/expired → force re-login
    clearSession();
  }

  return { ok: resp.ok, status: resp.status, json };
}

export function showAlert(container, type, message) {
  container.innerHTML = `
    <div class="alert alert-${type} mb-3">${message}</div>
  `;
}

export function renderTopbar() {
  const el = document.getElementById('topbar');
  if (!el) return;

  const user = getUser();
  const isLoggedIn = !!getToken();

  el.innerHTML = `
    <nav class="navbar navbar-expand-lg navbar-dark navbar-pelaporan">
      <div class="container-fluid px-3">
        <a class="navbar-brand fw-bold" href="/frontend/laporans.html">Pelaporan Warga</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navApi" aria-controls="navApi" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navApi">
          <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="/frontend/laporans.html">Laporan</a></li>
            <li class="nav-item"><a class="nav-link" href="/frontend/laporan-form.html">Tambah</a></li>
          </ul>
          <div class="d-flex align-items-center gap-2">
            ${isLoggedIn ? `<span class="text-white-50 small">${user?.name ?? ''} (${user?.role ?? ''})</span>
            <button id="btnLogout" class="btn btn-outline-light btn-sm" type="button">Logout</button>`
            : `<a class="btn btn-outline-light btn-sm" href="/frontend/login.html">Login</a>`}
          </div>
        </div>
      </div>
    </nav>
  `;

  const btn = document.getElementById('btnLogout');
  if (btn) {
    btn.addEventListener('click', async () => {
      try {
        await apiRequest('/logout', { method: 'POST', auth: true });
      } finally {
        clearSession();
        window.location.href = '/frontend/login.html';
      }
    });
  }
}
