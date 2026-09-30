<script setup lang="ts">
import { onMounted, ref } from 'vue';

export interface PolaroidPhoto {
    caption: string;
    gradient: string;
    stroke: string;
    label: string;
    tapeClass: string;
    src?: string;
    focus?: string;
}

defineProps<{
    photo: PolaroidPhoto;
}>();

const emit = defineEmits<{
    close: [];
}>();

const ready = ref(false);

onMounted(() => {
    ready.value = true;
});
</script>

<template>
    <Teleport to="body">
        <div v-if="ready">
            <div
                class="fixed inset-0 z-[80] flex items-center justify-center bg-[#2a1038]/55 p-4 backdrop-blur-[6px]"
                @click="emit('close')"
            >
                <button
                    type="button"
                    class="absolute top-4 right-4 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-2xl leading-none text-iasmin-purple"
                    aria-label="Fechar foto"
                    @click="emit('close')"
                >
                    ×
                </button>
                <div
                    class="relative w-[min(78vw,52dvh,420px)] -rotate-2 bg-white p-3 pb-16 shadow-[0_24px_60px_rgba(40,10,70,0.4)]"
                    @click.stop
                >
                    <div class="absolute top-[-18px] left-1/2 h-8 w-24 -translate-x-1/2" :class="photo.tapeClass" />
                    <img
                        v-if="photo.src"
                        :src="photo.src"
                        :alt="photo.label"
                        class="aspect-square w-full object-cover"
                        :class="photo.focus ?? 'object-[center_22%]'"
                    />
                    <div v-else class="flex aspect-square w-full flex-col items-center justify-center gap-3 bg-gradient-to-br" :class="photo.gradient">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" :stroke="photo.stroke" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 8h3l2-2.5h6L17 8h3v11H4z" />
                            <circle cx="12" cy="13" r="3.6" />
                        </svg>
                        <span class="text-sm tracking-[1px] text-iasmin-plum">{{ photo.label }}</span>
                    </div>
                    <span class="absolute inset-x-0 bottom-4 text-center font-script text-4xl text-iasmin-purple-deep">{{ photo.caption }}</span>
                </div>
            </div>
        </div>
    </Teleport>
</template>
