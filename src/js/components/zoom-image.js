export const zoomImage = (function() {
  const zoomElements = document.querySelectorAll('.reg__img');

  zoomElements.forEach(zoomElement => {
    zoomElement.addEventListener('click', (e) => {
      const target = e.target;
      const link = location.href + target.getAttribute('src').substr(2);
      window.open(link);
    });
  });
}());
