<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="-4 0 108 82"
        aria-hidden="true"
        :class="className"
        v-bind="$attrs"
    >
        <path
            fill="currentColor"
            d="M50 40C40 12 12 0 4 13C-2 28 20 43 50 42Z"
        />
        <path
            fill="currentColor"
            d="M50 40C60 12 88 0 96 13C102 28 80 43 50 42Z"
        />
        <path
            fill="currentColor"
            d="M50 42C30 44 12 57 19 71C27 80 44 66 50 46Z"
        />
        <path
            fill="currentColor"
            d="M50 42C70 44 88 57 81 71C73 80 56 66 50 46Z"
        />
        <ellipse cx="50" cy="44" rx="3.2" ry="14" fill="currentColor" />
        <circle cx="50" cy="28" r="4.2" fill="currentColor" />
    </svg>
</template>
