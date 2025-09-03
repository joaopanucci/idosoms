// public/assets/js/dashboard.js
let chartMonth, chartClass;
async function loadChartsWithFilters() {
  const qs = new URLSearchParams();
  const f = document.getElementById('fromDate')?.value;
  const t = document.getElementById('toDate')?.value;
  const type = document.getElementById('selType')?.value;
  const mun = document.getElementById('munCode')?.value;
  if (f) qs.set('from', f);
  if (t) qs.set('to', t);
  if (type) qs.set('type', type);
  if (mun) qs.set('mun', mun);
  const res = await fetch('/api_stats.php' + (qs.toString() ? ('?' + qs.toString()) : ''));
  if (!res.ok) return;
  const data = await res.json();
  plotByMonth(data.byMonth || []);
  plotByClass(data.byClass || []);
}

function destroyChart(ch) { if (ch) { ch.destroy(); } }

function plotByMonth(rows) {
  const ctx = document.getElementById('chartByMonth');
  if (!ctx) return;
  const groups = {};
  const labelsSet = new Set();
  rows.forEach(r => {
    const ym = r.ym;
    labelsSet.add(ym);
    const type = r.type;
    groups[type] = groups[type] || {};
    groups[type][ym] = (groups[type][ym] || 0) + parseInt(r.c, 10);
  });
  const labels = Array.from(labelsSet).sort();
  const datasets = Object.keys(groups).map((type) => ({
    label: type,
    data: labels.map(l => groups[type][l] || 0),
    borderWidth: 2,
    fill: false,
  }));

  destroyChart(chartMonth);
  chartMonth = new Chart(ctx, {
    type: 'line',
    data: { labels, datasets },
    options: {
      responsive: true,
      plugins: { legend: { position: 'top' }, title: { display: true, text: 'Avaliações por mês' } },
      scales: { y: { beginAtZero: true } }
    }
  });
}

function plotByClass(rows) {
  const ctx = document.getElementById('chartByClass');
  if (!ctx) return;
  const map = {};
  rows.forEach(r => {
    const key = r.classification;
    map[key] = (map[key] || 0) + parseInt(r.c, 10);
  });
  const labels = Object.keys(map);
  const data = labels.map(k => map[k]);

  destroyChart(chartClass);
  chartClass = new Chart(ctx, {
    type: 'bar',
    data: { labels, datasets: [{ label: 'Total', data, borderWidth: 1 }]},
    options: {
      responsive: true,
      plugins: { legend: { display: false }, title: { display: true, text: 'Distribuição por classificação' } },
      scales: { y: { beginAtZero: true } }
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('btnApply')?.addEventListener('click', loadChartsWithFilters);
  loadChartsWithFilters();
});
