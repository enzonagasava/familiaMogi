<script setup lang="ts">
interface Props {
    title: string;
    description?: string;
}

defineProps<Props>();
</script>

<template>
    <header class="mb-4">
        <h2 class="mb-0.5 text-2xl font-bold text-gray-800">{{ title }}</h2>
        <p v-if="description" class="text-sm text-muted-foreground">
            {{ description }}
        </p>
    </header>
</template>
