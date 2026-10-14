import { createApp } from 'vue';
import { useQuickViewStore } from '@/stores/quickViewStore';
import QuickViewModal from '@/components/shared/QuickViewModal.vue';

const el = document.getElementById('vue-quickview-modal');
if (el) {
    const app = createApp(QuickViewModal);
    app.use(window.__pinia);   // Pinia compartida
    app.mount(el);

    // Puente global para llamadas desde jQuery legacy (citas.js)
    window.__openQuickView = (historyId) => {
        useQuickViewStore(window.__pinia).open(historyId);
    };
}