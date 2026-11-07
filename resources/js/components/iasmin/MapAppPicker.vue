<script setup lang="ts">
defineProps<{
    apps: { name: string; href: string }[];
}>();

const emit = defineEmits<{
    close: [];
}>();
</script>

<template>
    <Teleport to="body">
        <div
            class="fixed inset-0 z-[80] flex items-end justify-center bg-[#2a1038]/55 p-4 backdrop-blur-[6px] sm:items-center"
            @click="emit('close')"
        >
            <div
                role="dialog"
                aria-modal="true"
                aria-labelledby="map-picker-title"
                class="w-full max-w-sm rounded-[24px] bg-white p-6 shadow-[0_24px_60px_rgba(40,10,70,0.35)]"
                @click.stop
            >
                <div class="mb-5 flex items-start justify-between gap-3">
                    <div>
                        <h3 id="map-picker-title" class="font-script text-[40px] leading-none text-iasmin-purple">Abrir no mapa</h3>
                        <p class="mt-1 text-sm text-iasmin-mauve">O ponto é o mesmo em qualquer app</p>
                    </div>
                    <button
                        type="button"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-iasmin-petal text-2xl leading-none text-iasmin-purple"
                        aria-label="Fechar"
                        @click="emit('close')"
                    >
                        ×
                    </button>
                </div>
                <div class="flex flex-col gap-2.5">
                    <a
                        v-for="app in apps"
                        :key="app.name"
                        :href="app.href"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex h-12 items-center justify-between rounded-full border border-iasmin-lilac px-5 text-[15px] font-semibold text-iasmin-ink no-underline transition-colors hover:bg-iasmin-petal"
                        @click="emit('close')"
                    >
                        {{ app.name }}
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </div>
    </Teleport>
</template>
