import { apiRequest, showAlert } from './api.js';

const form = document.getElementById('formRegister');
const alertBox = document.getElementById('alert');

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  alertBox.innerHTML = '';

  const fd = new FormData(form);
  const payload = {
    name: String(fd.get('name') || '').trim(),
    email: String(fd.get('email') || '').trim(),
    password: String(fd.get('password') || ''),
    password_confirmation: String(fd.get('password_confirmation') || ''),
  };

  const { ok, json } = await apiRequest('/register', {
    method: 'POST',
    auth: false,
    body: payload,
  });

  if (!ok || !json?.success) {
    const msg = json?.message || 'Registrasi gagal';
    showAlert(alertBox, 'danger', msg);
    return;
  }

  showAlert(alertBox, 'success', 'Registrasi berhasil. Silakan login.');
  setTimeout(() => {
    window.location.href = '/frontend/login.html';
  }, 600);
});
