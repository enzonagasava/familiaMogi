<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface ListingItem {
    id: number;
    produto_id: number | null;
    anuncio_ativo: boolean;
    anuncio_status?: string | null;
    anuncio_tipos: string[];
    created_at?: string | null;
    produto: {
        id: number;
        nome: string;
        descricao: string;
        estoque: number;
        imageUrl?: string | null;
    } | null;
}

const page = usePage();

const listings = computed<ListingItem[]>(() => (Array.isArray(page.props.listings) ? (page.props.listings as ListingItem[]) : []));
const anuncioTiposLabels = computed<Record<string, string>>(() => {
    const payload = page.props.anuncioTiposLabels;
    return payload && typeof payload === 'object' ? (payload as Record<string, string>) : {};
});

function formatTipos(tipos: string[]) {
    if (!tipos?.length) {
        return 'Sem tipo';
    }

    return tipos.map((tipo) => anuncioTiposLabels.value[tipo] ?? tipo).join(', ');
}

function removeListing(id: number) {
    if (!confirm('Deseja excluir este anúncio?')) {
        return;
    }

    router.delete(route('admin.anuncios.destroy', id));
}
</script>

<template>
    <Head>
        <title>Gerenciar Anúncios</title>
        <meta name="description" content="Gestão de anúncios de produtos" />
    </Head>

    <AuthLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-xl border border-border bg-card text-card-foreground shadow-sm">
                    <div class="p-6">
                        <div class="mb-6 flex items-center justify-between">
                            <h1 class="text-2xl font-bold text-foreground">Gerenciar Anúncios</h1>
                            <Link
                                :href="route('admin.anuncios.create')"
                                class="rounded-md bg-green-600 px-4 py-2 text-white transition hover:bg-green-700"
                            >
                                + Novo anúncio
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-card">
                                <thead class="border-b border-border bg-card">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Produto</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Tipos</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Criado em</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted-foreground">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border bg-card">
                                    <tr v-for="listing in listings" :key="listing.id" class="transition-colors hover:bg-accent/40">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <img
                                                    v-if="listing.produto?.imageUrl"
                                                    :src="listing.produto.imageUrl"
                                                    :alt="listing.produto?.nome"
                                                    class="h-12 w-12 rounded-md object-cover"
                                                />
                                                <div>
                                                    <p class="text-sm font-semibold text-foreground">{{ listing.produto?.nome ?? 'Produto removido' }}</p>
                                                    <p class="text-xs text-muted-foreground">Estoque: {{ listing.produto?.estoque ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-foreground">{{ formatTipos(listing.anuncio_tipos) }}</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                                :class="listing.anuncio_ativo ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'"
                                            >
                                                {{ listing.anuncio_ativo ? 'Ativo' : 'Inativo' }}
                                            </span>
                                            <p v-if="listing.anuncio_status" class="mt-1 text-xs text-muted-foreground">{{ listing.anuncio_status }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-foreground">{{ listing.created_at ?? '-' }}</td>
                                        <td class="px-6 py-4 text-right text-sm whitespace-nowrap">
                                            <Link
                                                :href="route('admin.anuncios.edit', listing.id)"
                                                class="mr-4 text-indigo-600 transition hover:text-indigo-900"
                                            >
                                                Editar
                                            </Link>
                                            <button
                                                type="button"
                                                class="text-red-600 transition hover:text-red-900"
                                                @click="removeListing(listing.id)"
                                            >
                                                Excluir
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="listings.length === 0">
                                        <td colspan="5" class="py-6 text-center text-muted-foreground">Nenhum anúncio cadastrado.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
