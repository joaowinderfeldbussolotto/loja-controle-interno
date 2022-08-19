const el = document.getElementById('civil_state')
const inputEl = document.getElementById('nameFoo')
const filiacao = document.getElementById('filiacao')
el.addEventListener('change', function () {
  if (this.value === 'Solteira(o)') {
    inputEl.style.display = 'none'
    filiacao.style.display = 'block'
  } else {
    inputEl.style.display = 'block'
    filiacao.style.display = 'none'
  }
})
