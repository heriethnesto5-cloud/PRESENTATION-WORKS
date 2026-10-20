const cssLink = document.getElementById('cssLink');
const controls = document.querySelectorAll('[data-style]');
const storageKey = 'homepage-selected-stylesheet';
function setStyle(file) {
  cssLink.disabled = !file;
  if (file) cssLink.href = file;
  document.title = file ? `Homepage - ${file.replace('.css', '')}` : 'Homepage - No Stylesheet';
  controls.forEach((control) => control.classList.toggle('active', control.dataset.style === file));
  localStorage.setItem(storageKey, file);
}
controls.forEach((control) => control.onclick = (event) => {
  event.preventDefault();
  setStyle(control.dataset.style);
});
const saved = localStorage.getItem(storageKey);
setStyle([...controls].some((control) => control.dataset.style === saved) ? saved : 'style1.css');