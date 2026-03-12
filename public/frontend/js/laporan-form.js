import { apiRequest, getUser, renderTopbar, requireAuth, showAlert, qs } from './api.js';

const authed = requireAuth();
if (authed) {
  renderTopbar();
}

const alertBox = document.getElementById('alert');
const title = document.getElementById('title');
const form = document.getElementById('formLaporan');
const btnSubmit = document.getElementById('btnSubmit');
const btnReload = document.getElementById('btnReload');
const adminStatusWrap = document.getElementById('adminStatusWrap');
const adminTanggapanWrap = document.getElementById('adminTanggapanWrap');
const tokenInfo = document.getElementById('tokenInfo');

const id = qs('id');
const user = getUser();

if (user?.role === 'admin') {
  adminStatusWrap.style.display = 'block';
  if (adminTanggapanWrap) adminTanggapanWrap.style.display = 'block';
}

btnReload.addEventListener('click', () => window.location.reload());

function setFormValue(name, value) {
  const el = form.querySelector(`[name="${name}"]`);
  if (!el) return;
  el.value = value ?? '';
}

async function loadDetail() {
  if (!id) return;

  title.textContent = 'Detail / Edit Laporan';
  btnSubmit.textContent = 'Update';

  const { ok, json } = await apiRequest(`/laporans/${encodeURIComponent(id)}`);
  if (!ok || !json?.success) {
    showAlert(alertBox, 'danger', json?.message || 'Gagal memuat detail');
    btnReload.style.display = 'inline-block';
    return;
  }

  const l = json.data;
  setFormValue('judul', l.judul);
  setFormValue('kategori', l.kategori);
  setFormValue('lokasi', l.lokasi);
  setFormValue('no_hp', l.no_hp);
  setFormValue('deskripsi', l.deskripsi);
  if (user?.role === 'admin') {
    setFormValue('status', l.status);
  }

  if (l.kode_token) {
    tokenInfo.style.display = 'block';
    tokenInfo.innerHTML = `
      <div class="alert alert-info mb-0">
        Token laporan: <span class="badge text-bg-dark" style="letter-spacing:.08em">${l.kode_token}</span>
      </div>
    `;
  }
}

function buildFormData(isUpdate) {
  const fd = new FormData(form);

  // For update we use POST + _method=PUT so multipart is safe across servers
  if (isUpdate) {
    fd.append('_method', 'PUT');
  }

  // Remove empty file field (avoid validation noise)
  const foto = fd.get('foto');
  if (foto instanceof File && foto.size === 0) {
    fd.delete('foto');
  }

  // Admin-only status
  if (user?.role !== 'admin') {
    fd.delete('status');
  }

  return fd;
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  alertBox.innerHTML = '';

  const isUpdate = !!id;

  const fd = buildFormData(isUpdate);

  if (user?.role === 'admin') {
    const status = String(fd.get('status') || '').trim();
    const isi = String(fd.get('isi_tanggapan') || '').trim();
    if (status && !isi) {
      showAlert(alertBox, 'danger', 'Penanganan wajib diisi saat admin mengubah status.');
      return;
    }
  }

  const path = isUpdate ? `/laporans/${encodeURIComponent(id)}` : '/laporans';
  const method = 'POST';

  const { ok, json, status } = await apiRequest(path, {
    method,
    isForm: true,
    body: fd,
  });

  if (!ok || !json?.success) {
    const msg = json?.message || `Gagal menyimpan (HTTP ${status})`;
    showAlert(alertBox, 'danger', msg);
    return;
  }

  const laporan = json.data;
  if (!isUpdate && laporan?.kode_token) {
    tokenInfo.style.display = 'block';
    tokenInfo.innerHTML = `
      <div class="alert alert-success">
        Berhasil membuat laporan. Simpan token ini:
        <span class="badge text-bg-dark ms-2" style="letter-spacing:.08em">${laporan.kode_token}</span>
      </div>
    `;
  }

  showAlert(alertBox, 'success', json?.message || 'Berhasil');
  setTimeout(() => {
    window.location.href = '/frontend/laporans.html';
  }, 600);
});

if (authed) {
  loadDetail();
}
