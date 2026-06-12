/**
 * WAVE FM — Main JavaScript
 */
'use strict';

// ── Audio Stream Player ───────────────────────────────────────
const audio = document.getElementById('liveStream');
const playIcon = document.getElementById('miniPlayIcon');
const vinylDisc = document.querySelector('.vinyl-disc');
const eqBars = document.querySelectorAll('.eq-bar');
let isPlaying = false;

function toggleStream() {
  if (!audio) return;
  if (isPlaying) {
    audio.pause();
    isPlaying = false;
    if (playIcon) { playIcon.className = 'fas fa-play'; }
    if (vinylDisc) vinylDisc.classList.remove('playing');
    eqBars.forEach(b => b.classList.remove('active'));
    // Also update hero button if present
    const heroBtn = document.getElementById('heroPlayIcon');
    if (heroBtn) heroBtn.className = 'fas fa-play';
  } else {
    audio.play().then(() => {
      isPlaying = true;
      if (playIcon) playIcon.className = 'fas fa-pause';
      if (vinylDisc) vinylDisc.classList.add('playing');
      eqBars.forEach(b => b.classList.add('active'));
      const heroBtn = document.getElementById('heroPlayIcon');
      if (heroBtn) heroBtn.className = 'fas fa-pause';
    }).catch(e => {
      showToast('Stream unavailable — check your connection.', 'error');
    });
  }
}

// Hero play button
function toggleHeroPlay() {
  toggleMiniPlayer();
  toggleStream();
}

function setStreamVol(val) {
  if (audio) audio.volume = parseFloat(val);
  // Sync main player volume slider
  const mainVol = document.getElementById('mainVolSlider');
  if (mainVol) mainVol.value = val;
}

// ── Mini Player ───────────────────────────────────────────────
function toggleMiniPlayer() {
  const mp = document.getElementById('miniPlayer');
  if (!mp) return;
  mp.classList.toggle('active');
}

// ── Main embedded player (hero page) ─────────────────────────
const mainPlayBtn = document.getElementById('mainPlayBtn');
if (mainPlayBtn) {
  mainPlayBtn.addEventListener('click', () => {
    toggleMiniPlayer();
    toggleStream();
    const icon = mainPlayBtn.querySelector('i');
    if (icon) icon.className = isPlaying ? 'fas fa-pause' : 'fas fa-play';
  });
}

// ── Volume slider main ────────────────────────────────────────
const mainVolSlider = document.getElementById('mainVolSlider');
if (mainVolSlider) {
  mainVolSlider.addEventListener('input', function() {
    setStreamVol(this.value);
    const miniVol = document.getElementById('miniVolume');
    if (miniVol) miniVol.value = this.value;
  });
}

// ── Navbar scroll effect ──────────────────────────────────────
const nav = document.getElementById('mainNav');
window.addEventListener('scroll', () => {
  if (nav) nav.classList.toggle('scrolled', window.scrollY > 50);
}, { passive: true });

// ── Schedule tabs ─────────────────────────────────────────────
function switchDay(dayIndex) {
  document.querySelectorAll('.day-tab').forEach((tab, i) => {
    tab.classList.toggle('active', i === dayIndex);
  });
  document.querySelectorAll('.sched-pane').forEach((pane, i) => {
    pane.style.display = i === dayIndex ? 'block' : 'none';
  });
}

// Initialise — show today
document.addEventListener('DOMContentLoaded', () => {
  const todayTab = document.querySelector('.day-tab.today-tab');
  if (todayTab) {
    const idx = Array.from(document.querySelectorAll('.day-tab')).indexOf(todayTab);
    if (idx >= 0) switchDay(idx);
  }

  // Intersection observer for fade-up
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); } });
  }, { threshold: 0.12 });
  document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

  // Animate poll bars on load
  animatePollBars();
});

// ── Poll voting ───────────────────────────────────────────────
function votePoll(genreId) {
  const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '/';
  fetch(baseUrl + 'requests/poll', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: 'genre_id=' + encodeURIComponent(genreId)
  })
  .then(r => r.json())
  .then(data => {
    if (data.ok) {
      updatePollResults(data.results);
      showToast('Vote recorded! Thanks for participating.', 'success');
    } else {
      showToast(data.message || 'You have already voted this week.', 'warning');
    }
  })
  .catch(() => showToast('Vote could not be submitted.', 'error'));
}

