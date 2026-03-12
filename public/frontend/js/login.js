import { apiRequest, renderTopbar, setSession, showAlert, qs } from './api.js';

renderTopbar();

const form = document.getElementById('formLogin');
const alertBox = document.getElementById('alert');

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  alertBox.innerHTML = '';

  const fd = new FormData(form);
  const payload = {
    email: String(fd.get('email') || '').trim(),
    password: String(fd.get('password') || ''),
  };

  const { ok, json } = await apiRequest('/login', {
    method: 'POST',
    auth: false,
    body: payload,
  });

  if (!ok || !json?.success) {
    showAlert(alertBox, 'danger', json?.message || 'Login gagal');
    return;
  }

  setSession({ token: json.data.access_token, user: json.data.user });

  const next = qs('next');
  if (next) {
    window.location.href = decodeURIComponent(next);
    return;
  }

  window.location.href = '/frontend/laporans.html';
});
