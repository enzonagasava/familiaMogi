<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Home, Calendar, ShoppingCart, List } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps<{ menuOpen?: boolean }>()
const emit = defineEmits<{ (e: 'openSidebar'): void }>()

const dashboardHref = computed(() => route('admin.dashboard'))
const calendarHref = computed(() => route('admin.calendar.index'))

</script>

<template>
  <nav class="fixed bottom-0 left-0 right-0 z-50 lg:hidden border-t border-sidebar-border bg-[color:var(--sidebar-background)] text-[color:var(--text-inverse)]">
    <div class="max-w-5xl mx-auto flex justify-between items-center px-4 py-2">
      <Link :href="dashboardHref" class="flex-1 flex flex-col items-center justify-center py-2 text-sm text-[color:var(--text-inverse)] opacity-90" aria-label="Home">
        <Home class="w-5 h-5" />
        <span class="text-xs">Home</span>
      </Link>

      <Link :href="calendarHref" class="flex-1 flex flex-col items-center justify-center py-2 text-sm text-[color:var(--text-inverse)] opacity-90" aria-label="Calendário">
        <Calendar class="w-5 h-5" />
        <span class="text-xs">Calendário</span>
      </Link>

      <Link :href="route('admin.pedidos.index')" class="flex-1 flex flex-col items-center justify-center py-2 text-sm text-[color:var(--text-inverse)] opacity-90" aria-label="Pedidos">
        <ShoppingCart class="w-5 h-5" />
        <span class="text-xs">Pedidos</span>
      </Link>

      <button
        type="button"
        class="flex-1 flex flex-col items-center justify-center py-2 text-sm cursor-pointer"
        :class="props.menuOpen ? 'text-primary' : 'text-[color:var(--text-inverse)] opacity-90'"
        aria-label="Menu"
        @click="emit('openSidebar')"
      >
        <List class="w-5 h-5" />
        <span class="text-xs">Menu</span>
      </button>
    </div>
  </nav>
</template>

<style scoped>
nav { box-shadow: 0 -1px 10px rgba(0,0,0,0.04);} 
</style>
