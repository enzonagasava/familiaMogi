<script setup lang="ts">
import AnuncioForm from '@/components/admin/ecommerce/anuncios/AnuncioForm.vue';

defineProps<{
    produtos: Array<{
        id: number;
        nome: string;
        estoque: number;
        listings_count: number;
        imageUrl?: string | null;
    }>;
    anuncioTipos: Record<string, string>;
    selectedProdutoId?: number | null;
}>();
</script>

<template>
    <AnuncioForm
        title="Novo Anúncio"
        mode="create"
        :produtos="produtos"
        :anuncio-tipos="anuncioTipos"
        :selected-produto-id="selectedProdutoId ?? null"
    />
</template>
