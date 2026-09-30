<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import { ref, watch } from 'vue';
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
}

interface Gift {
    id: number;
    name: string;
    category: string | null;
}

defineProps<{
    categories: Category[];
    gifts: Paginator & { data: Gift[] };
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Lista de presentes', href: '/gifts' }],
    },
});

const open = ref(false);

const form = useForm({
    name: '',
    gift_category_id: '',
});

watch(open, (isOpen) => {
    if (!isOpen) {
        form.reset();
        form.clearErrors();
    }
});

const submit = () => {
    form.post('/gifts', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            open.value = false;
        },
    });
};

const remove = (id: number) => {
    router.delete(`/gifts/${id}`, { preserveScroll: true });
};
</script>

<template>
    <Head title="Lista de presentes" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-lg font-semibold">Lista de presentes</h1>
                <p class="text-sm text-muted-foreground">Itens que aparecem para quem for presentear.</p>
            </div>

            <Dialog v-model:open="open">
                <DialogTrigger as-child>
                    <Button :disabled="categories.length === 0">
                        <Plus />
                        Novo presente
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <form class="grid gap-4" @submit.prevent="submit">
                        <DialogHeader>
                            <DialogTitle>Novo presente</DialogTitle>
                            <DialogDescription>Escolha a categoria e o nome do item.</DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="gift-name">Nome</Label>
                            <Input
                                id="gift-name"
                                v-model="form.name"
                                type="text"
                                maxlength="120"
                                placeholder="Nome do item"
                                required
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="gift-category">Categoria</Label>
                            <select
                                id="gift-category"
                                v-model="form.gift_category_id"
                                required
                                class="border-input h-9 w-full rounded-md border bg-transparent px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                            >
                                <option value="" disabled>Escolha uma categoria</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.gift_category_id" />
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" @click="open = false">Cancelar</Button>
                            <Button type="submit" :disabled="form.processing">Salvar</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>

        <p v-if="categories.length === 0" class="text-sm text-muted-foreground">
            Cadastre uma categoria antes de adicionar itens.
            <Link href="/categories" class="font-medium text-foreground underline">Ir para categorias</Link>
        </p>

        <div class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <div class="overflow-x-auto">
            <table class="w-full min-w-[520px] text-left text-sm">
                <thead class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border">
                    <tr>
                        <th class="px-4 py-3 font-medium">Item</th>
                        <th class="px-4 py-3 font-medium">Categoria</th>
                        <th class="px-4 py-3 text-right font-medium">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="gifts.data.length === 0">
                        <td colspan="3" class="px-4 py-8 text-center text-muted-foreground">Nenhum item na lista ainda.</td>
                    </tr>
                    <tr
                        v-for="gift in gifts.data"
                        :key="gift.id"
                        class="border-b border-sidebar-border/40 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3 font-medium">{{ gift.name }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ gift.category }}</td>
                        <td class="px-4 py-3 text-right">
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="text-destructive hover:text-destructive"
                                        aria-label="Remover"
                                        @click="remove(gift.id)"
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
            <AdminPagination :paginator="gifts" empty-label="0 presentes" />
        </div>
    </div>
</template>
