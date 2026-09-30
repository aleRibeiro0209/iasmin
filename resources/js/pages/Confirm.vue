<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import FlowerSvg from '@/components/iasmin/FlowerSvg.vue';
import AnimatedButterfly from '@/components/iasmin/AnimatedButterfly.vue';
import IasminDecor, { type DecorItem } from '@/components/iasmin/IasminDecor.vue';
import GiftListButton from '@/components/iasmin/GiftListButton.vue';

const pageDecor: DecorItem[] = [
    { t: 'b', x: 168, y: 74, s: 60, r: -6, v: 'mid' },
    { t: 'b', x: 318, y: 150, s: 40, r: 16, v: 'blue' },
    { t: 'b', x: 14, y: 300, s: 36, r: -14, v: 'lilac' },
    { t: 'b', x: 336, y: 520, s: 38, r: 12, v: 'purple' },
    { t: 'b', x: 10, y: 900, s: 40, r: -10, v: 'blue' },
    { t: 'b', x: 330, y: 1080, s: 44, r: 14, v: 'lilac' },
    { t: 'f', x: 290, y: -60, s: 170, r: -10, v: 'purple' },
    { t: 'f', x: 250, y: 70, s: 70, r: 20, v: 'lilac' },
    { t: 'f', x: -70, y: 60, s: 120, r: 14, v: 'mid' },
    { t: 'f', x: -60, y: 1140, s: 190, r: 6, v: 'purple' },
    { t: 'f', x: 100, y: 1185, s: 130, r: -14, v: 'lilac' },
    { t: 'f', x: 210, y: 1160, s: 150, r: 18, v: 'blue' },
    { t: 'f', x: 300, y: 1130, s: 180, r: -8, v: 'purple' },
];

const going = ref<'sim' | 'nao'>('sim');

const form = useForm({
    name: '',
    attending: true,
    guests: 1,
    message: '',
});

const isYes = computed(() => going.value === 'sim');
const isNo = computed(() => going.value === 'nao');

const choose = (value: 'sim' | 'nao') => {
    going.value = value;
    form.attending = value === 'sim';
};
const inc = () => (form.guests = Math.min(10, form.guests + 1));
const dec = () => (form.guests = Math.max(1, form.guests - 1));
const submit = () =>
    form
        .transform((data) => ({ ...data, guests: data.attending ? data.guests : 0 }))
        .post('/confirmar');

// Classes dos botões "Sim/Não" conforme seleção.
const choiceClass = (active: boolean) =>
    active
        ? 'border-iasmin-purple bg-iasmin-purple text-white'
        : 'border-iasmin-input bg-white text-iasmin-purple-deep';

const reminders = [
    { text: 'Traje de banho, toalha e protetor solar', emphasis: '', bg: 'bg-iasmin-sky', icon: 'wave' },
    { text: 'Roupa leve e confortável, de qualquer cor, ', emphasis: 'exceto roxo', bg: 'bg-iasmin-petal', icon: 'no-purple' },
    { text: 'Sua bebida alcoólica de preferência', emphasis: '', bg: 'bg-iasmin-sage-bg', icon: 'drink' },
];
</script>

