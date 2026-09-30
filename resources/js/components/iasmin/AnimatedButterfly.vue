<script setup lang="ts">
import type { CSSProperties } from 'vue';
import { computed } from 'vue';
import ButterflySvg from '@/components/iasmin/ButterflySvg.vue';

type Variant = 'purple' | 'lilac' | 'mid' | 'blue';

// Três camadas (spec):
//  1. .voa   → trajeto de voo (vooN) + duração + delay negativo
//  2. meio   → rotação fixa da borboleta
//  3. .asa   → bater de asas (velocidade própria) contendo o SVG
const props = withDefaults(
    defineProps<{
        variant?: Variant;
        voo?: 1 | 2 | 3 | 4 | 5;
        duration?: number; // segundos (14–24)
        delay?: number; // segundos, aplicado como negativo
        beat?: number; // segundos (0.24–0.39)
        rotate?: number; // inclinação fixa em graus
    }>(),
    { variant: 'purple', voo: 1, duration: 18, delay: 0, beat: 0.3, rotate: 0 },
);

const flyStyle = computed<CSSProperties>(() => ({
    animationName: `voo${props.voo}`,
    animationDuration: `${props.duration}s`,
    animationDelay: `-${props.delay}s`,
}));

const tiltStyle = computed<CSSProperties>(() => ({
    width: '100%',
    height: '100%',
    transform: `rotate(${props.rotate}deg)`,
}));

const beatStyle = computed<CSSProperties>(() => ({
    animationDuration: `${props.beat}s`,
}));
</script>

<template>
    <div class="voa" :style="flyStyle" aria-hidden="true">
        <div :style="tiltStyle">
            <div class="asa" :style="beatStyle">
                <ButterflySvg :variant="variant" class="h-full w-full" />
            </div>
        </div>
    </div>
</template>
