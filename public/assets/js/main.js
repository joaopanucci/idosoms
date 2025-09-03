// public/assets/js/main.js
document.addEventListener('DOMContentLoaded', () => {
  // Dropdowns touch-friendly
  document.querySelectorAll('.dropdown').forEach(dd => {
    dd.addEventListener('click', e => {
      if (e.target.closest('.menu')) return;
      dd.classList.toggle('open');
    });
  });

  // Evaluation dynamic render (if present)
  const qWrap = document.getElementById('questions');
  if (qWrap && (window.IVCF20_QUESTIONS || window.IVSF10_QUESTIONS)) {
    const type = document.querySelector('input[name="type"]').value;
    const questions = type === 'IVSF10' ? window.IVSF10_QUESTIONS : window.IVCF20_QUESTIONS;
    renderQuestions(qWrap, questions);
  }
});

function renderQuestions(container, questions) {
  container.innerHTML = '';
  const frag = document.createDocumentFragment();
  questions.forEach((q, idx) => {
    const field = document.createElement('div');
    field.className = 'field';
    const id = q.id || ('q_' + idx);
    const label = document.createElement('label');
    label.textContent = (idx+1) + ') ' + q.label;
    field.appendChild(label);
    if (q.type === 'radio') {
      q.options.forEach((opt, i) => {
        const rid = `${id}_${i}`;
        const wrap = document.createElement('div');
        const inp = document.createElement('input');
        inp.type = 'radio'; inp.name = id; inp.id = rid; inp.value = opt.points;
        inp.addEventListener('change', updateScore);
        const l = document.createElement('label'); l.htmlFor = rid; l.textContent = opt.label;
        wrap.appendChild(inp); wrap.appendChild(l);
        field.appendChild(wrap);
      });
    } else if (q.type === 'select') {
      const sel = document.createElement('select');
      sel.name = id;
      q.options.forEach(opt => {
        const o = document.createElement('option');
        o.value = opt.points; o.textContent = opt.label;
        sel.appendChild(o);
      });
      sel.addEventListener('change', updateScore);
      field.appendChild(sel);
    }
    frag.appendChild(field);
  });
  container.appendChild(frag);
  updateScore();
}

function updateScore() {
  const answers = {};
  const inputs = document.querySelectorAll('#questions input[type=radio]:checked, #questions select');
  let score = 0;
  inputs.forEach(el => {
    const name = el.name;
    const val = parseInt(el.value || '0', 10) || 0;
    answers[name] = val;
    score += val;
  });
  const type = document.querySelector('input[name="type"]').value;
  const cls = classify(type, score);

  const scoreEl = document.getElementById('score');
  const classEl = document.getElementById('class');
  if (scoreEl) scoreEl.textContent = score;
  if (classEl) classEl.textContent = cls;

  const answersInput = document.getElementById('answersInput');
  const scoreInput = document.getElementById('scoreInput');
  const classInput = document.getElementById('classInput');
  if (answersInput) answersInput.value = JSON.stringify(answers);
  if (scoreInput) scoreInput.value = String(score);
  if (classInput) classInput.value = cls;
}

function classify(type, score) {
  if (type === 'IVSF10') {
    if (score <= 3) return 'Baixa';
    if (score <= 6) return 'Moderada';
    return 'Alta';
  } else {
    if (score <= 6) return 'Robusto';
    if (score <= 14) return 'Pré-frágil';
    return 'Frágil';
  }
}


function validateEvalForm() {
  const form = document.getElementById('evalForm');
  if (!form) return true;
  const cnes = form.querySelector('input[name="patient_unit_cnes"]');
  const ibge = form.querySelector('input[name="patient_municipality"]');
  let ok = true;
  if (cnes && cnes.value) {
    if (!/^\d{7}$/.test(cnes.value)) {
      cnes.classList.add('invalid'); ok = false;
    } else { cnes.classList.remove('invalid'); }
  }
  if (ibge && ibge.value) {
    if (!/^\d{6,7}$/.test(ibge.value)) {
      ibge.classList.add('invalid'); ok = false;
    } else { ibge.classList.remove('invalid'); }
  }
  if (!ok) alert('Verifique os campos: CNES deve ter 7 dígitos; IBGE com 6 ou 7 dígitos.');
  return ok;
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('evalForm');
  if (form) form.addEventListener('submit', (e) => { if (!validateEvalForm()) e.preventDefault(); });
});