<template>
    <Head title="Iasmin 15 anos – Confirmação de Presença" />

    <div class="relative min-h-screen w-full overflow-hidden bg-white font-body text-iasmin-ink">
        <!-- gradiente da tela (sobre o bg-white do body, para o blur ter cor atrás) -->
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white via-iasmin-cloud to-iasmin-blossom" />

        <!-- blobs -->
        <div class="pointer-events-none absolute -left-20 top-[420px] h-[280px] w-[280px] rounded-full bg-iasmin-mint/70 blur-[70px]" />
        <div class="pointer-events-none absolute right-[10%] top-[60%] h-[260px] w-[260px] rounded-full bg-iasmin-lilac/40 blur-[70px]" />

        <!-- decoração mobile (fiel ao reference) -->
        <IasminDecor :items="pageDecor" :frame-h="1240" />

        <!-- decoração desktop -->
        <FlowerSvg variant="purple" class="pointer-events-none absolute -right-[30px] -top-[60px] hidden h-[170px] w-[170px] -rotate-[10deg] lg:block lg:h-[240px] lg:w-[240px]" />
        <FlowerSvg variant="mid" class="pointer-events-none absolute -left-[70px] top-[60px] hidden h-[120px] w-[120px] rotate-[14deg] lg:block lg:h-[180px] lg:w-[180px]" />
        <AnimatedButterfly variant="mid" :rotate="-6" :voo="1" :duration="18" :delay="0" :beat="0.3" class="hidden h-12 w-[60px] lg:left-[38%] lg:top-[90px] lg:block" />
        <AnimatedButterfly variant="blue" :rotate="16" :voo="5" :duration="20" :delay="5" :beat="0.28" class="hidden h-8 w-10 lg:right-[10%] lg:top-[170px] lg:block" />
        <AnimatedButterfly variant="lilac" :rotate="-14" :voo="2" :duration="16" :delay="9" :beat="0.34" class="hidden h-[29px] w-9 lg:left-[8%] lg:top-[320px] lg:block" />

        <!-- Voltar -->
        <Link
            href="/"
            class="absolute left-3 top-4 z-30 flex h-11 items-center gap-1.5 rounded-full border border-iasmin-lilac bg-white/95 pl-3 pr-4 text-sm font-semibold text-iasmin-purple lg:left-8"
        >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8A00C4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M15 5l-7 7 7 7" />
            </svg>
            Voltar ao convite
        </Link>

        <!-- CONTEÚDO -->
        <!-- pb grande no mobile mantém o conteúdo acima do jardim de flores da base. -->
        <div class="relative z-20 mx-auto flex w-full max-w-4xl flex-col items-center gap-[26px] px-3 pt-[130px] pb-[210px] lg:px-6 lg:pb-16 lg:pt-[150px]">
            <div class="flex flex-col items-center gap-1 text-center">
                <h1 class="font-script text-[50px] leading-[1.05] text-iasmin-purple lg:text-7xl">Confirme sua presença</h1>
                <p class="text-xs uppercase tracking-[3px] text-iasmin-mauve">Iasmin · 15 anos</p>
            </div>

            <div class="flex w-full flex-col items-center gap-[26px] lg:flex-row lg:items-start lg:justify-center lg:gap-8">
                <!-- FORMULÁRIO -->
                <form
                    class="flex w-full flex-col gap-[18px] rounded-[26px] border border-iasmin-lilac bg-white p-5 shadow-[0_16px_36px_rgba(90,30,140,0.12)] lg:max-w-[440px] lg:p-6"
                    @submit.prevent="submit"
                >
                    <div class="flex flex-col gap-2">
                        <label for="rsvp-nome" class="text-sm font-semibold text-iasmin-ink">Seu nome completo</label>
                        <input
                            id="rsvp-nome"
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="Ex.: Maria Souza"
                            class="h-[50px] rounded-[14px] border-[1.5px] border-iasmin-input bg-iasmin-field px-4 text-base text-iasmin-ink outline-iasmin-violet placeholder:text-iasmin-mauve/60"
                        />
                        <p v-if="form.errors.name" class="text-[13px] text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <span class="text-sm font-semibold text-iasmin-ink">Você vai?</span>
                        <div class="grid grid-cols-2 gap-2.5">
                            <button
                                type="button"
                                class="h-[52px] rounded-[14px] border-[1.5px] text-[15px] font-semibold transition-colors"
                                :class="choiceClass(isYes)"
                                @click="choose('sim')"
                            >
                                Sim, eu vou!
                            </button>
                            <button
                                type="button"
                                class="h-[52px] rounded-[14px] border-[1.5px] text-[15px] font-semibold transition-colors"
                                :class="choiceClass(isNo)"
                                @click="choose('nao')"
                            >
                                Não poderei ir
                            </button>
                        </div>
                    </div>

                    <div v-if="isYes" class="flex items-center justify-between gap-3">
                        <span class="w-[150px] text-sm font-semibold text-iasmin-ink">Quantas pessoas, contando com você?</span>
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                aria-label="Diminuir"
                                class="h-11 w-11 rounded-full border-[1.5px] border-iasmin-violet bg-white text-[22px] leading-none text-iasmin-purple"
                                @click="dec"
                            >
                                −
                            </button>
                            <span class="w-[30px] text-center font-display text-[28px] font-bold text-iasmin-purple">{{ form.guests }}</span>
                            <button
                                type="button"
                                aria-label="Aumentar"
                                class="h-11 w-11 rounded-full bg-iasmin-purple text-[22px] leading-none text-white"
                                @click="inc"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="rsvp-msg" class="text-sm font-semibold text-iasmin-ink">
                            Recado para a Iasmin <span class="font-normal text-iasmin-mauve">(opcional)</span>
                        </label>
                        <textarea
                            id="rsvp-msg"
                            v-model="form.message"
                            placeholder="Escreva algo carinhoso…"
                            class="h-[92px] resize-none rounded-[14px] border-[1.5px] border-iasmin-input bg-iasmin-field px-4 py-3 text-base text-iasmin-ink outline-iasmin-violet placeholder:text-iasmin-mauve/60"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="h-14 rounded-full bg-iasmin-purple text-[17px] font-semibold tracking-[0.5px] text-white shadow-[0_10px_22px_rgba(138,0,196,0.32)] transition-colors hover:bg-iasmin-purple-deep disabled:opacity-60"
                    >
                        {{ form.processing ? 'Enviando…' : 'Enviar confirmação' }}
                    </button>
                </form>

                <!-- NÃO ESQUEÇA -->
                <div class="flex w-full flex-col gap-3 rounded-[22px] border border-dashed border-iasmin-orchid bg-white/80 px-5 py-[18px] lg:w-[320px] lg:max-w-[320px] lg:self-start">
                    <p class="text-center text-xs uppercase tracking-[3px] text-iasmin-mauve">não esqueça</p>
                    <div v-for="item in reminders" :key="item.text" class="flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full" :class="item.bg">
                            <svg v-if="item.icon === 'wave'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#007FFF" stroke-width="1.7" stroke-linecap="round" aria-hidden="true">
                                <path d="M2 15c2 0 2-1.5 4-1.5S8 15 10 15s2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5" />
                                <path d="M2 19.5c2 0 2-1.5 4-1.5s2 1.5 4 1.5 2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5" />
                            </svg>
                            <svg v-else-if="item.icon === 'no-purple'" width="20" height="20" viewBox="0 0 26 26" aria-hidden="true">
                                <circle cx="13" cy="13" r="10" fill="#9D4EDD" />
                                <path d="M6 20L20 6" stroke="#ffffff" stroke-width="2.4" stroke-linecap="round" />
                            </svg>
                            <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3F7A3D" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M6 3h12l-1.2 7.5a4.8 4.8 0 0 1-9.6 0z" />
                                <path d="M12 15.3V21M8.5 21h7" />
                            </svg>
                        </div>
                        <span class="text-sm text-iasmin-ink">{{ item.text }}<strong v-if="item.emphasis" class="text-iasmin-purple">{{ item.emphasis }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <GiftListButton />
</template>
