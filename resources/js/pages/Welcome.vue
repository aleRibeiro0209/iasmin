<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import FlowerSvg from '@/components/iasmin/FlowerSvg.vue';
import AnimatedButterfly from '@/components/iasmin/AnimatedButterfly.vue';
import IasminDecor, { type DecorItem } from '@/components/iasmin/IasminDecor.vue';
import GiftListButton from '@/components/iasmin/GiftListButton.vue';
import PhotoLightbox from '@/components/iasmin/PhotoLightbox.vue';

// Decoração mobile fiel ao HTML de referência (frame 390px de largura).
// Coordenadas relativas ao topo de cada seção; alturas do reference:
// hero 1024 · data 640 · momentos 640 · info 740 · confirmação 500.
const heroDecor: DecorItem[] = [
    { t: 'b', x: 150, y: 78, s: 92, r: -6, v: 'mid' },
    { t: 'b', x: 64, y: 176, s: 28, r: 20, v: 'blue' },
    { t: 'b', x: 22, y: 300, s: 50, r: -18, v: 'purple' },
    { t: 'b', x: 322, y: 322, s: 44, r: 14, v: 'blue' },
    { t: 'b', x: 234, y: 408, s: 44, r: 16, v: 'lilac' },
    { t: 'b', x: 334, y: 640, s: 48, r: 18, v: 'purple' },
    { t: 'b', x: 22, y: 880, s: 40, r: -10, v: 'lilac' },
    { t: 'b', x: 312, y: 950, s: 34, r: -8, v: 'blue' },
    { t: 'f', x: -84, y: 30, s: 220, r: 8, v: 'purple' },
    { t: 'f', x: 78, y: 120, s: 92, r: -22, v: 'lilac' },
    { t: 'f', x: 254, y: 20, s: 220, r: -14, v: 'purple' },
    { t: 'f', x: 236, y: 138, s: 84, r: 30, v: 'lilac' },
    { t: 'f', x: -48, y: 650, s: 110, r: 12, v: 'mid' },
    { t: 'f', x: 328, y: 780, s: 96, r: -10, v: 'blue' },
];
const dataDecor: DecorItem[] = [
    { t: 'b', x: 26, y: 56, s: 38, r: -12, v: 'purple' },
    { t: 'b', x: 330, y: 396, s: 42, r: 12, v: 'blue' },
    { t: 'f', x: 318, y: -20, s: 120, r: 0, v: 'lilac' },
    { t: 'f', x: -52, y: 536, s: 130, r: 20, v: 'purple' },
];
const momentosDecor: DecorItem[] = [
    { t: 'b', x: 175, y: 22, s: 34, r: -8, v: 'lilac' },
    { t: 'b', x: 334, y: 346, s: 42, r: 15, v: 'purple' },
    { t: 'b', x: 10, y: 572, s: 36, r: -15, v: 'blue' },
    { t: 'f', x: 324, y: 598, s: 110, r: 10, v: 'mid' },
];
const infoDecor: DecorItem[] = [
    { t: 'b', x: 18, y: 22, s: 40, r: -10, v: 'lilac' },
    { t: 'b', x: 176, y: 30, s: 34, r: -8, v: 'purple' },
    { t: 'b', x: 300, y: 210, s: 42, r: 12, v: 'mid' },
    { t: 'b', x: 20, y: 380, s: 36, r: -14, v: 'blue' },
    { t: 'b', x: 332, y: 450, s: 34, r: 10, v: 'purple' },
    { t: 'b', x: 150, y: 610, s: 40, r: -6, v: 'lilac' },
    { t: 'b', x: 310, y: 686, s: 38, r: 12, v: 'blue' },
];
const confirmDecor: DecorItem[] = [
    { t: 'b', x: 30, y: 26, s: 46, r: -14, v: 'purple' },
    { t: 'b', x: 316, y: 52, s: 50, r: 14, v: 'lilac' },
    { t: 'b', x: 18, y: 246, s: 34, r: -8, v: 'blue' },
    { t: 'b', x: 338, y: 256, s: 36, r: 10, v: 'mid' },
    { t: 'f', x: 30, y: 386, s: 90, r: 0, v: 'blue' },
    { t: 'f', x: -70, y: 386, s: 210, r: 6, v: 'purple' },
    { t: 'f', x: 92, y: 436, s: 150, r: -12, v: 'lilac' },
    { t: 'f', x: 196, y: 406, s: 180, r: 18, v: 'mid' },
    { t: 'f', x: 300, y: 376, s: 200, r: -6, v: 'purple' },
];

