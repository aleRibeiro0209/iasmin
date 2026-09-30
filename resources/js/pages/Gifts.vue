<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
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
    description: string | null;
    gift_category_id: number;
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
const editing = ref<Gift | null>(null);

const form = useForm({
    name: '',
    description: '',
    gift_category_id: '',
});

const editForm = useForm({
    name: '',
    description: '',
    gift_category_id: '' as string | number,
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

const startEdit = (gift: Gift) => {
    editForm.clearErrors();
    editForm.name = gift.name;
    editForm.description = gift.description ?? '';
    editForm.gift_category_id = gift.gift_category_id;
    editing.value = gift;
};

const closeEdit = () => {
    editing.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const saveEdit = () => {
    if (!editing.value) {
        return;
    }

    editForm.put(`/gifts/${editing.value.id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => closeEdit(),
    });
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
                            <Label for="gift-description">Descrição</Label>
                            <textarea
                                id="gift-description"
                                v-model="form.description"
                                maxlength="500"
                                rows="3"
                                placeholder="Detalhe o item, se quiser"
                                class="border-input w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                            />
                            <InputError :message="form.errors.description" />
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
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border">
                    <tr>
                        <th class="px-4 py-3 font-medium">Item</th>
                        <th class="px-4 py-3 font-medium">Descrição</th>
                        <th class="px-4 py-3 font-medium">Categoria</th>
                        <th class="px-4 py-3 text-right font-medium">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="gifts.data.length === 0">
                        <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">Nenhum item na lista ainda.</td>
                    </tr>
                    <tr
                        v-for="gift in gifts.data"
                        :key="gift.id"
                        class="border-b border-sidebar-border/40 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3 font-medium">{{ gift.name }}</td>
                        <td class="max-w-[280px] truncate px-4 py-3 text-muted-foreground" :title="gift.description || ''">
                            {{ gift.description || '—' }}
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">{{ gift.category }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            aria-label="Editar"
                                            @click="startEdit(gift)"
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
                                            @click="remove(gift.id)"
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
            <AdminPagination :paginator="gifts" empty-label="0 presentes" />
        </div>

        <Dialog :open="editing !== null" @update:open="(isOpen) => !isOpen && closeEdit()">
            <DialogContent>
                <form class="grid gap-4" @submit.prevent="saveEdit">
                    <DialogHeader>
                        <DialogTitle>Editar presente</DialogTitle>
                        <DialogDescription>Atualize a categoria, o nome e a descrição.</DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-gift-name">Nome</Label>
                        <Input
                            id="edit-gift-name"
                            v-model="editForm.name"
                            type="text"
                            maxlength="120"
                            placeholder="Nome do item"
                            required
                        />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-gift-description">Descrição</Label>
                        <textarea
                            id="edit-gift-description"
                            v-model="editForm.description"
                            maxlength="500"
                            rows="3"
                            placeholder="Detalhe o item, se quiser"
                            class="border-input w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        />
                        <InputError :message="editForm.errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-gift-category">Categoria</Label>
                        <select
                            id="edit-gift-category"
                            v-model="editForm.gift_category_id"
                            required
                            class="border-input h-9 w-full rounded-md border bg-transparent px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        >
                            <option value="" disabled>Escolha uma categoria</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <InputError :message="editForm.errors.gift_category_id" />
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="closeEdit">Cancelar</Button>
                        <Button type="submit" :disabled="editForm.processing">Salvar</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
