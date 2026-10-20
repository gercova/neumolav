<script setup>
import axios from 'axios';
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { useCitasStore } from '@/stores/citasStore';
import { useCitasModalsStore } from '@/stores/citasModalsStore';
import { openChangeTipoAtencionModal } from '@/composables/useTipoAtencionModal';

const props     = defineProps({ cita: Object });
const store     = useCitasStore();
const modals    = useCitasModalsStore();
const busy      = ref(false);
const isOpen    = ref(false);
const buttonRef = ref(null);
const menuRef   = ref(null);
const menuStyle = ref({});

const getApi        = () => (window.API_BASE_URL || '/admin').replace(/\/+$/, '');
const emitChange    = () => store.refresh(false);
const updateMenuPosition = () => {
    if (!buttonRef.value) return;
    const rect  = buttonRef.value.getBoundingClientRect();
    const viewH = window.innerHeight;
    const viewW = window.innerWidth;
    const menuW = 224;
    const gap   = 3;

    const menuH         = menuRef.value ? menuRef.value.offsetHeight : 360;
    const spaceBelow    = viewH - rect.bottom;
    const spaceAbove    = rect.top;

    let top;
    if (spaceBelow < menuH + gap && spaceAbove > spaceBelow) {
        top = Math.max(6, rect.top - menuH - gap);
    } else {
        top = Math.min(viewH - menuH - 6, rect.bottom + gap);
    }

    let left = rect.right - menuW;
    if (left < 8) left = 8;
    if (left + menuW > viewW - 8) left = viewW - menuW - 8;

    menuStyle.value = {
        top:    `${top}px`,
        left:   `${left}px`,
        width:  `${menuW}px`,
    };
};

const toggleMenu = async (e) => {
    e.stopPropagation();
    if (busy.value) return;

    if (isOpen.value) {
        isOpen.value = false;
        return;
    }

    window.dispatchEvent(new CustomEvent('close-citas-actions', { detail: props.cita.id }));
    isOpen.value = true;
    await nextTick();
    updateMenuPosition();
};

const closeMenu     = () => {
    isOpen.value    = false;
};

const handleDocumentClick = (e) => {
    if (!isOpen.value) return;
    if (buttonRef.value && buttonRef.value.contains(e.target)) return;
    if (menuRef.value && menuRef.value.contains(e.target)) return;
    closeMenu();
};

const handleKeydown = (e) => {
    if (e.key === 'Escape' && isOpen.value) closeMenu();
};

const handleScrollOrResize = () => {
    if (isOpen.value) closeMenu();
};

const handleCloseOtherActions = (e) => {
    if (e?.detail !== props.cita.id) {
        closeMenu();
    }
};

onMounted(() => {
    document.addEventListener('click', handleDocumentClick);
    document.addEventListener('keydown', handleKeydown);
    window.addEventListener('scroll', handleScrollOrResize, true);
    window.addEventListener('resize', handleScrollOrResize);
    window.addEventListener('close-citas-actions', handleCloseOtherActions);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
    document.removeEventListener('keydown', handleKeydown);
    window.removeEventListener('scroll', handleScrollOrResize, true);
    window.removeEventListener('resize', handleScrollOrResize);
    window.removeEventListener('close-citas-actions', handleCloseOtherActions);
});

const changeStatus = async (statusId) => {
    closeMenu();
    busy.value = true;
    try {
        const token = window.token || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await axios.post(`${getApi()}/citas/${props.cita.id}/status`, { id_estado: statusId, _token: token });
        window.alertNotify?.('success', response.data?.messages || 'Estado actualizado');
        emitChange();
    } catch (e) {
        const msg = e.response?.data?.messages || 'No se pudo cambiar el estado.';
        window.Swal?.fire('Error', msg, 'error');
    } finally {
        busy.value = false;
    }
};

const reschedule = () => {
    closeMenu();
    modals.openReschedule(props.cita);
};

const changeTipo = () => {
    closeMenu();
    openChangeTipoAtencionModal(props.cita, () => store.refresh(false));
};

