import { ref, computed, watch } from 'vue';

export function useClientPagination(itemsRef, defaultPageSize = 10) {
    const currentPage       = ref(1);
    const pageSize          = ref(defaultPageSize);
    const searchQuery       = ref('');
    const pageSizeOptions   = [10, 25, 50, 100];

    const filteredItems     = computed(() => {
        const list          = (typeof itemsRef === 'function' ? itemsRef() : itemsRef.value) || [];
        if (!searchQuery.value.trim()) {
            return list;
        }
        const q = searchQuery.value.toLowerCase().trim();
        return list.filter((item) =>
            Object.values(item).some((val) =>
                typeof val === 'string' || typeof val === 'number'
                    ? String(val).toLowerCase().includes(q)
                    : false
            )
        );
    });

    const totalRecords      = computed(() => filteredItems.value.length);
    const totalPages        = computed(() => Math.max(1, Math.ceil(totalRecords.value / pageSize.value)));

    const paginatedItems    = computed(() => {
        const start         = (currentPage.value - 1) * pageSize.value;
        return filteredItems.value.slice(start, start + pageSize.value);
    });

    const startRecord = computed(() => {
        if (totalRecords.value === 0) return 0;
        return (currentPage.value - 1) * pageSize.value + 1;
    });

    const endRecord = computed(() => {
        return Math.min(currentPage.value * pageSize.value, totalRecords.value);
    });

    const visiblePages = computed(() => {
        const total = totalPages.value;
        const cur = currentPage.value;
        if (total <= 7) {
            return Array.from({ length: total }, (_, i) => i + 1);
        }
        const pages = [1];
        if (cur > 3) pages.push('…');
        const start = Math.max(2, cur - 1);
        const end = Math.min(total - 1, cur + 1);
        for (let i = start; i <= end; i++) {
            pages.push(i);
        }
        if (cur < total - 2) pages.push('…');
        pages.push(total);
        return pages;
    });

    watch([searchQuery, pageSize], () => {
        currentPage.value = 1;
    });

    const goToPage = (p) => {
        if (typeof p === 'number' && p >= 1 && p <= totalPages.value) {
            currentPage.value = p;
        }
    };

    const prevPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--;
        }
    };

    const nextPage = () => {
        if (currentPage.value < totalPages.value) {
            currentPage.value++;
        }
    };

    return {
        currentPage,
        pageSize,
        pageSizeOptions,
        searchQuery,
        filteredItems,
        totalRecords,
        totalPages,
        paginatedItems,
        startRecord,
        endRecord,
        visiblePages,
        goToPage,
        prevPage,
        nextPage,
    };
}
