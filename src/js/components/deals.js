import $ from 'jquery';
export const deals = (function() {
  $('.error-msg').hide();
  function renderRowDate(data) {
    const table = document.getElementById('info-table');

    while(table.rows.length > 0) { // Очистка таблицы
        table.deleteRow(0);
      }

    table.insertAdjacentHTML('beforeend', `
      <thead class="reestr__thead">
        <tr>
          <th class="reestr__th">ФИО </th>
          <th class="reestr__th">№ сертификата </th>
          <th class="reestr__th">Срок действия </th>
          <th class="reestr__th">Специальность </th>
        </tr>
      </thead>
    `);

    data.forEach(dataEl => {
        table.insertAdjacentHTML('beforeend', `
        <tr class="reestr__oddrow">
            <td class="reestr__td" data-label="ФИО">${dataEl.FIO} </td>
            <td class="reestr__td" data-label="№ сертификата">${dataEl.SERT_NUMBER} </td>
            <td class="reestr__td" data-label="Срок действия">${dataEl.DATE_START} по ${dataEl.DATE_END} </td>
            <td class="reestr__td" data-label="Специальность">${dataEl.SPEC} </td>
        </tr>
    `);
    });
  }

  const showLoader = () => {
    const loader = document.querySelector('.lds-ring');
    loader.classList.add('loader--show');
    loader.classList.remove('loader--hide');
  }

  const hideLoader = () => {
    const loader = document.querySelector('.lds-ring');
    loader.classList.add('loader--hide');
    loader.classList.remove('loader--show');
  }

  const showErrMsg = (msg) => {
    $('.error-msg').show();
    if (msg) {
      $('.error-msg').text(msg);
    }
  }

  document.addEventListener('submit', (e) => {
    e.preventDefault();
    $('.error-msg').hide();

    const msg   = $('#expert-search').serialize();

    $.ajax({
        type: 'POST',
        url: '/ex/index.php',
        data: msg,
        beforeSend: showLoader,
        success: function(data) {
            hideLoader();
            const parseData = JSON.parse(data);
            if (parseData.length < 1) {
              showErrMsg('Сертификат не найден');
            }
            renderRowDate(parseData);
        },
        error:  function(xhr, str){
            console.log('Возникла ошибка: ' + xhr.responseCode);
            showErrMsg('Ошибка на сервере' + xhr.responseCode);
        }
    });
  });
}());
