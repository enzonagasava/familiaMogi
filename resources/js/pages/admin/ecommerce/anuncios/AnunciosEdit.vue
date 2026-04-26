<script setup lang="ts">
import AnuncioForm from '@/components/admin/ecommerce/anuncios/AnuncioForm.vue';

defineProps<{
    listing: {
        id: number;
        produto_id: number | null;
        anuncio_ativo: boolean;
        anuncio_status: string | null;
        anuncio_tipos: string[];
    };
    produtos: Array<{
        id: number;
        nome: string;
        estoque: number;
        listings_count: number;
        imageUrl?: string | null;
    }>;
    anuncioTipos: Record<string, string>;
}>();
</script>

<template>
    <AnuncioForm title="Editar Anúncio" mode="edit" :produtos="produtos" :anuncio-tipos="anuncioTipos" :listing="listing" />
</template>
