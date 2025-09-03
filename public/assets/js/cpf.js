// public/assets/js/cpf.js
// Máscara simples de CPF
document.addEventListener('DOMContentLoaded', () => {
  const cpf = document.getElementById('cpf');
  if (!cpf) return;
  cpf.addEventListener('input', () => {
    let v = cpf.value.replace(/\D+/g,'');
    v = v.slice(0,11);
    if (v.length > 9) cpf.value = v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
    else if (v.length > 6) cpf.value = v.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
    else if (v.length > 3) cpf.value = v.replace(/(\d{3})(\d{0,3})/, '$1.$2');
    else cpf.value = v;
  });
});
