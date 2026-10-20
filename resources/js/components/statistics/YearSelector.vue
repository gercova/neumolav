<script setup>
import { storeToRefs } from 'pinia';
import { useStatisticsStore } from '@/stores/statisticsStore';

const store = useStatisticsStore();
const { years, selectedYear, loading } = storeToRefs(store);

const onChange = (e) => store.fetchAnnual(e.target.value);
</script>

<template>
    <select
        class="custom-select custom-select-sm form-control form-control-sm"
        style="width: 95px; height: 31px;"
        :value="selectedYear"
        @change="onChange"
        :disabled="loading.annual"
    >
        <option v-for="y in years" :key="y.year" :value="y.year">{{ y.year }}</option>
    </select>
</template>