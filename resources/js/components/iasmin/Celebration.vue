<script setup lang="ts">
import { nextTick, onMounted, onUnmounted, ref } from 'vue';

const ready = ref(false);
const toast = ref(true);
const canvas = ref<HTMLCanvasElement | null>(null);

let frame = 0;
let hideTimer = 0;

type Piece = {
    x: number;
    y: number;
    w: number;
    h: number;
    vx: number;
    vy: number;
    rot: number;
    spin: number;
    color: string;
    round: boolean;
};

const colors = ['#8a00c4', '#9d4edd', '#c77dff', '#e0aaff', '#6a0099'];
const pieces: Piece[] = [];

const resize = () => {
    const el = canvas.value;

    if (!el) {
        return;
    }

    el.width = window.innerWidth;
    el.height = window.innerHeight;
};

const burst = () => {
    const width = window.innerWidth;

    for (let i = 0; i < 110; i++) {
        pieces.push({
            x: Math.random() * width,
            y: -12 - Math.random() * 160,
            w: 7 + Math.random() * 7,
            h: 10 + Math.random() * 8,
            vx: (Math.random() - 0.5) * 0.8,
            vy: 0.45 + Math.random() * 0.9,
            rot: Math.random() * Math.PI,
            spin: (Math.random() - 0.5) * 0.18,
            color: colors[i % colors.length],
            round: Math.random() > 0.72,
        });
    }
};

const draw = () => {
    const el = canvas.value;
    const ctx = el?.getContext('2d');

    if (!el || !ctx) {
        return;
    }

    ctx.clearRect(0, 0, el.width, el.height);

    for (const piece of pieces) {
        piece.x += piece.vx;
        piece.y += piece.vy;
        piece.vy += 0.035;
        piece.rot += piece.spin;

        ctx.save();
        ctx.translate(piece.x, piece.y);
        ctx.rotate(piece.rot);
        ctx.fillStyle = piece.color;

        if (piece.round) {
            ctx.beginPath();
            ctx.arc(0, 0, piece.w / 2, 0, Math.PI * 2);
            ctx.fill();
        } else {
            ctx.fillRect(-piece.w / 2, -piece.h / 2, piece.w, piece.h);
        }

        ctx.restore();
    }

    if (pieces.some((piece) => piece.y < window.innerHeight + 30)) {
        frame = requestAnimationFrame(draw);
    }
};

onMounted(async () => {
    ready.value = true;
    await nextTick();

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!reduced && canvas.value) {
        resize();
        burst();
        frame = requestAnimationFrame(draw);
    }

    hideTimer = window.setTimeout(() => {
        toast.value = false;
    }, 6400);
});

onUnmounted(() => {
    cancelAnimationFrame(frame);
    window.clearTimeout(hideTimer);
});
</script>

<template>
    <Teleport to="body">
        <div v-if="ready">
            <canvas ref="canvas" class="pointer-events-none fixed inset-0 h-dvh w-screen" style="z-index: 70" aria-hidden="true" />
            <div
                v-if="toast"
                class="iasmin-toast fixed flex items-center gap-3 rounded-2xl bg-iasmin-purple px-4 py-3 text-white shadow-[0_16px_40px_rgba(138,0,196,0.35)]"
                style="z-index: 80; top: 12px; left: 12px; max-width: 230px"
                role="status"
            >
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/15 text-lg" aria-hidden="true">✓</span>
                <span class="text-left">
                    <span class="block text-[15px] font-semibold leading-tight">Presença confirmada!</span>
                    <span class="mt-0.5 block text-[13px] leading-snug text-white/85">Obrigada por celebrar com a Iasmin.</span>
                </span>
            </div>
        </div>
    </Teleport>
</template>
