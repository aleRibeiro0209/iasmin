<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import GiftListScreen, { type GiftCategoryGroup } from '@/components/iasmin/GiftListScreen.vue';

const ready = ref(false);
const pulling = ref(false);
const settled = ref(false);
const categories = ref<GiftCategoryGroup[]>([]);

const origem = () => (window.location.pathname === '/confirmar' ? 'confirmar' : 'inicio');
const presentesUrl = () => `/presentes?origem=${origem()}`;
const backHref = () => (origem() === 'confirmar' ? '/confirmar' : '/');

onMounted(() => {
    ready.value = true;
    router.prefetch(presentesUrl());
    void loadCategories();
});

onUnmounted(() => {
    document.body.style.overflow = '';
});

const openList = () => {
    if (pulling.value) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        router.visit(presentesUrl(), { showProgress: false });
        return;
    }

    pulling.value = true;
    document.body.style.overflow = 'hidden';
};

const loadCategories = async () => {
    try {
        const response = await fetch('/presentes/dados', {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            return;
        }

        const data = (await response.json()) as { categories?: GiftCategoryGroup[] };
        categories.value = data.categories ?? [];
    } catch {
        categories.value = [];
    }
};

const finished = (event: AnimationEvent) => {
    if (settled.value || event.animationName !== 'iasmin-pull') {
        return;
    }

    settled.value = true;
    router.visit(presentesUrl(), {
        showProgress: false,
        viewTransition: true,
    });
};
</script>

<template>
    <Teleport to="body">
        <div v-if="ready">
            <button
                v-if="!pulling"
                type="button"
                class="fixed top-1/2 right-0 z-40 flex h-14 w-[52px] -translate-y-1/2 items-center justify-center rounded-l-full bg-iasmin-purple text-white shadow-[0_10px_22px_rgba(138,0,196,0.35)]"
                aria-label="Presentes"
                @click="openList"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="8" width="18" height="13" rx="2" />
                    <path d="M3 12h18M12 8v13M12 8c-1.5-2.5-4.5-3.2-6-1.5S7 9 12 8c1.5-2.5 4.5-3.2 6-1.5S17 9 12 8" />
                </svg>
            </button>

            <div
                v-else
                class="iasmin-pull fixed inset-y-0 right-0 z-[90] h-dvh w-screen shadow-[-28px_0_50px_rgba(42,16,56,0.18)]"
                @animationend="finished"
            >
            <span
                class="absolute top-1/2 right-full flex h-14 w-[52px] -translate-y-1/2 items-center justify-center rounded-l-full bg-iasmin-purple text-white shadow-[0_10px_22px_rgba(138,0,196,0.35)]"
                aria-hidden="true"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="8" width="18" height="13" rx="2" />
                    <path d="M3 12h18M12 8v13M12 8c-1.5-2.5-4.5-3.2-6-1.5S7 9 12 8c1.5-2.5 4.5-3.2 6-1.5S17 9 12 8" />
                </svg>
            </span>

            <div class="h-full overflow-y-auto overscroll-contain">
                <GiftListScreen :categories="categories" :back-href="backHref()" @back="settled = true" />
            </div>
            </div>
        </div>
    </Teleport>
</template>