const remove = async () => {
    closeMenu();
    const r = await window.Swal.fire({
        title: '¿Eliminar cita?',
        text: `¿Está seguro de eliminar la cita de ${props.cita.nombres || 'este paciente'}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
    });
    if (!r.isConfirmed) return;
    try {
        const token = window.token || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        await axios.delete(`${getApi()}/citas/${props.cita.id}`, { data: { _token: token } });
        window.alertNotify?.('success', 'Cita eliminada');
        emitChange();
    } catch {
        window.Swal?.fire('Error', 'No se pudo eliminar.', 'error');
    }
};
</script>
<template>
    <div class="d-inline-block position-relative">
        <button 
            ref="buttonRef"
            type="button"
            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center"
            :disabled="busy"
            @click="toggleMenu"
        >
            <i class="bi bi-three-dots-vertical mr-1"></i> Acciones
        </button>

        <Teleport to="body">
            <div v-if="isOpen"
                 ref="menuRef"
                 class="floating-actions-dropdown"
                 :style="menuStyle"
                 @click.stop>
                <a v-if="cita.status_id !== 7" class="dropdown-item py-2 px-3" href="#" @click.prevent="changeStatus(7)">
                    <i class="bi bi-clock text-info mr-2"></i> Pasar a 'En Espera'
                </a>
                <a v-if="cita.status_id !== 6" class="dropdown-item py-2 px-3" href="#" @click.prevent="changeStatus(6)">
                    <i class="bi bi-check2-circle text-success mr-2"></i> Marcar 'Atendido'
                </a>
                <a v-if="cita.status_id !== 1" class="dropdown-item py-2 px-3" href="#" @click.prevent="changeStatus(1)">
                    <i class="bi bi-hourglass-split text-warning mr-2"></i> Marcar 'Pendiente'
                </a>
                <a v-if="cita.status_id !== 2" class="dropdown-item py-2 px-3" href="#" @click.prevent="changeStatus(2)">
                    <i class="bi bi-calendar-check text-primary mr-2"></i> Marcar 'Confirmado'
                </a>
                <a v-if="cita.status_id !== 3" class="dropdown-item text-danger py-2 px-3" href="#" @click.prevent="changeStatus(3)">
                    <i class="bi bi-x-circle text-danger mr-2"></i> Cancelar Cita
                </a>

                <div class="dropdown-divider my-1"></div>

                <a class="dropdown-item py-2 px-3" href="#" @click.prevent="changeTipo">
                    <i class="bi bi-tag-fill text-primary mr-2"></i> Cambiar Tipo de Atención
                </a>
                <a class="dropdown-item py-2 px-3" href="#" @click.prevent="reschedule">
                    <i class="bi bi-arrow-repeat text-warning mr-2"></i> Reagendar Fecha
                </a>

                <div class="dropdown-divider my-1"></div>

                <a v-if="cita.routes?.control_add" class="dropdown-item py-2 px-3" :href="cita.routes.control_add">
                    <i class="bi bi-journal-plus text-primary mr-2"></i> Nuevo Control
                </a>
                <a v-if="cita.routes?.exam_add" class="dropdown-item py-2 px-3" :href="cita.routes.exam_add">
                    <i class="bi bi-file-earmark-medical text-primary mr-2"></i> Nuevo Examen
                </a>
                <a v-if="cita.routes?.report_add" class="dropdown-item py-2 px-3" :href="cita.routes.report_add">
                    <i class="bi bi-file-earmark-text text-primary mr-2"></i> Nuevo Informe
                </a>
                <a v-if="cita.routes?.history_edit" class="dropdown-item py-2 px-3" :href="cita.routes.history_edit">
                    <i class="bi bi-person-lines-fill text-primary mr-2"></i> Editar Historia
                </a>

                <div class="dropdown-divider my-1"></div>

                <a class="dropdown-item text-danger py-2 px-3" href="#" @click.prevent="remove">
                    <i class="bi bi-trash text-danger mr-2"></i> Eliminar Registro
                </a>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
    .floating-actions-dropdown {
        position: fixed;
        z-index: 1060;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.16), 0 3px 8px rgba(0, 0, 0, 0.06);
        padding: 6px 0;
        max-height: calc(100vh - 20px);
        overflow-y: auto;
        font-size: 0.9rem;
        animation: fcm-in 0.12s ease-out;
    }

    @keyframes fcm-in {
        from {
            opacity: 0;
            transform: scale(0.97);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .floating-actions-dropdown .dropdown-item {
        display: flex;
        align-items: center;
        color: #212529;
        text-decoration: none;
        transition: background-color 0.12s ease, color 0.12s ease;
        cursor: pointer;
    }

    .floating-actions-dropdown .dropdown-item:hover {
        background-color: #f3f4f6;
        color: #111827;
    }

    .floating-actions-dropdown .dropdown-item.text-danger {
        color: #dc2626 !important;
    }

    .floating-actions-dropdown .dropdown-item.text-danger:hover {
        background-color: #fef2f2;
        color: #b91c1c !important;
    }

    .floating-actions-dropdown .dropdown-divider {
        height: 0;
        border-top: 1px solid #e5e7eb;
    }
</style>