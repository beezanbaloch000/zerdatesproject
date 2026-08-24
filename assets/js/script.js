const menuToggle = document.querySelector('.menu-toggle');
const siteNav = document.querySelector('.site-nav');
if (menuToggle && siteNav) {
  menuToggle.addEventListener('click', () => {
    const isOpen = siteNav.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', String(isOpen));
  });
}
setTimeout(() => document.querySelector('.flash')?.classList.add('fade'), 4200);

$(function () {
  $('#service-search').on('input', function () {
    const query = $(this).val().toLowerCase().trim();
    let visibleCount = 0;
    $('.service-result').each(function () {
      const matches = $(this).data('service-search').includes(query);
      $(this).toggle(matches);
      if (matches) visibleCount += 1;
    });
    $('#service-no-results').toggleClass('d-none', visibleCount > 0);
  });

  $('#contact-form').on('submit', function (event) {
    const form = this;
    if (!form.checkValidity()) {
      event.preventDefault();
      form.classList.add('was-validated');
    }
  });
});
