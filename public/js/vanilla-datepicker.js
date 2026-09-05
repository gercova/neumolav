/**
 * VanillaDatePicker - Pure Vanilla JavaScript Date Picker
 * Zero third-party dependencies. Appends to document.body to avoid
 * breaking Bootstrap input-group layout.
 */
class VanillaDatePicker {
    constructor(element, options = {}) {
        this.input = typeof element === 'string' ? document.querySelector(element) : element;
        if (!this.input) return;

        this.options = Object.assign({
            minDate: '1900-01-01',
            maxDate: new Date(),
            defaultDate: null,
            onSelect: null,
            autoClose: true,
            triggerButton: null
        }, options);

        // Normalize min/max dates
        this.minDate = this.options.minDate ? this.parseDate(this.options.minDate) : null;
        this.maxDate = this.options.maxDate ? this.parseDate(this.options.maxDate) : null;

        // Spanish localization
        this.months = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        this.monthsShort = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dic'];
        this.weekdays = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'];

        // State: 'days' | 'months' | 'years'
        this.viewMode = 'days';
        this.selectedDate = null;

        // Parse current value from input (supports dd-mm-yyyy and yyyy-mm-dd)
        const inputVal = this.input.value ? this.input.value.trim() : '';
        if (inputVal) {
            this.selectedDate = this.parseDate(inputVal);
        } else if (this.options.defaultDate) {
            this.selectedDate = this.parseDate(this.options.defaultDate);
        }

        // Navigation date
        const initialDate = this.selectedDate || (this.maxDate && this.maxDate < new Date() ? this.maxDate : new Date());
        this.currentYear  = initialDate.getFullYear();
        this.currentMonth = initialDate.getMonth();
        this.yearGridStart = Math.floor(this.currentYear / 12) * 12;

        this.isOpen = false;
        this._closeOnOutsideClick = this._closeOnOutsideClick.bind(this);
        this._closeOnKeydown      = this._closeOnKeydown.bind(this);

        this.init();
    }

    init() {
        this.createDOM();
        this.bindEvents();
    }

    createDOM() {
        // Container is appended to body so it never disrupts input-group or flex/grid layouts
        this.container = document.createElement('div');
        this.container.className = 'vdp-container';
        this.container.setAttribute('role', 'dialog');
        this.container.setAttribute('aria-modal', 'true');
        document.body.appendChild(this.container);
        this.render();
    }

    /* ─────────────────────────────────────────────
       POSITIONING (fixed, relative to trigger)
    ───────────────────────────────────────────── */
    _reposition() {
        const anchor = this.options.triggerButton
            ? (typeof this.options.triggerButton === 'string'
                ? document.querySelector(this.options.triggerButton)
                : this.options.triggerButton)
            : this.input;

        const rect = anchor.getBoundingClientRect();
        const pickerH = 360;
        const pickerW = 310;
        const spaceBelow = window.innerHeight - rect.bottom;
        const spaceRight = window.innerWidth - rect.left;

        let top, left;

        if (spaceBelow < pickerH && rect.top > pickerH) {
            // Open upward
            top = rect.top + window.scrollY - pickerH - 4;
        } else {
            // Open downward
            top = rect.bottom + window.scrollY + 4;
        }

        if (spaceRight < pickerW) {
            left = Math.max(4, rect.right - pickerW + window.scrollX);
        } else {
            left = rect.left + window.scrollX;
        }

        this.container.style.position = 'absolute';
        this.container.style.top  = `${top}px`;
        this.container.style.left = `${left}px`;
        this.container.style.width = `${pickerW}px`;
        this.container.style.zIndex = '9999';
    }

    /* ─────────────────────────────────────────────
       RENDER
    ───────────────────────────────────────────── */
    render() {
        this.container.innerHTML = '';

        if (this.viewMode === 'days')   { this.renderHeader_days();   this.renderDaysView();   }
        if (this.viewMode === 'months') { this.renderHeader_months(); this.renderMonthsView(); }
        if (this.viewMode === 'years')  { this.renderHeader_years();  this.renderYearsView();  }

        this.renderFooter();
    }

    _makeNavBtn(html, title, onClick) {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'vdp-nav-btn';
        b.innerHTML = html;
        b.title = title;
        b.addEventListener('click', (e) => { e.stopPropagation(); onClick(); });
        return b;
    }

    _makeTitleBtn(text, onClick) {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'vdp-title-btn';
        b.textContent = text;
        b.addEventListener('click', (e) => { e.stopPropagation(); onClick(); });
        return b;
    }

