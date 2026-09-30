<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import FlowerSvg from '@/components/iasmin/FlowerSvg.vue';
import AnimatedButterfly from '@/components/iasmin/AnimatedButterfly.vue';
import IasminDecor, { type DecorItem } from '@/components/iasmin/IasminDecor.vue';

export interface GiftCategoryGroup {
    id: number;
    name: string;
    items: { id: number; name: string }[];
}

withDefaults(
    defineProps<{
        categories: GiftCategoryGroup[];
        backHref?: string;
    }>(),
    {
        backHref: '/',
    },
);

const emit = defineEmits<{
    back: [];
}>();

const pageDecor: DecorItem[] = [
    { t: 'b', x: 150, y: 78, s: 70, r: -6, v: 'mid' },
    { t: 'b', x: 28, y: 210, s: 36, r: -14, v: 'lilac' },
    { t: 'b', x: 330, y: 240, s: 40, r: 14, v: 'blue' },
    { t: 'b', x: 20, y: 520, s: 34, r: -10, v: 'purple' },
    { t: 'f', x: -84, y: 20, s: 200, r: 8, v: 'purple' },
    { t: 'f', x: 250, y: 10, s: 200, r: -14, v: 'purple' },
    { t: 'f', x: 86, y: 110, s: 80, r: -18, v: 'lilac' },
    { t: 'f', x: -60, y: 820, s: 190, r: 6, v: 'purple' },
    { t: 'f', x: 100, y: 860, s: 140, r: -12, v: 'lilac' },
    { t: 'f', x: 210, y: 840, s: 160, r: 16, v: 'mid' },
    { t: 'f', x: 300, y: 810, s: 180, r: -6, v: 'purple' },
];
</script>

<template>
    <div class="iasmin-gift-screen relative min-h-screen w-full overflow-hidden bg-white font-body text-iasmin-ink">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white via-iasmin-cloud to-iasmin-blossom" />

        <IasminDecor :items="pageDecor" :frame-h="900" />
        <FlowerSvg variant="purple" class="pointer-events-none absolute -left-[70px] -top-[20px] hidden h-[190px] w-[190px] rotate-[8deg] lg:z-10 lg:block lg:h-[260px] lg:w-[260px]" />
        <FlowerSvg variant="lilac" class="pointer-events-none absolute -right-[30px] top-[10px] hidden h-[150px] w-[150px] -rotate-[14deg] lg:z-10 lg:block lg:h-[220px] lg:w-[220px]" />
        <AnimatedButterfly variant="mid" :rotate="-6" :voo="1" :duration="19" :delay="0" :beat="0.3" class="hidden h-[60px] w-[74px] lg:left-[12%] lg:top-[90px] lg:z-10 lg:block" />

        <header class="sticky top-0 z-50 w-full border-b border-iasmin-veil bg-white/[0.86] backdrop-blur-[10px]">
            <div class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between pr-4 pl-5 lg:px-8">
                <Link href="/" class="flex items-baseline gap-1.5">
                    <span class="font-script text-[30px] leading-none text-iasmin-purple">Iasmin</span>
                    <span class="font-display text-[18px] font-bold text-iasmin-violet">15 anos</span>
                </Link>
                <Link
                    :href="backHref"
                    class="flex h-11 items-center rounded-full border-[1.5px] border-iasmin-purple px-[18px] text-sm font-semibold text-iasmin-purple transition-colors hover:bg-iasmin-purple hover:text-white"
                    @click="emit('back')"
                >
                    Voltar
                </Link>
            </div>
        </header>

        <div class="relative z-20 mx-auto flex w-full max-w-3xl flex-col items-center gap-8 px-4 pt-28 pb-[220px] text-center lg:px-6 lg:pt-36">
            <div class="flex flex-col items-center gap-2">
                <h1 class="font-script text-[52px] leading-none text-iasmin-purple lg:text-7xl">Lista de presentes</h1>
                <p class="max-w-[320px] text-[15px] leading-relaxed text-iasmin-ink lg:max-w-md">
                    Se quiser presentear a Iasmin, estes são os itens que ela vai amar ganhar.
                </p>
            </div>

            <div v-if="categories.length" class="flex w-full max-w-[440px] flex-col gap-8 text-left">
                <section v-for="category in categories" :key="category.id" class="flex flex-col gap-3">
                    <h2 class="text-center font-script text-[40px] leading-none text-iasmin-purple">{{ category.name }}</h2>
                    <ul class="flex flex-col gap-3">
                        <li
                            v-for="gift in category.items"
                            :key="gift.id"
                            class="rounded-[22px] border border-iasmin-lilac bg-white px-5 py-4 text-base font-semibold text-iasmin-ink shadow-[0_8px_20px_rgba(90,30,140,0.08)]"
                        >
                            {{ gift.name }}
                        </li>
                    </ul>
                </section>
            </div>
            <p v-else class="max-w-[280px] text-sm text-iasmin-mauve">A lista ainda está sendo preparada com carinho.</p>
        </div>
    </div>
</template>