function updatePollResults(results) {
  if (!results || !results.length) return;
  const total = results.reduce((s, r) => s + parseInt(r.votes), 0);
  results.forEach(r => {
    const pct = total > 0 ? Math.round((r.votes / total) * 100) : 0;
    const fill = document.querySelector(`.poll-fill[data-genre="${r.id}"]`);
    const label = document.querySelector(`.poll-pct[data-genre="${r.id}"]`);
    if (fill) fill.style.width = pct + '%';
    if (label) label.textContent = pct + '%';
  });
}

function animatePollBars() {
  document.querySelectorAll('.poll-fill').forEach(el => {
    const target = el.dataset.width || '0';
    setTimeout(() => { el.style.width = target + '%'; }, 200);
  });
}

// ── Request status update (admin AJAX) ───────────────────────
function updateRequestStatus(id, status, baseUrl) {
  fetch(baseUrl + 'admin/requests/status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: 'id=' + id + '&status=' + encodeURIComponent(status)
  })
  .then(r => r.json())
  .then(data => {
    if (data.ok) {
      const badge = document.querySelector(`#req-status-${id}`);
      if (badge) { badge.textContent = status; badge.className = 'badge bg-' + statusColor(status); }
      showToast('Status updated to: ' + status, 'success');
    }
  });
}

function statusColor(s) {
  return {pending:'secondary', approved:'info', played:'success', rejected:'danger'}[s] || 'secondary';
}

// ── Toast notifications ───────────────────────────────────────
function showToast(message, type = 'info') {
  const container = document.getElementById('toastContainer');
  if (!container) return;
  const t = document.createElement('div');
  const icons = { success:'check-circle', error:'exclamation-circle', warning:'exclamation-triangle', info:'info-circle' };
  const colors = { success:'#2ecc71', error:'#e74c3c', warning:'#d4a847', info:'#3498db' };
  t.style.cssText = `
    background:#1a1a1a; border:1px solid rgba(255,255,255,0.08);
    border-left:3px solid ${colors[type] || colors.info};
    border-radius:3px; padding:14px 16px; margin-top:8px;
    font-family:'DM Mono',monospace; font-size:11px; letter-spacing:0.06em;
    color:#f2f0eb; min-width:280px; max-width:360px;
    animation:slideInRight 0.3s ease; display:flex; align-items:center; gap:10px;
    box-shadow:0 8px 30px rgba(0,0,0,0.4);
  `;
  t.innerHTML = `<i class="fas fa-${icons[type]||'info-circle'}" style="color:${colors[type]||colors.info}"></i><span>${message}</span>`;
  container.appendChild(t);
  setTimeout(() => { t.style.opacity='0'; t.style.transform='translateX(20px)'; t.style.transition='all 0.3s ease'; setTimeout(()=>t.remove(), 300); }, 4000);
}

// Add toast animation CSS
const style = document.createElement('style');
style.textContent = '@keyframes slideInRight{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}';
document.head.appendChild(style);

// ── Admin sidebar ─────────────────────────────────────────────
function toggleSidebar() {
  const sidebar = document.getElementById('adminSidebar');
  const overlay = document.getElementById('sidebarOverlay');
  if (sidebar) sidebar.classList.toggle('open');
  if (overlay) overlay.classList.toggle('active');
}

// ── Confirm delete dialogs ────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function(e) {
      if (!confirm(this.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });
  });

  // Auto-dismiss flash messages
  setTimeout(() => {
    document.querySelectorAll('.flash-msg').forEach(el => {
      el.style.transition = 'opacity 0.5s ease';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 500);
    });
  }, 5000);
});

// ── Calendar ──────────────────────────────────────────────────
let calYear, calMonth;

function initCalendar(year, month) {
  calYear = year;
  calMonth = month;
}

function prevMonth() {
  calMonth--;
  if (calMonth < 1) { calMonth = 12; calYear--; }
  fetchCalendar(calYear, calMonth);
}

function nextMonth() {
  calMonth++;
  if (calMonth > 12) { calMonth = 1; calYear++; }
  fetchCalendar(calYear, calMonth);
}

function fetchCalendar(y, m) {
  const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '/';
  fetch(`${baseUrl}about?cal_year=${y}&cal_month=${m}`, { headers: {'X-Requested-With':'XMLHttpRequest'} })
  .then(r => r.text())
  .then(html => {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const newCal = doc.querySelector('#calendarWidget');
    const curCal = document.getElementById('calendarWidget');
    if (newCal && curCal) curCal.innerHTML = newCal.innerHTML;
    calYear = y; calMonth = m;
  });
}
