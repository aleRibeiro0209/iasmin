<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import { SidebarProvider } from '@/components/ui/sidebar';
import { TooltipProvider } from '@/components/ui/tooltip';
import type { AppVariant } from '@/types';

type Props = {
    variant?: AppVariant;
};

withDefaults(defineProps<Props>(), {
    variant: 'sidebar',
});

const isOpen = usePage().props.sidebarOpen;

onMounted(() => {
    document.documentElement.classList.add('admin-panel');
});

onUnmounted(() => {
    document.documentElement.classList.remove('admin-panel');
});
</script>

<template>
    <TooltipProvider v-if="variant === 'header'" :delay-duration="300">
        <div class="flex min-h-screen w-full flex-col">
            <slot />
        </div>
    </TooltipProvider>
    <SidebarProvider v-else :default-open="isOpen">
        <slot />
    </SidebarProvider>
</template>
