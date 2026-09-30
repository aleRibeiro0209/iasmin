<script setup lang="ts">
import { computed, useId } from 'vue';

type Variant = 'purple' | 'lilac' | 'mid' | 'blue';

const props = withDefaults(defineProps<{ variant?: Variant }>(), {
    variant: 'purple',
});

// c1/c2: gradiente das asas (topo → base); e: cor da borda e detalhes.
const palettes: Record<Variant, { c1: string; c2: string; e: string }> = {
    lilac: { c1: '#FBF4FF', c2: '#E0AAFF', e: '#B066E8' },
    purple: { c1: '#E0AAFF', c2: '#8A00C4', e: '#6A0099' },
    mid: { c1: '#F3E6FF', c2: '#9D4EDD', e: '#8A00C4' },
    blue: { c1: '#E6F7FF', c2: '#53D4FF', e: '#007FFF' },
};

const c = computed(() => palettes[props.variant]);
const gid = `butterfly-${useId()}`;
</script>

<template>
    <svg
        viewBox="0 0 100 80"
        aria-hidden="true"
        class="overflow-visible drop-shadow-[0_4px_5px_rgba(90,30,140,0.2)]"
    >
        <defs>
            <linearGradient :id="gid" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" :stop-color="c.c1" />
                <stop offset="1" :stop-color="c.c2" />
            </linearGradient>
        </defs>

        <g :fill="`url(#${gid})`" :stroke="c.e" stroke-width="1.6" stroke-linejoin="round">
            <path d="M50 40C40 12 12 0 4 13C-2 28 20 43 50 42Z" />
            <path d="M50 40C60 12 88 0 96 13C102 28 80 43 50 42Z" />
            <path d="M50 42C30 44 12 57 19 71C27 80 44 66 50 46Z" />
            <path d="M50 42C70 44 88 57 81 71C73 80 56 66 50 46Z" />
        </g>
        <g fill="#ffffff" fill-opacity="0.45">
            <path d="M49 39C42 21 23 12 15 19C10 27 26 37 49 40Z" />
            <path d="M51 39C58 21 77 12 85 19C90 27 74 37 51 40Z" />
            <path d="M49 44C37 48 27 57 30 65C35 69 45 60 49 46Z" />
            <path d="M51 44C63 48 73 57 70 65C65 69 55 60 51 46Z" />
        </g>
        <g :fill="c.e" fill-opacity="0.55">
            <circle cx="24" cy="14" r="3" />
            <circle cx="76" cy="14" r="3" />
            <circle cx="30" cy="62" r="2.2" />
            <circle cx="70" cy="62" r="2.2" />
        </g>
        <g fill="#ffffff">
            <circle cx="8" cy="16" r="1.6" />
            <circle cx="7" cy="24" r="1.4" />
            <circle cx="13" cy="31" r="1.3" />
            <circle cx="92" cy="16" r="1.6" />
            <circle cx="93" cy="24" r="1.4" />
            <circle cx="87" cy="31" r="1.3" />
            <circle cx="21" cy="69" r="1.3" />
            <circle cx="79" cy="69" r="1.3" />
        </g>
        <ellipse cx="50" cy="45" rx="2.3" ry="13" fill="#3B1656" />
        <circle cx="50" cy="31" r="3" fill="#3B1656" />
        <g fill="none" stroke="#3B1656" stroke-width="1.2" stroke-linecap="round">
            <path d="M49 29C46 20 42 15 37 13" />
            <path d="M51 29C54 20 58 15 63 13" />
        </g>
        <circle cx="37" cy="13" r="1.6" fill="#3B1656" />
        <circle cx="63" cy="13" r="1.6" fill="#3B1656" />
    </svg>
</template>
