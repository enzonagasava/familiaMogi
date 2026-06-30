<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface ProdutoOption {
    id: number;
    nome: string;
    estoque: number;
    listings_count: number;
    imageUrl?: string | null;
}

interface ListingFormData {
    id?: number;
    produto_id: number | null;
    anuncio_ativo: boolean;
    anuncio_status: string;
    anuncio_tipos: string[];
}

const props = defineProps<{
    title: string;
    mode: 'create' | 'edit';
    produtos: ProdutoOption[];
    anuncioTipos: Record<string, string>;
    listing?: ListingFormData;
    selectedProdutoId?: number | null;
}>();

const form = useForm({
    produto_id: props.listing?.produto_id ?? props.selectedProdutoId ?? null,
    anuncio_ativo: props.listing?.anuncio_ativo ?? true,
    anuncio_status: props.listing?.anuncio_status ?? '',
    anuncio_tipos: props.listing?.anuncio_tipos ?? [],
});

const submitLabel = computed(() => (props.mode === 'create' ? 'Criar Anúncio' : 'Salvar Alterações'));

function submit() {
    if (props.mode === 'create') {
        form.post(route('admin.anuncios.store'));
        return;
    }

    form.put(route('admin.anuncios.update', props.listing?.id));
}
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" content="Gestão de anúncios de produtos" />
    </Head>

    <AuthLayout>
        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-xl border border-border bg-card text-card-foreground shadow-sm">
                    <div class="p-6">
                        <div class="mb-6 flex items-center justify-between">
                            <h1 class="text-2xl font-bold text-foreground">{{ title }}</h1>
                            <Link
                                :href="route('admin.anuncio.config')"
                                class="rounded-md border border-border px-4 py-2 text-sm font-medium text-foreground transition hover:bg-accent/40"
                            >
                                Voltar para anúncios
                            </Link>
                        </div>

                        <form class="space-y-6" @submit.prevent="submit">
                            <div>
                                <label for="produto_id" class="nexa-form-label mb-2 block font-semibold">Produto base do anúncio</label>
                                <select
                                    id="produto_id"
                                    v-model="form.produto_id"
                                    class="w-full rounded border border-gray-300 bg-card px-4 py-2 text-foreground focus:ring-2 focus:ring-green-500 focus:outline-none"
                                >
                                    <option :value="null" disabled>Selecione um produto</option>
                                    <option v-for="produto in produtos" :key="produto.id" :value="produto.id">
                                        #{{ produto.id }} - {{ produto.nome }} (estoque: {{ produto.estoque }}, anúncios: {{ produto.listings_count }})
                                    </option>
                                </select>
                                <p v-if="form.errors.produto_id" class="mt-1 text-sm text-red-600">{{ form.errors.produto_id }}</p>
                            </div>

                            <div>
                                <label class="nexa-form-label mb-2 block font-semibold">Tipos de anúncio</label>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label
                                        v-for="(label, key) in anuncioTipos"
                                        :key="key"
                                        class="flex items-center gap-2 rounded border border-border bg-card px-3 py-2 text-sm text-foreground"
                                    >
                                        <input
                                            :checked="form.anuncio_tipos.includes(key)"
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-border"
                                            @change="(event) => {
                                                const checked = (event.target as HTMLInputElement).checked;
                                                if (checked && !form.anuncio_tipos.includes(key)) {
                                                    form.anuncio_tipos.push(key);
                                                }
                                                if (!checked) {
                                                    form.anuncio_tipos = form.anuncio_tipos.filter((item) => item !== key);
                                                }
                                            }"
                                        />
                                        {{ label }}
                                    </label>
                                </div>
                                <p v-if="form.errors.anuncio_tipos" class="mt-1 text-sm text-red-600">{{ form.errors.anuncio_tipos }}</p>
                            </div>

                            <div>
                                <label for="anuncio_status" class="nexa-form-label mb-2 block font-semibold">Observações do anúncio</label>
                                <textarea
                                    id="anuncio_status"
                                    v-model="form.anuncio_status"
                                    rows="3"
                                    class="w-full rounded border border-gray-300 bg-card px-4 py-2 text-foreground focus:ring-2 focus:ring-green-500 focus:outline-none"
                                    placeholder="Ex: campanha de lançamento, foco em WhatsApp"
                                />
                                <p v-if="form.errors.anuncio_status" class="mt-1 text-sm text-red-600">{{ form.errors.anuncio_status }}</p>
                            </div>

                            <div class="flex items-center gap-3">
                                <input id="anuncio_ativo" v-model="form.anuncio_ativo" type="checkbox" class="h-4 w-4 rounded border-border" />
                                <label for="anuncio_ativo" class="nexa-form-label text-sm font-semibold">
                                    {{ form.anuncio_ativo ? 'Anúncio ativo' : 'Anúncio inativo' }}
                                </label>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded bg-green-600 px-6 py-2 font-semibold text-white transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    {{ submitLabel }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
