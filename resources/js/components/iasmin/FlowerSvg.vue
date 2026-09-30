<script setup lang="ts">
import { computed, useId } from 'vue';

type Variant = 'purple' | 'lilac' | 'mid' | 'blue';

const props = withDefaults(defineProps<{ variant?: Variant }>(), {
    variant: 'purple',
});

// Cada variante define: d (traço escuro), m (miolo médio), l (gradiente claro),
// inner (pétalas internas) e ctr (centro).
const palettes: Record<Variant, { d: string; m: string; l: string; inner: string; ctr: string }> = {
    purple: { d: '#8A00C4', m: '#9D4EDD', l: '#E0AAFF', inner: '#C77DFF', ctr: '#F6ECFF' },
    lilac: { d: '#9D4EDD', m: '#C77DFF', l: '#F4E6FF', inner: '#E0AAFF', ctr: '#FFFFFF' },
    mid: { d: '#8A00C4', m: '#C77DFF', l: '#F9F1FF', inner: '#9D4EDD', ctr: '#E0AAFF' },
    blue: { d: '#007FFF', m: '#0CB7F2', l: '#E6F7FF', inner: '#53D4FF', ctr: '#FFFFFF' },
};

const c = computed(() => palettes[props.variant]);
const gid = `flower-${useId()}`;

const petalRotations = [0, 45, 90, 135, 180, 225, 270, 315];
const innerRotations = [22.5, 67.5, 112.5, 157.5, 202.5, 247.5, 292.5, 337.5];
const dotPositions = [
    [100, 80], [114, 86], [120, 100], [114, 114],
    [100, 120], [86, 114], [80, 100], [86, 86],
];
</script>

<template>
    <svg
        viewBox="0 0 200 200"
        aria-hidden="true"
        class="overflow-visible drop-shadow-[0_6px_10px_rgba(90,30,140,0.18)]"
    >
        <defs>
            <radialGradient :id="gid" gradientUnits="userSpaceOnUse" cx="100" cy="100" r="100">
                <stop offset="0" :stop-color="c.d" />
                <stop offset="0.5" :stop-color="c.m" />
                <stop offset="1" :stop-color="c.l" />
            </radialGradient>
        </defs>

        <!-- Folhas -->
        <g fill="#9DCC9B" stroke="#6FA56C" stroke-width="1">
            <ellipse cx="100" cy="20" rx="15" ry="28" transform="rotate(22 100 100)" />
            <ellipse cx="100" cy="20" rx="13" ry="26" transform="rotate(158 100 100)" />
            <ellipse cx="100" cy="20" rx="14" ry="27" transform="rotate(250 100 100)" />
        </g>

        <!-- Pétalas externas -->
        <g :fill="`url(#${gid})`" :stroke="c.d" stroke-opacity="0.35" stroke-width="1">
            <ellipse
                v-for="r in petalRotations"
                :key="`p${r}`"
                cx="100"
                cy="56"
                rx="27"
                ry="43"
                :transform="`rotate(${r} 100 100)`"
            />
        </g>

        <!-- Nervuras -->
        <g stroke="#ffffff" stroke-opacity="0.4" stroke-width="1.4" stroke-linecap="round">
            <path
                v-for="r in petalRotations"
                :key="`v${r}`"
                d="M100 88L100 22"
                :transform="`rotate(${r} 100 100)`"
            />
        </g>

        <!-- Pétalas internas -->
        <g :fill="c.inner" :stroke="c.d" stroke-opacity="0.3" stroke-width="1">
            <ellipse
                v-for="r in innerRotations"
                :key="`i${r}`"
                cx="100"
                cy="74"
                rx="13"
                ry="24"
                :transform="`rotate(${r} 100 100)`"
            />
        </g>

        <!-- Centro -->
        <circle cx="100" cy="100" r="16" :fill="c.ctr" />
        <g :fill="c.d">
            <circle v-for="(d, i) in dotPositions" :key="`d${i}`" :cx="d[0]" :cy="d[1]" r="2.6" />
        </g>
        <circle cx="100" cy="100" r="6" :fill="c.m" />
    </svg>
</template>
