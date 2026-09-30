<script setup lang="ts">
import type { CSSProperties } from 'vue';
import FlowerSvg from '@/components/iasmin/FlowerSvg.vue';
import AnimatedButterfly from '@/components/iasmin/AnimatedButterfly.vue';

type Variant = 'purple' | 'lilac' | 'mid' | 'blue';

// Item decorativo no espaço de referência (mobile 390px de largura).
// t: 'b' borboleta | 'f' flor · x/y: canto sup. esq. · s: tamanho · r: rotação · v: variante.
export interface DecorItem {
    t: 'b' | 'f';
    x: number;
    y: number;
    s: number;
    r: number;
    v: Variant;
}

const props = withDefaults(
    defineProps<{
        items: DecorItem[];
        // Dimensões do frame de referência ao qual as coordenadas pertencem.
        frameW?: number;
        frameH: number;
    }>(),
    { frameW: 390 },
);

// Altura da borboleta = 80% da largura (mesma proporção do reference).
const heightOf = (it: DecorItem) => (it.t === 'b' ? Math.round(it.s * 0.8) : it.s);

// Ancoragem por borda mais próxima em cada eixo: mantém as decorações da esquerda
// coladas à esquerda e as da direita à direita (e topo/rodapé), preservando o
// enquadramento do reference mesmo quando a largura/altura real difere de 390px.
// includeRotation: flores giram no próprio style; borboletas recebem a rotação na
// camada do meio (o outer .voa reserva o transform para o trajeto animado).
const styleFor = (it: DecorItem, includeRotation: boolean): CSSProperties => {
    const h = heightOf(it);
    const cx = it.x + it.s / 2;
    const cy = it.y + h / 2;

    const horizontal: CSSProperties =
        cx <= props.frameW / 2
            ? { left: `${it.x}px` }
            : { right: `${props.frameW - (it.x + it.s)}px` };

    const vertical: CSSProperties =
        cy <= props.frameH / 2
            ? { top: `${it.y}px` }
            : { bottom: `${props.frameH - (it.y + h)}px` };

    return {
        position: 'absolute',
        width: `${it.s}px`,
        height: `${h}px`,
        ...(includeRotation ? { transform: `rotate(${it.r}deg)` } : {}),
        ...horizontal,
        ...vertical,
    };
};

// Índice sequencial só entre borboletas (para distribuir os voos de forma estável).
const butterflyIndex = (i: number) =>
    props.items.slice(0, i).filter((it) => it.t === 'b').length;

// Regras de distribuição (spec):
//  · alterna voo1, voo2, voo3 e depois a "travessia";
//  · na travessia, esquerda usa voo4 e direita usa voo5;
//  · duração 14–24s, delay negativo único, bater de asas 0.24–0.39s.
const flight = (it: DecorItem, i: number) => {
    const n = butterflyIndex(i);
    const cycle = n % 4;
    const onLeft = it.x + it.s / 2 <= props.frameW / 2;
    const voo = (cycle < 3 ? cycle + 1 : onLeft ? 4 : 5) as 1 | 2 | 3 | 4 | 5;
    return {
        voo,
        duration: 14 + ((n * 3) % 11), // 14..24
        delay: (n * 3.7) % 24, // trajeto começa em ponto diferente
        beat: 0.24 + ((n * 2) % 6) * 0.03, // 0.24..0.39
    };
};
</script>

<template>
    <!-- Camada decorativa exclusiva do mobile (no desktop usamos o layout responsivo).
         z-10 + overflow-x-clip: as decorações pintam ACIMA do fundo de todas as
         seções (o conteúdo fica em z-20), então borboletas e flores atravessam a
         fronteira entre seções sem serem cortadas nem cobertas pelo fundo da
         próxima seção. Só o excesso horizontal é recortado (sem scroll lateral). -->
    <div class="pointer-events-none absolute inset-0 z-10 overflow-x-clip lg:hidden" aria-hidden="true">
        <template v-for="(it, i) in items" :key="i">
            <AnimatedButterfly
                v-if="it.t === 'b'"
                :variant="it.v"
                :rotate="it.r"
                v-bind="flight(it, i)"
                :style="styleFor(it, false)"
            />
            <FlowerSvg v-else :variant="it.v" :style="styleFor(it, true)" />
        </template>
    </div>
</template>
