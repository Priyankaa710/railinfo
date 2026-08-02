/**
 * RailInfo — frontend behaviour
 * Station autocomplete talks to TrainController::stationSuggest()
 * (GET /trains/station-suggest?term=...) which is backed by the
 * CI4 Query Builder search on the stations table.
 */
(function () {
  'use strict';

  function buildSuggestUrl(term) {
    // If the app is installed in a subfolder, prefer an absolute path guess:
    const path = window.location.pathname.split('/').filter(Boolean);
    let base = '/';
    if (path.length && !['trains', 'pnr', 'about'].includes(path[0])) {
      base = '/' + path[0] + '/';
    }
    return window.location.origin + base + 'trains/station-suggest?term=' + encodeURIComponent(term);
  }

  function debounce(fn, delay) {
    let t;
    return function (...args) {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  function initAutocomplete(input) {
    const wrapper = input.closest('.position-relative') || input.parentElement;
    let box = wrapper.querySelector('.ri-suggestions');
    if (!box) {
      box = document.createElement('div');
      box.className = 'ri-suggestions';
      wrapper.appendChild(box);
    }

    const fetchSuggestions = debounce(async function (term) {
      if (term.length < 2) {
        box.classList.remove('show');
        box.innerHTML = '';
        return;
      }
      try {
        const res = await fetch(buildSuggestUrl(term), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) return;
        const stations = await res.json();
        renderSuggestions(stations);
      } catch (err) {
        // Fail silently — the plain text input still works without JS.
        console.warn('Station suggest failed', err);
      }
    }, 250);

    function renderSuggestions(stations) {
      if (!Array.isArray(stations) || stations.length === 0) {
        box.classList.remove('show');
        box.innerHTML = '';
        return;
      }
      box.innerHTML = stations.map((s) => `
        <div class="item" data-value="${s.name} (${s.code})">
          <strong>${s.name}</strong> <span class="text-muted">(${s.code})</span>
          <small>${s.city ?? ''}${s.state ? ', ' + s.state : ''}</small>
        </div>
      `).join('');
      box.classList.add('show');
    }

    input.addEventListener('input', (e) => fetchSuggestions(e.target.value.trim()));
    input.addEventListener('focus', (e) => {
      if (e.target.value.trim().length >= 2) fetchSuggestions(e.target.value.trim());
    });
    document.addEventListener('click', (e) => {
      if (!wrapper.contains(e.target)) {
        box.classList.remove('show');
      }
    });
    box.addEventListener('click', (e) => {
      const item = e.target.closest('.item');
      if (!item) return;
      input.value = item.dataset.value;
      box.classList.remove('show');
      box.innerHTML = '';
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-autocomplete="station"]').forEach(initAutocomplete);

    // PNR quick-check widget (progressive enhancement — form still
    // submits normally via POST /pnr/track if JS fails).
    document.querySelectorAll('.ri-pnr-input').forEach((input) => {
      input.addEventListener('input', () => {
        input.value = input.value.replace(/\D/g, '').slice(0, 10);
      });
    });
  });
})();
