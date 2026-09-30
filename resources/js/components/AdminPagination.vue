<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { computed } from 'vue';

export interface Paginator {
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    path: string;
}

const props = defineProps<{
    paginator: Paginator;
    emptyLabel: string;
}>();

const pageNumbers = computed(() => {
    const current = props.paginator.current_page;
    const last = props.paginator.last_page;
    const start = Math.max(1, Math.min(current - 2, last - 4));
    const end = Math.min(last, start + 4);

    return Array.from({ length: Math.max(0, end - start + 1) }, (_, index) => start + index);
});

const pageHref = (page: number) => `${props.paginator.path}?page=${page}`;
</script>

<template>
    <div
        class="flex flex-col gap-3 border-t border-sidebar-border/70 px-4 py-3 sm:flex-row sm:items-center sm:justify-between dark:border-sidebar-border"
    >
        <p class="text-sm text-muted-foreground">
            <template v-if="paginator.total === 0">{{ emptyLabel }}</template>
            <template v-else>{{ paginator.from }}–{{ paginator.to }} de {{ paginator.total }}</template>
        </p>

        <div class="flex items-center gap-1">
            <Link
                v-if="paginator.prev_page_url"
                :href="paginator.prev_page_url"
                preserve-scroll
                aria-label="Anterior"
                class="inline-flex size-8 items-center justify-center rounded-md border hover:bg-accent"
            >
                <ChevronLeft class="size-4" />
            </Link>
            <span
                v-else
                class="inline-flex size-8 items-center justify-center rounded-md border text-muted-foreground opacity-50"
                aria-hidden="true"
            >
                <ChevronLeft class="size-4" />
            </span>

            <Link
                v-for="page in pageNumbers"
                :key="page"
                :href="pageHref(page)"
                preserve-scroll
                class="inline-flex h-8 min-w-8 items-center justify-center rounded-md border px-2 text-sm"
                :class="
                    page === paginator.current_page
                        ? 'border-primary bg-primary text-primary-foreground'
                        : 'hover:bg-accent'
                "
            >
                {{ page }}
            </Link>

            <Link
                v-if="paginator.next_page_url"
                :href="paginator.next_page_url"
                preserve-scroll
                aria-label="Próxima"
                class="inline-flex size-8 items-center justify-center rounded-md border hover:bg-accent"
            >
                <ChevronRight class="size-4" />
            </Link>
            <span
                v-else
                class="inline-flex size-8 items-center justify-center rounded-md border text-muted-foreground opacity-50"
                aria-hidden="true"
            >
                <ChevronRight class="size-4" />
            </span>
        </div>
    </div>
</template>
