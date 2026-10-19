<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps({
    modelValue:        { type: [String, Number, Object], default: '' },
    placeholder:       { type: String, default: '-- Buscar --' },
    searchPlaceholder: { type: String, default: 'Escriba para buscar...' },
    searchFn:          { type: Function, required: true }, // async (query) => [{ id, value, label }]
    minChars:          { type: Number, default: 2 },
    maxResults:        { type: Number, default: 5 },
    initialLabel:      { type: String, default: '' },
    disabled:          { type: Boolean, default: false },
    clearable:         { type: Boolean, default: true },
});

const emit = defineEmits(['update:modelValue', 'selected', 'clear']);

const query            = ref('');
const results          = ref([]);
const loading          = ref(false);
const open             = ref(false);
const isFocused        = ref(false);
const highlightedIndex = ref(-1);
const displayLabel     = ref(props.initialLabel || (typeof props.modelValue === 'string' && props.modelValue ? props.modelValue : ''));

const rootRef          = ref(null);
const triggerRef       = ref(null);
const searchInputRef   = ref(null);
const resultsListRef   = ref(null);

let timer = null;
let searchRequestId = 0;

const hasValue = computed(() => Boolean(displayLabel.value || props.modelValue));

const displayedResults = computed(() => {
    return (results.value || []).slice(0, props.maxResults);
});

watch(() => props.initialLabel, (v) => {
    if (v) displayLabel.value = v;
});

watch(() => props.modelValue, (v) => {
    if (!v) {
        displayLabel.value = '';
    } else if (typeof v === 'string') {
        displayLabel.value = props.initialLabel || v;
    }
});

const isItemSelected = (item) => {
    if (!props.modelValue) return false;
    const currentVal = String(item.value ?? item.id ?? '');
    return currentVal === String(props.modelValue);
};

const runSearch = async () => {
    const term = query.value.trim();
    if (term.length < props.minChars) {
        results.value = [];
        highlightedIndex.value = -1;
        return;
    }

    const currentRequestId = ++searchRequestId;
    loading.value = true;
    try {
        const data = await props.searchFn(term);
        if (currentRequestId !== searchRequestId) return;
        results.value = Array.isArray(data) ? data.slice(0, props.maxResults) : [];
        highlightedIndex.value = results.value.length > 0 ? 0 : -1;
    } catch {
        if (currentRequestId !== searchRequestId) return;
        results.value = [];
        highlightedIndex.value = -1;
    } finally {
        if (currentRequestId === searchRequestId) {
            loading.value = false;
        }
    }
};

watch(query, () => {
    clearTimeout(timer);
    timer = setTimeout(runSearch, 250);
});

const openDropdown = () => {
    if (props.disabled) return;
    open.value = true;
    nextTick(() => {
        searchInputRef.value?.focus();
        if (query.value.trim().length >= props.minChars && !results.value.length) {
            runSearch();
        }
    });
};

const closeDropdown = () => {
    open.value = false;
    highlightedIndex.value = -1;
    triggerRef.value?.focus();
};

const toggleDropdown = () => {
    if (props.disabled) return;
    if (open.value) {
        closeDropdown();
    } else {
        openDropdown();
    }
};

const select = (item) => {
    const val = item.value ?? item.id;
    const lbl = item.label ?? item.text ?? item.descripcion ?? String(val);

    emit('update:modelValue', val);
    emit('selected', {
        id: val,
        value: val,
        label: lbl,
        ...item,
    });

    displayLabel.value = lbl;
    query.value = '';
    results.value = [];
    open.value = false;
    highlightedIndex.value = -1;
};

const clear = () => {
    emit('update:modelValue', '');
    emit('clear');
    displayLabel.value = '';
    query.value = '';
    results.value = [];
    open.value = false;
    highlightedIndex.value = -1;
};

const onKeyDown = () => {
    if (!displayedResults.value.length) return;
    if (highlightedIndex.value < displayedResults.value.length - 1) {
        highlightedIndex.value++;
    } else {
        highlightedIndex.value = 0;
    }
    scrollHighlightedIntoView();
};

const onKeyUp = () => {
    if (!displayedResults.value.length) return;
    if (highlightedIndex.value > 0) {
        highlightedIndex.value--;
    } else {
        highlightedIndex.value = displayedResults.value.length - 1;
    }
    scrollHighlightedIntoView();
};

const onKeyEnter = () => {
    if (highlightedIndex.value >= 0 && displayedResults.value[highlightedIndex.value]) {
        select(displayedResults.value[highlightedIndex.value]);
    } else if (displayedResults.value.length > 0) {
        select(displayedResults.value[0]);
    }
};

