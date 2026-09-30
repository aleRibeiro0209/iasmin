<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Eye, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import AdminPagination from '@/components/AdminPagination.vue';
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { dashboard } from '@/routes';

interface Confirmation {
    id: number;
    name: string;
    attending: boolean;
    guests: number;
    companions: string[] | null;
    message: string | null;
    created_at: string;
}

interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    path: string;
}

defineProps<{
    confirmations: Paginated<Confirmation>;
    stats: { total: number; attending: number; declined: number; guests: number };
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
    },
});

const viewing = ref<Confirmation | null>(null);
const pendingDelete = ref<Confirmation | null>(null);
const deleteName = ref('');
const deleting = ref(false);

const confirmDelete = () => {
    if (!pendingDelete.value) {
        return;
    }

    deleting.value = true;
    router.delete(`/confirmations/${pendingDelete.value.id}`, {
        preserveScroll: true,
        onFinish: () => {
            deleting.value = false;
            pendingDelete.value = null;
        },
    });
};

const formatDate = (value: string) =>
    new Date(value).toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <div class="grid auto-rows-min gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Confirmações</p>
                <p class="mt-1 text-3xl font-bold">{{ stats.total }}</p>
            </div>
            <div class="rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Vão comparecer</p>
                <p class="mt-1 text-3xl font-bold text-green-600">{{ stats.attending }}</p>
            </div>
            <div class="rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Total de convidados</p>
                <p class="mt-1 text-3xl font-bold text-primary">{{ stats.guests }}</p>
            </div>
            <div class="rounded-xl border border-sidebar-border/70 p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Não poderão ir</p>
                <p class="mt-1 text-3xl font-bold text-muted-foreground">{{ stats.declined }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border">
                        <tr>
                            <th class="px-4 py-3 font-medium">Nome</th>
                            <th class="px-4 py-3 font-medium">Vai?</th>
                            <th class="px-4 py-3 font-medium">Pessoas</th>
                            <th class="px-4 py-3 font-medium">Recado</th>
                            <th class="px-4 py-3 font-medium">Enviado em</th>
                            <th class="px-4 py-3 text-right font-medium">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="confirmations.data.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                                Nenhuma confirmação recebida ainda.
                            </td>
                        </tr>
                        <tr
                            v-for="confirmation in confirmations.data"
                            :key="confirmation.id"
                            class="border-b border-sidebar-border/40 last:border-0 dark:border-sidebar-border"
                        >
                            <td class="px-4 py-3 font-medium">
                                {{ confirmation.name }}
                                <p
                                    v-if="confirmation.companions?.length"
                                    class="mt-1 text-xs font-normal text-muted-foreground"
                                >
                                    {{ confirmation.companions.join(', ') }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                    :class="
                                        confirmation.attending
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-gray-100 text-gray-500'
                                    "
                                >
                                    {{ confirmation.attending ? 'Sim' : 'Não' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ confirmation.attending ? confirmation.guests : '—' }}</td>
                            <td class="max-w-[280px] truncate px-4 py-3" :title="confirmation.message || ''">
                                {{ confirmation.message || '—' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-muted-foreground">
                                {{ formatDate(confirmation.created_at) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                aria-label="Ver"
                                                @click="viewing = confirmation"
                                            >
                                                <Eye />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>Ver</TooltipContent>
                                    </Tooltip>
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                class="text-destructive hover:text-destructive"
                                                aria-label="Remover"
                                                @click="deleteName = confirmation.name; pendingDelete = confirmation"
                                            >
                                                <Trash2 />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>Remover</TooltipContent>
                                    </Tooltip>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <AdminPagination :paginator="confirmations" empty-label="0 confirmações" />
        </div>

        <Dialog :open="viewing !== null" @update:open="(isOpen) => !isOpen && (viewing = null)">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Convidado</DialogTitle>
                    <DialogDescription>Nome completo e acompanhantes desta confirmação.</DialogDescription>
                </DialogHeader>
                <div v-if="viewing" class="grid gap-4 text-sm">
                    <div>
                        <p class="text-muted-foreground">Nome</p>
                        <p class="mt-1 font-medium">{{ viewing.name }}</p>
                    </div>
                    <div v-if="viewing.companions?.length">
                        <p class="text-muted-foreground">Acompanhantes</p>
                        <ol class="mt-1 list-decimal space-y-1 pl-5 font-medium">
                            <li v-for="(companion, index) in viewing.companions" :key="index">{{ companion }}</li>
                        </ol>
                    </div>
                </div>
            </DialogContent>
        </Dialog>

        <ConfirmDeleteDialog
            :open="pendingDelete !== null"
            title="Remover confirmação"
            :description="`Remover a confirmação de ${deleteName}? Essa ação não pode ser desfeita.`"
            :processing="deleting"
            @update:open="(isOpen) => !isOpen && (pendingDelete = null)"
            @confirm="confirmDelete"
        />
    </div>
</template>
