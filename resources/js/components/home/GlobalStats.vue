<script setup>
defineProps({ stats: Object });
</script>

<template>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner"><h3>{{ stats.historias }}</h3><p>Historias Clínicas</p></div>
                <div class="icon"><i class="bi bi-file-medical-fill"></i></div>
                <a href="/admin/histories/home" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner"><h3>{{ stats.examenes }}</h3><p>Exámenes</p></div>
                <div class="icon"><i class="bi bi-file-medical-fill"></i></div>
                <a href="/admin/exams/home" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner"><h3>{{ stats.controles }}</h3><p>Controles</p></div>
                <div class="icon"><i class="bi bi-file-medical-fill"></i></div>
                <a href="/admin/appointments/home" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner"><h3>{{ stats.users }}</h3><p>Usuarios</p></div>
                <div class="icon"><i class="bi bi-people"></i></div>
                <a href="/admin/users/home" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
</template>