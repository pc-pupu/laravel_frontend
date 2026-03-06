document.addEventListener("DOMContentLoaded", function(){
    document.querySelectorAll('.sidebar .nav-link').forEach(function(element){
      
      element.addEventListener('click', function (e) {
  
        let nextEl = element.nextElementSibling;
        let parentEl  = element.parentElement;	
  
          if(nextEl) {
              e.preventDefault();	
              let mycollapse = new bootstrap.Collapse(nextEl);
              
              if(nextEl.classList.contains('show')){
                mycollapse.hide();
              } else {
                  mycollapse.show();
                  // find other submenus with class=show
                  var opened_submenu = parentEl.parentElement.querySelector('.submenu.show');
                  // if it exists, then close all of them
                  if(opened_submenu){
                    new bootstrap.Collapse(opened_submenu);
                  }
              }
          }
      }); // addEventListener
    }) // forEach
  }); 

document.addEventListener("DOMContentLoaded", function(){
    document.querySelectorAll('.sidebar .nav-link').forEach(function(element){
      
      element.addEventListener('click', function (e) {
  
        let nextEl = element.nextElementSibling;
        let parentEl  = element.parentElement;	
  
          if(nextEl) {
              e.preventDefault();	
              let mycollapse = new bootstrap.Collapse(nextEl);
              
              if(nextEl.classList.contains('show')){
                mycollapse.hide();
              } else {
                  mycollapse.show();
                  var opened_submenu = parentEl.parentElement.querySelector('.submenu.show');
                  if(opened_submenu){
                    new bootstrap.Collapse(opened_submenu);
                  }
              }
          }
      });
    })
});