const scrollHighlightedIntoView = () => {
    nextTick(() => {
        if (!resultsListRef.value) return;
        const activeEl = resultsListRef.value.querySelector('.is-highlighted');
        if (activeEl) {
            activeEl.scrollIntoView({ block: 'nearest' });
        }
    });
};

const escapeHtml = (str) => {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
};

const highlightMatch = (text, q) => {
    if (!text) return '';
    const cleanText = String(text);
    if (!q || !q.trim()) return escapeHtml(cleanText);

    const escapedQuery = q.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp(`(${escapedQuery})`, 'gi');
    const parts = cleanText.split(regex);

    return parts.map(part => {
        if (part.toLowerCase() === q.trim().toLowerCase()) {
            return `<strong class="select2-match-highlight">${escapeHtml(part)}</strong>`;
        }
        return escapeHtml(part);
    }).join('');
};

const onClickOutside = (e) => {
    if (rootRef.value && !rootRef.value.contains(e.target)) {
        open.value = false;
        highlightedIndex.value = -1;
    }
};

onMounted(() => {
    document.addEventListener('click', onClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', onClickOutside);
    clearTimeout(timer);
});
</script>

<template>
    <div ref="rootRef" class="select2-custom-container" :class="{ 'is-open': open, 'is-disabled': disabled }">
        <!-- Trigger Box (Selection container) -->
        <div
            ref="triggerRef"
            class="select2-custom-trigger"
            :class="{
                'has-value': hasValue,
                'is-focused': isFocused || open,
                'is-disabled': disabled
            }"
            tabindex="0"
            role="combobox"
            :aria-expanded="open ? 'true' : 'false'"
            aria-haspopup="listbox"
            @click="toggleDropdown"
            @keydown.down.prevent="openDropdown"
            @keydown.up.prevent="openDropdown"
            @keydown.enter.prevent="openDropdown"
            @keydown.space.prevent="openDropdown"
            @focus="isFocused = true"
            @blur="isFocused = false"
        >
            <span class="select2-custom-rendered" :class="{ 'is-placeholder': !hasValue }">
                {{ displayLabel || placeholder }}
            </span>

            <div class="select2-custom-controls">
                <button
                    v-if="clearable && hasValue && !disabled"
                    type="button"
                    class="select2-custom-clear-btn"
                    title="Limpiar"
                    @click.stop="clear"
                >
                    &times;
                </button>
                <span class="select2-custom-arrow" role="presentation">
                    <i class="bi bi-chevron-down" :class="{ 'rotate-arrow': open }"></i>
                </span>
            </div>
        </div>

        <!-- Dropdown Menu -->
        <div
            v-if="open"
            class="select2-custom-dropdown"
            role="listbox"
        >
            <!-- Dropdown Search Box (authentic Select2 header) -->
            <div class="select2-custom-search-header">
                <div class="select2-custom-search-box">
                    <i class="bi bi-search select2-custom-search-icon"></i>
                    <input
                        ref="searchInputRef"
                        v-model="query"
                        type="search"
                        class="select2-custom-search-field"
                        :placeholder="searchPlaceholder"
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="off"
                        spellcheck="false"
                        @keydown.down.prevent="onKeyDown"
                        @keydown.up.prevent="onKeyUp"
                        @keydown.enter.prevent="onKeyEnter"
                        @keydown.esc.prevent="closeDropdown"
                    />
                    <span v-if="loading" class="spinner-border spinner-border-sm text-primary select2-custom-search-spinner" role="status"></span>
                </div>
            </div>

            <!-- Results List -->
            <ul ref="resultsListRef" class="select2-custom-results-list">
                <!-- Loading State -->
                <li v-if="loading" class="select2-custom-status-item">
                    <span class="spinner-border spinner-border-sm text-primary mr-1" role="status"></span>
                    <span>Buscando resultados...</span>
                </li>

                <!-- Min chars required prompt -->
                <li v-else-if="query.trim().length < minChars" class="select2-custom-status-item text-muted">
                    <i class="bi bi-keyboard mr-1"></i>
                    <span>Ingrese al menos {{ minChars }} caracteres para buscar</span>
                </li>

                <!-- Empty / No results -->
                <li v-else-if="!displayedResults.length" class="select2-custom-status-item text-muted">
                    <i class="bi bi-exclamation-circle mr-1"></i>
                    <span>No se encontraron resultados</span>
                </li>

                <!-- Found options (strictly max 5) -->
                <li
                    v-for="(item, idx) in displayedResults"
                    :key="item.value ?? item.id ?? idx"
                    class="select2-custom-option"
                    :class="{
                        'is-highlighted': highlightedIndex === idx,
                        'is-selected': isItemSelected(item)
                    }"
                    role="option"
                    :aria-selected="isItemSelected(item)"
                    @mouseenter="highlightedIndex = idx"
                    @click="select(item)"
                >
                    <div class="select2-custom-option-text text-truncate">
                        <span v-html="highlightMatch(item.label ?? item.text ?? item.descripcion, query)"></span>
                    </div>
                    <span v-if="isItemSelected(item)" class="select2-custom-check">
                        <i class="bi bi-check-lg text-primary"></i>
                    </span>
                </li>
            </ul>

            <!-- Footer badge showing count (max 5) -->
            <div v-if="displayedResults.length" class="select2-custom-footer-info">
                <span>{{ displayedResults.length }} de {{ displayedResults.length }} resultados mostrados (máx. {{ maxResults }})</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.select2-custom-container {
    position: relative;
    width: 100%;
    font-family: inherit;
}

