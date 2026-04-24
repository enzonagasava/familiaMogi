<script setup lang="ts">
import EcommerceNavigation from '@/components/layouts/navigation/EcommerceNavigation.vue'
</script>

<template>
  <div role="navigation" aria-label="Sidebar navigation">
    <EcommerceNavigation @close="$emit('close')" />
  </div>
</template>
