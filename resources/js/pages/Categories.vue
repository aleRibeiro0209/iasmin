<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import AdminPagination, { type Paginator } from '@/components/AdminPagination.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';

interface Category {
    id: number;
    name: string;
    items_count: number;
}

defineProps<{
    categories: Paginator & { data: Category[] };
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Categorias', href: '/categories' }],
    },
});

const page = usePage();
const deleteError = computed(() => page.props.errors.delete);
const open = ref(false);

const form = useForm({
    name: '',
});

watch(open, (isOpen) => {
    if (!isOpen) {
        form.reset();
        form.clearErrors();
    }
});

const submit = () => {
    form.post('/categories', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            open.value = false;
        },
    });
};

const remove = (id: number) => {
    router.delete(`/categories/${id}`, { preserveScroll: true });
};
</script>

<template>
    <Head title="Categorias" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-lg font-semibold">Categorias</h1>
                <p class="text-sm text-muted-foreground">Agrupe os presentes da lista.</p>
            </div>

            <Dialog v-model:open="open">
                <DialogTrigger as-child>
                    <Button>
                        <Plus />
                        Nova categoria
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <form class="grid gap-4" @submit.prevent="submit">
                        <DialogHeader>
                            <DialogTitle>Nova categoria</DialogTitle>
                            <DialogDescription>O nome aparece na lista de presentes.</DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="category-name">Nome</Label>
                            <Input
                                id="category-name"
                                v-model="form.name"
                                type="text"
                                maxlength="120"
                                placeholder="Nome da categoria"
                                required
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" @click="open = false">Cancelar</Button>
                            <Button type="submit" :disabled="form.processing">Salvar</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>

        <p v-if="deleteError" class="text-sm text-red-500">{{ deleteError }}</p>

        <div class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border">
                    <tr>
                        <th class="px-4 py-3 font-medium">Categoria</th>
                        <th class="px-4 py-3 font-medium">Itens</th>
                        <th class="px-4 py-3 text-right font-medium">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="categories.data.length === 0">
                        <td colspan="3" class="px-4 py-8 text-center text-muted-foreground">Nenhuma categoria ainda.</td>
                    </tr>
                    <tr
                        v-for="category in categories.data"
                        :key="category.id"
                        class="border-b border-sidebar-border/40 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3 font-medium">{{ category.name }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ category.items_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="text-destructive hover:text-destructive"
                                        aria-label="Remover"
                                        @click="remove(category.id)"
                                    >
                                        <Trash2 />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>Remover</TooltipContent>
                            </Tooltip>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
            <AdminPagination :paginator="categories" empty-label="0 categorias" />
        </div>
    </div>
</template>
