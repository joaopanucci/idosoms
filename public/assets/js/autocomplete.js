// public/assets/js/autocomplete.js
// Simple autocomplete for municipalities and CNES using local JSON files (sample).
async function fetchJSON(url) {
  const res = await fetch(url);
  if (!res.ok) return [];
  return await res.json();
}

function setupAutocomplete(input, listEl, data, getLabel, onSelect) {
  function renderSuggestions(q) {
    listEl.innerHTML = '';
    if (!q) return;
    const qq = q.toLowerCase();
    const matches = data.filter(item => getLabel(item).toLowerCase().includes(qq)).slice(0, 12);
    for (const m of matches) {
      const li = document.createElement('div');
      li.className = 'ac-item';
      li.textContent = getLabel(m);
      li.addEventListener('click', () => onSelect(m));
      listEl.appendChild(li);
    }
  }
  input.addEventListener('input', () => renderSuggestions(input.value));
  input.addEventListener('blur', () => setTimeout(() => listEl.innerHTML = '', 150));
}

async function initAutocomplete() {
  const munName = document.getElementById('munName');
  const munCode = document.querySelector('input[name="patient_municipality"]');
  const munList = document.getElementById('munList');
  const cnesName = document.getElementById('unitName');
  const cnesCode = document.querySelector('input[name="patient_unit_cnes"]');
  const cnesList = document.getElementById('cnesList');

  const municipios = await fetchJSON('/assets/data/municipios.json');
  const cnes = await fetchJSON('/assets/data/cnes.json');

  if (munName && munList && Array.isArray(municipios)) {
    setupAutocomplete(munName, munList, municipios, 
      (m) => `${m.nome} - ${m.uf} (${m.codigo})`,
      (m) => { munName.value = `${m.nome} - ${m.uf}`; if (munCode) munCode.value = m.codigo; }
    );
  }

  if (cnesName && cnesList && Array.isArray(cnes)) {
    setupAutocomplete(cnesName, cnesList, cnes, 
      (u) => `${u.nome} (CNES ${u.cnes})`,
      (u) => { cnesName.value = u.nome; if (cnesCode) cnesCode.value = u.cnes; }
    );
  }
}

document.addEventListener('DOMContentLoaded', initAutocomplete);
