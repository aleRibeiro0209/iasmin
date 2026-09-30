<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Celebration from '@/components/iasmin/Celebration.vue';
import GiftListScreen, { type GiftCategoryGroup } from '@/components/iasmin/GiftListScreen.vue';

defineProps<{
    categories: GiftCategoryGroup[];
    celebration?: boolean;
    backHref?: string;
}>();
</script>

<template>
    <Head title="Iasmin 15 anos – Lista de presentes" />

    <GiftListScreen :categories="categories" :back-href="backHref" />
    <Celebration v-if="celebration" />
</template>