    _appendHeader(nodes) {
        const header = document.createElement('div');
        header.className = 'vdp-header';
        nodes.forEach(n => header.appendChild(n));
        this.container.appendChild(header);
    }

    renderHeader_days() {
        const titleGroup = document.createElement('div');
        titleGroup.className = 'vdp-title-group';
        titleGroup.appendChild(this._makeTitleBtn(this.months[this.currentMonth], () => {
            this.viewMode = 'months'; this.render();
        }));
        titleGroup.appendChild(this._makeTitleBtn(this.currentYear, () => {
            this.yearGridStart = Math.floor(this.currentYear / 12) * 12;
            this.viewMode = 'years'; this.render();
        }));

        this._appendHeader([
            this._makeNavBtn('&laquo;', 'Año anterior',  () => this.changeYear(-1)),
            this._makeNavBtn('&lsaquo;', 'Mes anterior', () => this.changeMonth(-1)),
            titleGroup,
            this._makeNavBtn('&rsaquo;', 'Mes siguiente', () => this.changeMonth(1)),
            this._makeNavBtn('&raquo;', 'Año siguiente',  () => this.changeYear(1)),
        ]);
    }

    renderHeader_months() {
        this._appendHeader([
            this._makeNavBtn('&lsaquo;', 'Año anterior', () => { this.changeYear(-1); }),
            this._makeTitleBtn(this.currentYear, () => {
                this.yearGridStart = Math.floor(this.currentYear / 12) * 12;
                this.viewMode = 'years'; this.render();
            }),
            this._makeNavBtn('&rsaquo;', 'Año siguiente', () => { this.changeYear(1); }),
        ]);
    }

    renderHeader_years() {
        const span = document.createElement('span');
        span.className = 'vdp-title-btn';
        span.textContent = `${this.yearGridStart} – ${this.yearGridStart + 11}`;
        this._appendHeader([
            this._makeNavBtn('&laquo;', 'Década anterior', () => { this.yearGridStart -= 12; this.render(); }),
            span,
            this._makeNavBtn('&raquo;', 'Década siguiente', () => { this.yearGridStart += 12; this.render(); }),
        ]);
    }

    renderDaysView() {
        // Weekday row
        const weekRow = document.createElement('div');
        weekRow.className = 'vdp-weekdays';
        this.weekdays.forEach(d => {
            const cell = document.createElement('div');
            cell.className = 'vdp-weekday';
            cell.textContent = d;
            weekRow.appendChild(cell);
        });
        this.container.appendChild(weekRow);

        // Day grid
        const grid = document.createElement('div');
        grid.className = 'vdp-days-grid';

        const firstDay    = new Date(this.currentYear, this.currentMonth, 1).getDay();
        const totalDays   = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
        const prevTotal   = new Date(this.currentYear, this.currentMonth, 0).getDate();

        for (let i = firstDay - 1; i >= 0; i--) {
            grid.appendChild(this.createDayCell(
                new Date(this.currentYear, this.currentMonth - 1, prevTotal - i), prevTotal - i, true
            ));
        }
        for (let d = 1; d <= totalDays; d++) {
            grid.appendChild(this.createDayCell(
                new Date(this.currentYear, this.currentMonth, d), d, false
            ));
        }
        const total = firstDay + totalDays;
        const fill  = total <= 35 ? 35 - total : 42 - total;
        for (let d = 1; d <= fill; d++) {
            grid.appendChild(this.createDayCell(
                new Date(this.currentYear, this.currentMonth + 1, d), d, true
            ));
        }

        this.container.appendChild(grid);
    }

    createDayCell(date, text, adjacent) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'vdp-day-cell';
        btn.textContent = text;
        if (adjacent) btn.classList.add('vdp-adjacent');

        const today = new Date();
        if (this.isSameDay(date, today))                              btn.classList.add('vdp-today');
        if (this.selectedDate && this.isSameDay(date, this.selectedDate)) btn.classList.add('vdp-selected');

