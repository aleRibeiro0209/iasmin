<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Eye, Pencil, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import AdminPagination from '@/components/AdminPagination.vue';
import ConfirmDeleteDialog from '@/components/ConfirmDeleteDialog.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
const editing = ref<Confirmation | null>(null);
const pendingDelete = ref<Confirmation | null>(null);
const deleteName = ref('');
const deleting = ref(false);

const editForm = useForm({
    name: '',
    companions: [] as string[],
});

const startEdit = (confirmation: Confirmation) => {
    editing.value = confirmation;
    editForm.clearErrors();
    editForm.name = confirmation.name;
    editForm.companions = [...(confirmation.companions ?? [])];
};

const closeEdit = () => {
    editing.value = null;
};

const resizeCompanions = (count: number) => {
    const next = Math.min(10, Math.max(0, count));
    const names = editForm.companions.slice(0, next);

    while (names.length < next) {
        names.push('');
    }

    editForm.companions = names;
};

const companionError = (index: number) =>
    (editForm.errors as Record<string, string | undefined>)[`companions.${index}`];

const saveEdit = () => {
    if (!editing.value) {
        return;
    }

    const id = editing.value.id;
    const attending = editing.value.attending;

    editForm
        .transform((data) => ({
            name: data.name.trim(),
            companions: attending ? data.companions.map((name) => name.trim()) : [],
        }))
        .put(`/confirmations/${id}`, {
            preserveScroll: true,
            onSuccess: () => {
                editing.value = null;
            },
        });
};

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
                                                aria-label="Editar"
                                                @click="startEdit(confirmation)"
                                            >
                                                <Pencil />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>Editar</TooltipContent>
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
                    <DialogDescription>Nome, acompanhantes e recado desta confirmação.</DialogDescription>
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
                    <div v-if="viewing.message">
                        <p class="text-muted-foreground">Recado</p>
                        <p class="mt-1 font-medium whitespace-pre-wrap">{{ viewing.message }}</p>
                    </div>
                </div>
            </DialogContent>
        </Dialog>

        <Dialog :open="editing !== null" @update:open="(isOpen) => !isOpen && closeEdit()">
            <DialogContent>
                <form class="grid gap-4" @submit.prevent="saveEdit">
                    <DialogHeader>
                        <DialogTitle>Editar confirmação</DialogTitle>
                        <DialogDescription>Corrija o nome e separe cada acompanhante em um campo.</DialogDescription>
                    </DialogHeader>

                    <div class="grid max-h-[60vh] gap-4 overflow-y-auto pr-1">
                        <div class="grid gap-2">
                            <Label for="edit-confirmation-name">Nome completo</Label>
                            <Input id="edit-confirmation-name" v-model="editForm.name" type="text" maxlength="120" required />
                            <InputError :message="editForm.errors.name" />
                        </div>

                        <div v-if="editing?.attending" class="grid gap-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="grid gap-1">
                                    <Label>Acompanhantes</Label>
                                    <InputError :message="editForm.errors.companions" />
                                </div>
                                <div class="flex items-center gap-3">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="icon"
                                        aria-label="Diminuir"
                                        :disabled="editForm.companions.length === 0"
                                        @click="resizeCompanions(editForm.companions.length - 1)"
                                    >
                                        −
                                    </Button>
                                    <span class="w-6 text-center text-base font-semibold">{{ editForm.companions.length }}</span>
                                    <Button
                                        type="button"
                                        size="icon"
                                        aria-label="Aumentar"
                                        :disabled="editForm.companions.length === 10"
                                        @click="resizeCompanions(editForm.companions.length + 1)"
                                    >
                                        +
                                    </Button>
                                </div>
                            </div>

                            <div v-for="(name, index) in editForm.companions" :key="index" class="grid gap-2">
                                <Label :for="`edit-companion-${index}`">Nome completo (Acompanhante {{ index + 1 }})</Label>
                                <Input
                                    :id="`edit-companion-${index}`"
                                    v-model="editForm.companions[index]"
                                    type="text"
                                    maxlength="120"
                                    required
                                />
                                <InputError :message="companionError(index)" />
                            </div>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="closeEdit">Cancelar</Button>
                        <Button type="submit" :disabled="editForm.processing">
                            {{ editForm.processing ? 'Salvando…' : 'Salvar' }}
                        </Button>
                    </DialogFooter>
                </form>
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