// Data da festa: 8 de novembro de 2026, 10h.
const eventDate = new Date(2026, 10, 8, 10, 0, 0);
const partyDay = { dd: '08', month: 'Nov', year: '2026', time: '10:00' };
const rsvpDeadline = '1º de novembro de 2026';
const partyAddress = 'Rua Araçoiaba da Serra, 10 - Vau Novo - Cajamar, CEP: 07762230';
const partyMapUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent('Rua Araçoiaba da Serra, 10, Vau Novo, Cajamar, SP, 07762-230')}`;

const now = ref(new Date());
let timer: ReturnType<typeof setInterval> | undefined;

type Polaroid = {
    caption: string;
    gradient: string;
    stroke: string;
    label: string;
    tapeClass: string;
    src?: string;
    focus?: string;
};

const heroPhoto: Polaroid = {
    caption: 'nossa borboleta',
    gradient: 'from-iasmin-lilac via-iasmin-petal to-iasmin-mint',
    stroke: '#6A0099',
    label: 'Foto da Iasmin',
    tapeClass: 'bg-iasmin-lilac/75 rotate-[4deg]',
    src: '/images/iasmin.jpg',
};

const openPhoto = ref<Polaroid | null>(null);
const closePhoto = () => {
    openPhoto.value = null;
};

const onKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Escape') closePhoto();
};

onMounted(() => {
    timer = setInterval(() => (now.value = new Date()), 1000);
    window.addEventListener('keydown', onKeydown);
});
onUnmounted(() => {
    clearInterval(timer);
    window.removeEventListener('keydown', onKeydown);
    document.body.style.overflow = '';
});

watch(openPhoto, (photo) => {
    document.body.style.overflow = photo ? 'hidden' : '';
});

const pad = (n: number) => String(n).padStart(2, '0');

const countdown = computed(() => {
    const diff = Math.max(0, eventDate.getTime() - now.value.getTime());
    const totalSeconds = Math.floor(diff / 1000);
    return [
        { value: pad(Math.floor(totalSeconds / 86400)), label: 'dias' },
        { value: pad(Math.floor((totalSeconds % 86400) / 3600)), label: 'horas' },
        { value: pad(Math.floor((totalSeconds % 3600) / 60)), label: 'min' },
        { value: pad(totalSeconds % 60), label: 'seg' },
    ];
});

const photos = [
    { caption: 'pequenininha', gradient: 'from-iasmin-lilac to-iasmin-petal', stroke: '#6A0099', label: 'Foto 1', tapeClass: 'bg-iasmin-mint/90 -rotate-6', rotate: '-rotate-[4deg]', offset: '', src: '/images/pequenininha.jpg', focus: 'object-center' },
    { caption: 'sorrisos', gradient: 'from-iasmin-mint to-iasmin-sky', stroke: '#005FBF', label: 'Foto 2', tapeClass: 'bg-iasmin-lilac/80 rotate-[5deg]', rotate: 'rotate-3', offset: 'mt-[18px]', src: '/images/sorrisos.jpg', focus: 'object-[center_32%]' },
    { caption: 'minha essência', gradient: 'from-iasmin-petal to-iasmin-mint', stroke: '#6A0099', label: 'Foto 3', tapeClass: 'bg-iasmin-sage/70 -rotate-3', rotate: 'rotate-2', offset: '', src: '/images/minha-essencia.jpg', focus: 'object-[center_34%]' },
    { caption: '15 primaveras', gradient: 'from-iasmin-lilac to-iasmin-mint', stroke: '#6A0099', label: 'Foto 4', tapeClass: 'bg-iasmin-lilac/80 rotate-[4deg]', rotate: '-rotate-3', offset: 'mt-[18px]', src: '/images/15-primaveras.jpg', focus: 'object-center' },
];
</script>

<template>
    <Head title="Iasmin 15 anos – Convite" />

    <div class="relative min-h-screen overflow-x-clip bg-white font-body text-iasmin-ink">
        <!-- ================= HEADER ================= -->
        <header
            class="sticky top-0 z-50 w-full border-b border-iasmin-veil bg-white/[0.86] backdrop-blur-[10px]"
        >
            <div class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between pr-4 pl-5 lg:px-8">
                <div class="flex items-baseline gap-1.5">
                    <span class="font-script text-[30px] leading-none text-iasmin-purple">Iasmin</span>
                    <span class="font-display text-[18px] font-bold text-iasmin-violet">15 anos</span>
                </div>
                <Link
                    href="/confirmar"
                    class="flex h-11 items-center rounded-full border-[1.5px] border-iasmin-purple px-[18px] text-sm font-semibold text-iasmin-purple transition-colors hover:bg-iasmin-purple hover:text-white"
                >
                    Confirmar
                </Link>
            </div>
        </header>

        <!-- ================= HERO ================= -->
        <!-- -mt-16 tuca o topo do hero sob o header (as pontas das flores ficam
             cobertas pelo header translúcido, como no reference). -->
        <section class="relative -mt-16 w-full overflow-x-clip bg-gradient-to-b from-white to-iasmin-cloud">
            <div class="pointer-events-none absolute -left-[60px] top-[420px] h-[260px] w-[260px] rounded-full bg-iasmin-lilac/35 blur-[60px]" />
            <div class="pointer-events-none absolute right-[8%] top-[200px] h-[220px] w-[220px] rounded-full bg-iasmin-mint/60 blur-[60px]" />
            <IasminDecor :items="heroDecor" :frame-h="1024" />
            <FlowerSvg variant="purple" class="pointer-events-none absolute -left-[70px] -top-[20px] hidden h-[190px] w-[190px] rotate-[8deg] lg:z-10 lg:block lg:-left-[150px] lg:-top-[36px] lg:h-[260px] lg:w-[260px]" />
            <FlowerSvg variant="lilac" class="pointer-events-none absolute -right-[30px] top-[10px] hidden h-[150px] w-[150px] -rotate-[14deg] lg:z-10 lg:block lg:h-[220px] lg:w-[220px]" />
            <AnimatedButterfly variant="mid" :rotate="-6" :voo="1" :duration="19" :delay="0" :beat="0.3" class="hidden h-[74px] w-[92px] lg:left-[2%] lg:top-[78%] lg:z-10 lg:block" />
            <AnimatedButterfly variant="blue" :rotate="14" :voo="2" :duration="17" :delay="4" :beat="0.27" class="hidden h-[36px] w-[44px] lg:right-[5%] lg:top-[62%] lg:z-10 lg:block" />

            <div class="iasmin-hero relative z-20 mx-auto flex w-full max-w-3xl flex-col items-center gap-4 px-6 pt-[268px] pb-16 text-center">
                <p class="iasmin-hero-eyebrow text-xs font-semibold uppercase tracking-[2.4px] text-iasmin-purple-deep">Você é nosso convidado especial</p>
                <h1 class="iasmin-hero-title font-script text-[104px] leading-none text-iasmin-purple lg:text-[7.25rem]">Iasmin</h1>
                <div class="iasmin-hero-number flex flex-col items-center lg:items-start">
                    <span class="iasmin-hero-digits font-display text-[150px] font-semibold leading-[0.9] tracking-[-4px] text-iasmin-violet lg:text-[10.5rem]">15</span>
                    <span class="iasmin-hero-anos pl-2.5 font-display text-[22px] uppercase tracking-[10px] text-iasmin-mauve lg:pl-1 lg:text-[26px]" aria-label="anos"><span>a</span><span>n</span><span>o</span><span>s</span></span>
                </div>

                <!-- Polaroid -->
                <button
                    type="button"
                    class="iasmin-hero-photo relative box-border w-[220px] -rotate-3 cursor-pointer border-0 bg-white p-2.5 pb-11 text-left shadow-[0_14px_30px_rgba(90,30,140,0.18),0_2px_6px_rgba(90,30,140,0.10)] lg:w-[280px]"
                    aria-label="Abrir foto: nossa borboleta"
                    @click="openPhoto = heroPhoto"
                >
                    <div class="absolute left-[70px] -top-3.5 h-[26px] w-20 rotate-[4deg] bg-iasmin-lilac/75 lg:left-[92px]" />
                    <img
                        src="/images/iasmin.jpg"
                        alt="Iasmin"
                        class="h-[220px] w-[200px] object-cover object-[center_22%] lg:h-[280px] lg:w-[260px]"
                    />
                    <span class="absolute inset-x-0 bottom-2 text-center font-script text-2xl text-iasmin-purple-deep">nossa borboleta</span>
                </button>

                <p class="iasmin-hero-quote max-w-[318px] text-base italic leading-[1.55] text-iasmin-ink lg:max-w-[28rem] lg:text-[17px]">
                    A borboleta mais linda do nosso jardim vai completar 15 anos, e você não pode ficar de fora!
                </p>

                <Link
                    href="/confirmar"
                    class="iasmin-hero-cta flex h-14 w-[300px] items-center justify-center gap-2.5 rounded-full bg-iasmin-purple text-[17px] font-semibold tracking-[0.5px] text-white shadow-[0_10px_22px_rgba(138,0,196,0.32)] transition-colors hover:bg-iasmin-purple-deep"
                >
                    <svg width="22" height="18" viewBox="0 0 24 20" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 9C10 3 5 1 2.5 2.5S3 9 12 10.5M12 9c2-6 7-8 9.5-6.5S21 9 12 10.5M12 10.5c-5 .5-8 3.5-6.5 6s5-1 6.5-5M12 10.5c5 .5 8 3.5 6.5 6s-5-1-6.5-5" />
                    </svg>
                    Confirmar presença
                </Link>
            </div>
        </section>

        <!-- ================= DATA / LOCAL ================= -->
        <section class="relative w-full overflow-x-clip bg-iasmin-cloud">
            <div class="pointer-events-none absolute right-[12%] top-[160px] h-[280px] w-[280px] rounded-full bg-iasmin-lilac/35 blur-[70px]" />
            <IasminDecor :items="dataDecor" :frame-h="640" />
            <AnimatedButterfly variant="purple" :rotate="12" :voo="3" :duration="21" :delay="8" :beat="0.33" class="hidden h-[34px] w-[42px] lg:right-[10%] lg:top-[40px] lg:z-10 lg:block" />

            <div class="relative z-20 mx-auto flex w-full max-w-6xl flex-col items-center gap-8 px-6 py-14 text-center lg:py-20">
                <div class="flex flex-col items-center gap-0.5">
                    <h2 class="font-script text-5xl leading-[1.1] text-iasmin-purple lg:text-6xl">Anote na agenda</h2>
                    <p class="text-xs uppercase tracking-[3px] text-iasmin-mauve">o grande dia</p>
                </div>

                <div class="flex flex-col items-center gap-8 lg:flex-row lg:items-stretch lg:justify-center lg:gap-8">
                    <!-- Data + hora -->
                    <div class="flex items-center gap-[22px]">
                        <div class="flex flex-col items-end font-display font-bold leading-none text-iasmin-ink">
                            <span class="text-[52px]">{{ partyDay.dd }}</span>
                            <span class="text-[34px] text-iasmin-purple">{{ partyDay.month }}</span>
                            <span class="text-[34px]">{{ partyDay.year }}</span>
                        </div>
                        <div class="h-[130px] border-l-4 border-dotted border-iasmin-violet" />
                        <div class="flex flex-col items-start">
                            <span class="text-[11px] leading-none text-iasmin-mauve">às</span>
                            <span class="mt-1 font-display text-[44px] font-bold leading-none text-iasmin-ink">{{ partyDay.time }}</span>
                            <span class="mt-1 text-sm uppercase tracking-[2px] text-iasmin-mauve">horas</span>
                        </div>
                    </div>

                    <!-- Local -->
                    <a
                        :href="partyMapUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex w-full max-w-[330px] items-center gap-3.5 rounded-[20px] border border-iasmin-lilac bg-white px-5 py-[18px] text-left no-underline shadow-[0_8px_20px_rgba(90,30,140,0.08)] lg:max-w-[420px]"
                    >
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-iasmin-petal">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#8A00C4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z" />
                                <circle cx="12" cy="9.5" r="2.6" />
                            </svg>
                        </div>
                        <div class="flex min-w-0 grow flex-col gap-1">
                            <span class="text-xs uppercase tracking-[2px] text-iasmin-mauve">Local</span>
                            <span class="text-[15px] font-semibold leading-snug text-iasmin-ink">{{ partyAddress }}</span>
                            <span class="text-sm text-iasmin-purple">Abrir no mapa</span>
                        </div>
                    </a>
                </div>

                <!-- Countdown -->
                <div class="flex flex-col items-center gap-2.5">
                    <p class="text-xs uppercase tracking-[3px] text-iasmin-mauve">Faltam</p>
                    <div class="flex gap-2.5 lg:gap-4">
                        <div
                            v-for="item in countdown"
                            :key="item.label"
                            class="flex h-[74px] w-[70px] flex-col items-center justify-center gap-0.5 rounded-2xl border border-iasmin-lilac bg-white lg:h-[96px] lg:w-[92px]"
                        >
                            <span class="font-display text-[28px] font-bold text-iasmin-purple lg:text-[38px]">{{ item.value }}</span>
                            <span class="text-[11px] text-iasmin-mauve lg:text-sm">{{ item.label }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= MOMENTOS / POLAROIDES ================= -->
        <section class="relative w-full overflow-x-clip bg-white">
            <div class="pointer-events-none absolute -left-20 top-[240px] h-[260px] w-[260px] rounded-full bg-iasmin-mint/70 blur-[70px]" />
            <div class="pointer-events-none absolute right-[15%] top-[60px] h-[220px] w-[220px] rounded-full bg-iasmin-lilac/30 blur-[60px]" />
            <IasminDecor :items="momentosDecor" :frame-h="640" />
            <AnimatedButterfly variant="lilac" :rotate="-8" :voo="3" :duration="15" :delay="3" :beat="0.24" class="hidden h-[27px] w-[34px] lg:left-[45%] lg:top-[24px] lg:z-10 lg:block" />

            <div class="relative z-20 mx-auto flex w-full max-w-5xl flex-col items-center gap-[30px] px-6 py-14 lg:py-20">
                <div class="flex flex-col items-center gap-0.5 text-center">
                    <h2 class="font-script text-5xl leading-[1.1] text-iasmin-purple lg:text-6xl">Momentos da Iasmin</h2>
                    <p class="text-xs uppercase tracking-[3px] text-iasmin-mauve">15 primaveras em fotos</p>
                </div>

                <div class="grid w-full max-w-[342px] grid-cols-2 gap-x-[22px] gap-y-[30px] lg:max-w-none lg:grid-cols-4 lg:gap-8">
                    <button
                        v-for="photo in photos"
                        :key="photo.label"
                        type="button"
                        class="relative cursor-pointer border-0 bg-white px-[9px] pt-[9px] pb-[38px] text-left shadow-[0_12px_24px_rgba(90,30,140,0.16)]"
                        :class="[photo.rotate, photo.offset]"
                        :aria-label="`Abrir foto: ${photo.caption}`"
                        @click="openPhoto = photo"
                    >
                        <div class="absolute left-[50px] -top-3 h-[22px] w-[60px]" :class="photo.tapeClass" />
                        <img
                            v-if="photo.src"
                            :src="photo.src"
                            :alt="photo.caption"
                            class="h-[150px] w-full object-cover lg:h-[200px]"
                            :class="photo.focus ?? 'object-[center_22%]'"
                        />
                        <div v-else class="flex h-[150px] flex-col items-center justify-center gap-1.5 bg-gradient-to-br lg:h-[200px]" :class="photo.gradient">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" :stroke="photo.stroke" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 8h3l2-2.5h6L17 8h3v11H4z" />
                                <circle cx="12" cy="13" r="3.6" />
                            </svg>
                            <span class="text-[11px] text-iasmin-plum">{{ photo.label }}</span>
                        </div>
                        <span class="absolute inset-x-0 bottom-1.5 text-center font-script text-[22px] text-iasmin-purple-deep">{{ photo.caption }}</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- ================= INFORMAÇÕES ================= -->
        <section class="relative w-full overflow-x-clip bg-iasmin-cloud">
            <div class="pointer-events-none absolute right-[20%] top-[380px] h-[260px] w-[260px] rounded-full bg-iasmin-mint/70 blur-[70px]" />
            <IasminDecor :items="infoDecor" :frame-h="740" />
            <FlowerSvg variant="mid" class="pointer-events-none absolute -left-[48px] top-[120px] hidden h-[110px] w-[110px] rotate-[12deg] lg:z-10 lg:block lg:h-[160px] lg:w-[160px]" />
            <AnimatedButterfly variant="purple" :rotate="-10" :voo="2" :duration="18" :delay="2" :beat="0.31" class="hidden h-[30px] w-[38px] lg:left-[8%] lg:top-[70px] lg:z-10 lg:block" />
            <AnimatedButterfly variant="mid" :rotate="12" :voo="4" :duration="22" :delay="7" :beat="0.28" class="hidden h-[34px] w-[42px] lg:right-[14%] lg:top-[360px] lg:z-10 lg:block" />

            <div class="relative z-20 mx-auto flex w-full max-w-6xl flex-col items-center gap-8 px-6 py-14 lg:py-20">
                <div class="flex flex-col items-center gap-0.5 text-center">
                    <h2 class="font-script text-5xl leading-[1.1] text-iasmin-purple lg:text-6xl">Fique por dentro</h2>
                    <p class="text-xs uppercase tracking-[3px] text-iasmin-mauve">informações importantes</p>
                </div>

                <div class="flex w-full max-w-[342px] flex-col gap-4 lg:max-w-none lg:flex-row lg:items-stretch lg:gap-6">
                    <!-- Piscina -->
                    <div class="flex gap-4 rounded-[22px] border border-iasmin-mint bg-white p-5 shadow-[0_8px_20px_rgba(0,127,255,0.07)] lg:flex-1">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-iasmin-sky">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#007FFF" stroke-width="1.7" stroke-linecap="round" aria-hidden="true">
                                <path d="M2 15c2 0 2-1.5 4-1.5S8 15 10 15s2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5" />
                                <path d="M2 19.5c2 0 2-1.5 4-1.5s2 1.5 4 1.5 2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5" />
                            </svg>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <h3 class="font-display text-xl font-bold text-iasmin-ink">Vai ter piscina!</h3>
                            <p class="text-[15px] leading-[1.5] text-iasmin-ink">Não esqueça de trazer seu traje de banho, toalha e protetor solar.</p>
                        </div>
                    </div>

                    <!-- Dress code -->
                    <div class="flex gap-4 rounded-[22px] border border-iasmin-lilac bg-white p-5 shadow-[0_8px_20px_rgba(138,0,196,0.07)] lg:flex-1">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-iasmin-petal">
                            <svg width="20" height="20" viewBox="0 0 26 26" aria-hidden="true">
                                <circle cx="13" cy="13" r="10" fill="#9D4EDD" />
                                <path d="M6 20L20 6" stroke="#ffffff" stroke-width="2.4" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="flex grow flex-col gap-2">
                            <h3 class="font-display text-xl font-bold text-iasmin-ink">Dress code</h3>
                            <p class="text-[15px] leading-[1.5] text-iasmin-ink">
                                Roupas leves e confortáveis, de qualquer cor,
                                <strong class="text-iasmin-purple">exceto roxo</strong>. Essa cor é exclusiva da aniversariante!
                            </p>
                        </div>
                    </div>

                    <!-- Bebida -->
                    <div class="flex gap-4 rounded-[22px] border border-[#C8E3C6] bg-white p-5 shadow-[0_8px_20px_rgba(80,140,80,0.08)] lg:flex-1">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-iasmin-sage-bg">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3F7A3D" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M6 3h12l-1.2 7.5a4.8 4.8 0 0 1-9.6 0z" />
                                <path d="M12 15.3V21M8.5 21h7" />
                            </svg>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <h3 class="font-display text-xl font-bold text-iasmin-ink">Traga sua bebida</h3>
                            <p class="text-[15px] leading-[1.5] text-iasmin-ink">Cada convidado deve levar a bebida alcoólica de sua preferência.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= CONFIRMAÇÃO ================= -->
        <section class="relative w-full overflow-hidden bg-gradient-to-b from-white to-iasmin-mist">
            <IasminDecor :items="confirmDecor" :frame-h="500" />
            <FlowerSvg variant="purple" class="pointer-events-none absolute -left-[70px] bottom-[10px] hidden h-[170px] w-[170px] rotate-[6deg] lg:z-10 lg:block lg:h-[230px] lg:w-[230px]" />
            <FlowerSvg variant="lilac" class="pointer-events-none absolute -right-[40px] bottom-[20px] hidden h-[140px] w-[140px] -rotate-[12deg] lg:z-10 lg:block lg:h-[200px] lg:w-[200px]" />
            <AnimatedButterfly variant="blue" :rotate="-14" :voo="4" :duration="23" :delay="6" :beat="0.36" class="hidden h-[36px] w-[46px] lg:left-[12%] lg:top-[40px] lg:z-10 lg:block" />

            <!-- pb grande no mobile reserva a faixa de flores abaixo do botão (como no
                 reference: o CTA termina bem acima do jardim de flores da base). -->
            <div class="relative z-20 mx-auto flex w-full max-w-3xl flex-col items-center gap-4 px-6 pt-16 pb-[260px] text-center lg:py-24">
                <h2 class="max-w-[300px] font-script text-[46px] leading-none text-iasmin-purple lg:max-w-none lg:text-6xl">Contamos com sua presença!</h2>
                <p class="max-w-[300px] text-[15px] leading-[1.5] text-iasmin-ink lg:max-w-[440px] lg:text-base">
                    Confirme até <strong>{{ rsvpDeadline }}</strong> para prepararmos tudo com carinho.
                </p>
                <Link
                    href="/confirmar"
                    class="flex h-14 w-[300px] items-center justify-center gap-2.5 rounded-full bg-iasmin-purple text-[17px] font-semibold tracking-[0.5px] text-white shadow-[0_10px_22px_rgba(138,0,196,0.32)] transition-colors hover:bg-iasmin-purple-deep"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12.5l4.5 4.5L19 7.5" />
                    </svg>
                    Confirmar presença
                </Link>
            </div>
        </section>
    </div>

    <GiftListButton />
    <PhotoLightbox v-if="openPhoto" :photo="openPhoto" @close="closePhoto" />
</template>
