// JS básico para confirmações e comportamentos simples
document.addEventListener('click', (e) => {
  if (e.target.matches('[data-confirm]')) {
    if (!confirm(e.target.getAttribute('data-confirm'))) {
      e.preventDefault();
    }
  }
});