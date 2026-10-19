<script setup>
import { onBeforeUnmount } from 'vue';
import { storeToRefs } from 'pinia';
import { useRolesStore } from '@/stores/rolesStore';

const store = useRolesStore();
const { usersModalVisible, usersRoleName, roleUsers, loadingUsers } = storeToRefs(store);

const onBackdropClick = (e) => {
    if (e.target.classList.contains('modal')) {
        store.closeUsersModal();
    }
};

const onKeydown = (e) => {
    if (e.key === 'Escape' && usersModalVisible.value) {
        store.closeUsersModal();
    }
};

if (typeof window !== 'undefined') {
    window.addEventListener('keydown', onKeydown);
}
onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', onKeydown);
    }
});
</script>

<template>
    <Teleport to="body">
        <div
            v-if="usersModalVisible"
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="background: rgba(0, 0, 0, 0.55);"
            @click="onBackdropClick"
        >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title font-weight-bold mb-0">
                            <i class="bi bi-people-fill mr-1"></i>
                            Usuarios del Rol: <span class="badge badge-light text-dark ml-1">{{ usersRoleName }}</span>
                        </h5>
                        <button
                            type="button"
                            class="close text-white"
                            aria-label="Cerrar"
                            @click="store.closeUsersModal()"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-3">
                        <div v-if="loadingUsers" class="text-center py-4 text-muted">
                            <span class="spinner-border spinner-border-sm text-secondary mr-2"></span>
                            Cargando usuarios...
                        </div>

                        <div v-else-if="roleUsers.length === 0" class="text-center py-4 text-muted">
                            <i class="bi bi-person-x" style="font-size: 2rem; opacity: .5;"></i>
                            <p class="mt-2 mb-0 small">Este rol no tiene usuarios asignados actualmente.</p>
                        </div>

                        <div v-else class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 10%;" class="text-center">#</th>
                                        <th style="width: 45%;">Nombre</th>
                                        <th style="width: 45%;">Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, idx) in roleUsers" :key="user.id || idx">
                                        <td class="text-center text-muted small">{{ idx + 1 }}</td>
                                        <td class="font-weight-bold text-dark">{{ user.name }}</td>
                                        <td><code class="text-secondary">{{ user.email }}</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer bg-light justify-content-end">
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            @click="store.closeUsersModal()"
                        >
                            <i class="bi bi-x-circle mr-1"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