        if (this.isDateDisabled(date)) {
            btn.classList.add('vdp-disabled');
            btn.disabled = true;
        } else {
            btn.addEventListener('click', (e) => { e.stopPropagation(); this.selectDate(date); });
        }
        return btn;
    }

    renderMonthsView() {
        const grid = document.createElement('div');
        grid.className = 'vdp-selection-grid';
        const today = new Date();

        this.monthsShort.forEach((name, idx) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'vdp-grid-item';
            btn.textContent = name;

            if (today.getFullYear() === this.currentYear && today.getMonth() === idx) btn.classList.add('vdp-today');
            if (this.selectedDate && this.selectedDate.getFullYear() === this.currentYear && this.selectedDate.getMonth() === idx) btn.classList.add('vdp-selected');

            const startOfMonth = new Date(this.currentYear, idx, 1);
            const endOfMonth   = new Date(this.currentYear, idx + 1, 0);
            if ((this.maxDate && startOfMonth > this.maxDate && endOfMonth > this.maxDate) ||
                (this.minDate && endOfMonth < this.minDate)) {
                btn.classList.add('vdp-disabled'); btn.disabled = true;
            } else {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.currentMonth = idx;
                    this.viewMode = 'days';
                    this.render();
                });
            }
            grid.appendChild(btn);
        });
        this.container.appendChild(grid);
    }

    renderYearsView() {
        const grid = document.createElement('div');
        grid.className = 'vdp-selection-grid';
        const today = new Date();

        for (let y = this.yearGridStart; y < this.yearGridStart + 12; y++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'vdp-grid-item';
            btn.textContent = y;

            if (today.getFullYear() === y) btn.classList.add('vdp-today');
            if (this.selectedDate && this.selectedDate.getFullYear() === y) btn.classList.add('vdp-selected');

            if ((this.maxDate && y > this.maxDate.getFullYear()) ||
                (this.minDate && y < this.minDate.getFullYear())) {
                btn.classList.add('vdp-disabled'); btn.disabled = true;
            } else {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.currentYear = y;
                    this.viewMode = 'months';
                    this.render();
                });
            }
            grid.appendChild(btn);
        }
        this.container.appendChild(grid);
    }

    renderFooter() {
        const footer = document.createElement('div');
        footer.className = 'vdp-footer';

        const todayBtn = document.createElement('button');
        todayBtn.type = 'button'; todayBtn.className = 'vdp-btn vdp-btn-today'; todayBtn.textContent = 'Hoy';
        todayBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const t = new Date();
            if (!this.isDateDisabled(t)) this.selectDate(t);
        });

        const clearBtn = document.createElement('button');
        clearBtn.type = 'button'; clearBtn.className = 'vdp-btn vdp-btn-clear'; clearBtn.textContent = 'Limpiar';
        clearBtn.addEventListener('click', (e) => { e.stopPropagation(); this.clearDate(); });

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button'; closeBtn.className = 'vdp-btn vdp-btn-close'; closeBtn.textContent = 'Cerrar';
        closeBtn.addEventListener('click', (e) => { e.stopPropagation(); this.close(); });

        footer.appendChild(todayBtn);
        footer.appendChild(clearBtn);
        footer.appendChild(closeBtn);
        this.container.appendChild(footer);
    }

    /* ─────────────────────────────────────────────
       DATE ACTIONS
    ───────────────────────────────────────────── */
    selectDate(date) {
        this.selectedDate = new Date(date);
        this.currentYear  = this.selectedDate.getFullYear();
        this.currentMonth = this.selectedDate.getMonth();

        // Write dd-mm-yyyy into the input
        const display = this.formatDateDisplay(this.selectedDate);
        this.input.value = display;
        this._triggerEvents();

        if (typeof this.options.onSelect === 'function') {
            this.options.onSelect(this.selectedDate, display, this);
        }

        if (this.options.autoClose) {
            this.close();
        } else {
            this.render();
        }
    }

    clearDate() {
        this.selectedDate = null;
        this.input.value  = '';
        this._triggerEvents();
        if (typeof this.options.onSelect === 'function') {
            this.options.onSelect(null, '', this);
        }
        this.close();
    }

    _triggerEvents() {
        this.input.dispatchEvent(new Event('change', { bubbles: true }));
        this.input.dispatchEvent(new Event('input',  { bubbles: true }));
    }

    /* ─────────────────────────────────────────────
       OPEN / CLOSE / TOGGLE
    ───────────────────────────────────────────── */
    open() {
        if (this.isOpen) { this._reposition(); return; }

        this.viewMode = 'days';
        if (this.selectedDate) {
            this.currentYear  = this.selectedDate.getFullYear();
            this.currentMonth = this.selectedDate.getMonth();
        }
        this.render();
        this._reposition();
        this.container.classList.add('vdp-open');
        this.isOpen = true;

        // Delayed outside-click listener so current click doesn't immediately close
        setTimeout(() => {
            document.addEventListener('click',   this._closeOnOutsideClick);
            document.addEventListener('keydown', this._closeOnKeydown);
        }, 10);
    }

    close() {
        if (!this.isOpen) return;
        this.container.classList.remove('vdp-open');
        this.isOpen = false;
        document.removeEventListener('click',   this._closeOnOutsideClick);
        document.removeEventListener('keydown', this._closeOnKeydown);
    }

    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    _closeOnOutsideClick(e) {
        if (!this.container.contains(e.target) &&
            e.target !== this.input &&
            e.target !== this._triggerEl) {
            this.close();
        }
    }

    _closeOnKeydown(e) {
        if (e.key === 'Escape') this.close();
    }

    /* ─────────────────────────────────────────────
       EVENT BINDING
    ───────────────────────────────────────────── */
    bindEvents() {
        // Trigger button
        if (this.options.triggerButton) {
            this._triggerEl = typeof this.options.triggerButton === 'string'
                ? document.querySelector(this.options.triggerButton)
                : this.options.triggerButton;

            if (this._triggerEl) {
                this._triggerEl.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.toggle();
                });
            }
        }

        // Allow toggling by clicking the input itself too
        this.input.addEventListener('click', (e) => {
            e.stopPropagation();
            // Don't re-open via input if user is typing
        });

        // Reposition on scroll/resize
        window.addEventListener('scroll', () => { if (this.isOpen) this._reposition(); }, { passive: true });
        window.addEventListener('resize', () => { if (this.isOpen) this._reposition(); }, { passive: true });
    }

    /* ─────────────────────────────────────────────
       HELPERS
    ───────────────────────────────────────────── */
    isDateDisabled(date) {
        if (this.maxDate) {
            const maxEnd = new Date(this.maxDate.getFullYear(), this.maxDate.getMonth(), this.maxDate.getDate(), 23, 59, 59);
            if (date > maxEnd) return true;
        }
        if (this.minDate) {
            const minStart = new Date(this.minDate.getFullYear(), this.minDate.getMonth(), this.minDate.getDate(), 0, 0, 0);
            if (date < minStart) return true;
        }
        return false;
    }

    isSameDay(d1, d2) {
        return d1.getFullYear() === d2.getFullYear() &&
               d1.getMonth()    === d2.getMonth()    &&
               d1.getDate()     === d2.getDate();
    }

    changeMonth(step) {
        this.currentMonth += step;
        if (this.currentMonth > 11) { this.currentMonth = 0;  this.currentYear++; }
        if (this.currentMonth < 0)  { this.currentMonth = 11; this.currentYear--; }
        this.render();
    }

    changeYear(step) {
        this.currentYear += step;
        this.render();
    }

    /** Returns dd-mm-yyyy string for display / input value */
    formatDateDisplay(date) {
        if (!date) return '';
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${d}-${m}-${y}`;
    }

    /** Returns yyyy-mm-dd string for backend / date parsing */
    formatDateISO(date) {
        if (!date) return '';
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    /**
     * Accepts dd-mm-yyyy, yyyy-mm-dd, or Date object.
     * Returns a valid Date or null.
     */
    parseDate(str) {
        if (!str) return null;
        if (str instanceof Date) return isNaN(str.getTime()) ? null : str;

        const s = String(str).trim();

        // dd-mm-yyyy or dd/mm/yyyy
        const dmy = s.match(/^(\d{1,2})[-/](\d{1,2})[-/](\d{4})$/);
        if (dmy) {
            const d = new Date(parseInt(dmy[3], 10), parseInt(dmy[2], 10) - 1, parseInt(dmy[1], 10));
            return isNaN(d.getTime()) ? null : d;
        }

        // yyyy-mm-dd or yyyy/mm/dd
        const ymd = s.match(/^(\d{4})[-/](\d{1,2})[-/](\d{1,2})$/);
        if (ymd) {
            const d = new Date(parseInt(ymd[1], 10), parseInt(ymd[2], 10) - 1, parseInt(ymd[3], 10));
            return isNaN(d.getTime()) ? null : d;
        }

        const d = new Date(s);
        return isNaN(d.getTime()) ? null : d;
    }

    /** Returns the selected date as an ISO string (yyyy-mm-dd) for backend submission */
    getISOValue() {
        return this.selectedDate ? this.formatDateISO(this.selectedDate) : '';
    }
}

window.VanillaDatePicker = VanillaDatePicker;
