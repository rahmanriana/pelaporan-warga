import { apiRequest, getUser, renderTopbar, requireAuth, showAlert } from './api.js';

const authed = requireAuth();
if (authed) {
  renderTopbar();
}

const alertBox = document.getElementById('alert');
const grid = document.getElementById('grid');

function badgeClass(status) {
  if (status === 'menunggu') return 'badge-menunggu';
  if (status === 'diproses') return 'badge-diproses';
  if (status === 'ditolak') return 'badge-ditolak';
  return 'badge-selesai';
}

function escapeHtml(s) {
  return String(s)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function card(l) {
  const foto = l.foto_url || '/images/placeholder-report.svg';
  const lastTanggapan = Array.isArray(l.tanggapans) && l.tanggapans.length > 0 ? l.tanggapans[0] : null;
  const canEdit = (() => {
    const user = getUser();
    if (!user) return false;
    if (user.role === 'admin') return true;
    return Number(l.user_id) === Number(user.id);
  })();

  return `
    <div class="col-sm-6 col-xl-4">
      <div class="card card-laporan h-100">
        <img src="${foto}" class="w-100" alt="Foto laporan" style="height:190px; object-fit:cover;" />
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
              <div class="fw-bold">${escapeHtml(l.judul)}</div>
              <div class="text-muted small">
                ${escapeHtml(l.kategori)} • ${escapeHtml(l.lokasi)}
              </div>
              ${l.kode_token ? `<div class="text-muted small mt-1">Token: <span class="badge text-bg-dark" style="letter-spacing:.08em">${escapeHtml(l.kode_token)}</span></div>` : ''}
              ${lastTanggapan?.isi_tanggapan ? `<div class="text-muted small mt-2"><div class="fw-semibold">Penanganan:</div><div>${escapeHtml(lastTanggapan.isi_tanggapan)}</div></div>` : ''}
            </div>
            <span class="badge ${badgeClass(l.status)}">${escapeHtml(l.status)}</span>
          </div>
        </div>
        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3 d-flex gap-2">
          <a class="btn btn-primary btn-sm" href="/frontend/laporan-form.html?id=${encodeURIComponent(l.id)}">Detail/Edit</a>
          ${canEdit ? `<button class="btn btn-outline-danger btn-sm" data-id="${escapeHtml(l.id)}" data-action="delete">Delete</button>` : ''}
        </div>
      </div>
    </div>
  `;
}

async function load() {
  alertBox.innerHTML = '';
  grid.innerHTML = '';

  const { ok, json } = await apiRequest('/laporans');
  if (!ok || !json?.success) {
    showAlert(alertBox, 'danger', json?.message || 'Gagal memuat data');
    return;
  }

  const items = Array.isArray(json.data) ? json.data : [];
  if (items.length === 0) {
    showAlert(alertBox, 'info', 'Belum ada laporan.');
    return;
  }

  grid.innerHTML = items.map(card).join('');

  grid.querySelectorAll('button[data-action="delete"]').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const id = btn.getAttribute('data-id');
      if (!confirm('Hapus laporan ini?')) return;

      const { ok, json } = await apiRequest(`/laporans/${encodeURIComponent(id)}`, { method: 'DELETE' });
      if (!ok || !json?.success) {
        showAlert(alertBox, 'danger', json?.message || 'Gagal hapus');
        return;
      }

      showAlert(alertBox, 'success', 'Laporan berhasil dihapus.');
      await load();
    });
  });
}

if (authed) {
  load();
}