.select2-custom-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    min-height: 38px;
    padding: 0.375rem 0.75rem;
    font-size: 0.95rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #ffffff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    cursor: pointer;
    user-select: none;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.select2-custom-trigger:hover:not(.is-disabled) {
    border-color: #b8c2cc;
}

.select2-custom-trigger.is-focused,
.select2-custom-trigger:focus,
.select2-custom-container.is-open .select2-custom-trigger {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.select2-custom-trigger.is-disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
    opacity: 0.85;
}

.select2-custom-rendered {
    flex: 1 1 auto;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    padding-right: 0.5rem;
    color: #333333;
}

.select2-custom-rendered.is-placeholder {
    color: #6c757d;
}

.select2-custom-controls {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    flex-shrink: 0;
}

.select2-custom-clear-btn {
    border: none;
    background: transparent;
    color: #888888;
    font-size: 1.25rem;
    font-weight: 700;
    line-height: 1;
    padding: 0 0.25rem;
    cursor: pointer;
    border-radius: 50%;
    transition: color 0.15s ease;
}

.select2-custom-clear-btn:hover {
    color: #dc3545;
}

.select2-custom-arrow {
    display: inline-flex;
    align-items: center;
    color: #6c757d;
    font-size: 0.75rem;
    transition: transform 0.2s ease;
}

.rotate-arrow {
    transform: rotate(180deg);
}

/* Dropdown Panel */
.select2-custom-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    margin-top: 2px;
    background: #ffffff;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
    z-index: 1060;
    overflow: hidden;
    animation: select2DropdownFadeIn 0.12s ease-out;
}

@keyframes select2DropdownFadeIn {
    from {
        opacity: 0;
        transform: translateY(-4px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Search Header */
.select2-custom-search-header {
    padding: 0.5rem;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.select2-custom-search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.select2-custom-search-icon {
    position: absolute;
    left: 0.6rem;
    color: #6c757d;
    font-size: 0.85rem;
    pointer-events: none;
}

.select2-custom-search-field {
    width: 100%;
    height: 32px;
    padding: 0.25rem 2rem 0.25rem 2rem;
    font-size: 0.875rem;
    color: #495057;
    background-color: #ffffff;
    border: 1px solid #ced4da;
    border-radius: 0.2rem;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.select2-custom-search-field:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.25);
}

.select2-custom-search-spinner {
    position: absolute;
    right: 0.6rem;
}

/* Results */
.select2-custom-results-list {
    list-style: none;
    margin: 0;
    padding: 0.25rem 0;
    max-height: 220px;
    overflow-y: auto;
}

.select2-custom-status-item {
    padding: 0.75rem 1rem;
    text-align: center;
    font-size: 0.85rem;
}

.select2-custom-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.85rem;
    font-size: 0.9rem;
    color: #212529;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.1s ease, color 0.1s ease;
}

.select2-custom-option:last-child {
    border-bottom: none;
}

.select2-custom-option.is-highlighted {
    background-color: #007bff;
    color: #ffffff !important;
}

.select2-custom-option.is-highlighted :deep(.select2-match-highlight) {
    background-color: #ffc107;
    color: #212529;
    padding: 0 2px;
    border-radius: 2px;
}

.select2-custom-option:not(.is-highlighted) :deep(.select2-match-highlight) {
    color: #007bff;
    font-weight: 700;
}

.select2-custom-option.is-selected {
    font-weight: 600;
    background-color: #f0f7ff;
}

.select2-custom-option.is-selected.is-highlighted {
    background-color: #0069d9;
    color: #ffffff;
}

.select2-custom-option-text {
    flex: 1 1 auto;
    line-height: 1.35;
}

.select2-custom-check {
    margin-left: 0.5rem;
    font-size: 1rem;
}

.select2-custom-option.is-highlighted .select2-custom-check i {
    color: #ffffff !important;
}

/* Footer Info */
.select2-custom-footer-info {
    padding: 0.25rem 0.75rem;
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
    font-size: 0.75rem;
    color: #6c757d;
    text-align: right;
}
</style>