// ------------------------------------------------------------
// Listing table helpers (search + page size + pagination)
// Enhances tables with class: .table-list
// ------------------------------------------------------------
(function () {
  function debounce(fn, wait) {
    let t;
    return function (...args) {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, args), wait);
    };
  }

  function enhanceListingTable(table) {
    if (!table || table.dataset.listingEnhanced === '1') return;
    if (table.getAttribute('data-disable-listing-tools') === '1') return;
    if (table.classList.contains('datatable_no_data_found')) return;
    // If a page is already using DataTables, don't double-enhance.
    if (table.classList.contains('dataTable') || table.closest('.dataTables_wrapper')) return;

    const tbody = table.tBodies && table.tBodies[0] ? table.tBodies[0] : null;
    if (!tbody) return;

    const allRows = Array.from(tbody.rows || []);
    if (!allRows.length) return;

    table.dataset.listingEnhanced = '1';

    const wrapper = table.closest('.table-responsive') || table;

    // Controls (page size + search)
    const controls = document.createElement('div');
    controls.className = 'd-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 listing-controls';
    controls.innerHTML = `
      <div class="d-flex align-items-center gap-2">
        <label class="small text-muted m-0">Show</label>
        <select class="form-select form-select-sm" style="width: auto;">
          <option value="10" selected>10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <span class="small text-muted">entries</span>
      </div>
      <div class="d-flex align-items-center gap-2">
        <label class="small text-muted m-0">Search</label>
        <input type="search" class="form-control form-control-sm" style="width: 220px;" placeholder="Type to search...">
      </div>
    `;

    // Footer (info + pagination)
    const footer = document.createElement('div');
    footer.className = 'd-flex flex-wrap justify-content-between align-items-center gap-2 mt-2 listing-footer';
    const info = document.createElement('div');
    info.className = 'small text-muted';
    const nav = document.createElement('nav');
    nav.setAttribute('aria-label', 'Table pagination');
    footer.appendChild(info);
    footer.appendChild(nav);

    wrapper.parentNode.insertBefore(controls, wrapper);
    wrapper.parentNode.insertBefore(footer, wrapper.nextSibling);

    const perPageSelect = controls.querySelector('select');
    const searchInput = controls.querySelector('input[type="search"]');

    let perPage = parseInt(perPageSelect.value, 10) || 10;
    let currentPage = 1;
    let query = '';

    function getFilteredRows() {
      if (!query) return allRows;
      const q = query.toLowerCase();
      return allRows.filter((row) => (row.textContent || '').toLowerCase().includes(q));
    }

    function createPageItem({ label, page, disabled, active, ariaLabel }) {
      const li = document.createElement('li');
      li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
      const a = document.createElement('a');
      a.className = 'page-link';
      a.href = '#';
      a.textContent = label;
      if (ariaLabel) a.setAttribute('aria-label', ariaLabel);
      a.addEventListener('click', function (e) {
        e.preventDefault();
        if (disabled || active) return;
        currentPage = page;
        render();
      });
      li.appendChild(a);
      return li;
    }

    function createEllipsis() {
      const li = document.createElement('li');
      li.className = 'page-item disabled';
      const span = document.createElement('span');
      span.className = 'page-link';
      span.textContent = '…';
      li.appendChild(span);
      return li;
    }

    function renderPagination(pageCount) {
      nav.innerHTML = '';
      const ul = document.createElement('ul');
      ul.className = 'pagination pagination-sm mb-0';

      ul.appendChild(
        createPageItem({
          label: '«',
          page: Math.max(1, currentPage - 1),
          disabled: currentPage <= 1,
          active: false,
          ariaLabel: 'Previous',
        })
      );

      const windowSize = 2; // pages around current
      const pages = [];
      for (let p = 1; p <= pageCount; p++) pages.push(p);

      const showPages = new Set([1, pageCount]);
      for (let p = currentPage - windowSize; p <= currentPage + windowSize; p++) {
        if (p >= 1 && p <= pageCount) showPages.add(p);
      }

      const ordered = Array.from(showPages).sort((a, b) => a - b);
      let last = 0;
      ordered.forEach((p) => {
        if (last && p - last > 1) ul.appendChild(createEllipsis());
        ul.appendChild(
          createPageItem({
            label: String(p),
            page: p,
            disabled: false,
            active: p === currentPage,
          })
        );
        last = p;
      });

      ul.appendChild(
        createPageItem({
          label: '»',
          page: Math.min(pageCount, currentPage + 1),
          disabled: currentPage >= pageCount,
          active: false,
          ariaLabel: 'Next',
        })
      );

      nav.appendChild(ul);
    }

    function render() {
      const filtered = getFilteredRows();
      const total = filtered.length;
      const pageCount = Math.max(1, Math.ceil(total / perPage));
      if (currentPage > pageCount) currentPage = pageCount;
      if (currentPage < 1) currentPage = 1;

      // Hide all rows first
      allRows.forEach((r) => (r.style.display = 'none'));

      const startIdx = (currentPage - 1) * perPage;
      const endIdx = Math.min(startIdx + perPage, total);
      const slice = filtered.slice(startIdx, endIdx);
      slice.forEach((r) => (r.style.display = ''));

      if (total === 0) {
        info.textContent = 'No matching records found.';
      } else {
        info.textContent = `Showing ${startIdx + 1} to ${endIdx} of ${total} entries`;
      }

      renderPagination(pageCount);
    }

    perPageSelect.addEventListener('change', function () {
      perPage = parseInt(this.value, 10) || 10;
      currentPage = 1;
      render();
    });

    searchInput.addEventListener(
      'input',
      debounce(function () {
        query = (this.value || '').trim();
        currentPage = 1;
        render();
      }, 150)
    );

    render();
  }

  function enhanceAll(root) {
    const scope = root && root.querySelectorAll ? root : document;
    const tables = scope.querySelectorAll('table.table-list');
    tables.forEach(enhanceListingTable);
  }

  document.addEventListener('DOMContentLoaded', function () {
    enhanceAll(document);

    // If some pages render tables dynamically, observe and enhance later.
    const mo = new MutationObserver(function (mutations) {
      for (const m of mutations) {
        for (const node of m.addedNodes) {
          if (node && node.nodeType === 1) {
            enhanceAll(node);
          }
        }
      }
    });
    mo.observe(document.body, { childList: true, subtree: true });
  });
})();
