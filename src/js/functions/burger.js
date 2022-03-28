import { disableScroll } from '../functions/disable-scroll';
import { enableScroll } from '../functions/enable-scroll';

// console.log('start burger');
const burger = document.querySelector('.burger');
const menu = document.querySelector('.nav__list');

const disScroll = () => {
  let pagePosition = window.scrollY;
  document.body.classList.add('dis-scroll');
  document.body.dataset.position = pagePosition;
  document.body.style.top = -pagePosition + 'px';
}

const enScroll = () => {
  let pagePosition = parseInt(document.body.dataset.position, 10);
  document.body.style.top = 'auto';
  document.body.classList.remove('dis-scroll');
  window.scrollTo({
    top: pagePosition,
    left: 0
  });
  document.body.removeAttribute('data-position');
}

burger.addEventListener('click', (e) => {
  burger.classList.toggle('burger--active');
  menu.classList.toggle('menu--active');

  if (burger.classList.contains('burger--active')) {
    disScroll();
  } else {
    enScroll();
  }
});
