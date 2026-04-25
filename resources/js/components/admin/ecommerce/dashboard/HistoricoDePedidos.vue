  <script setup lang="ts">
import { Button } from '@/components/ui/button';
const {
    historico,
    carregarPagina
} = defineProps<{
    historico: {
        data: Array<any>;
        current_page: number;
        last_page: number;
        next_page_url: string | null;
        prev_page_url: string | null;
    };
    carregarPagina: (url: string | null) => void;
}>();

  </script>

  <template>
            <div class="block w-full rounded-xl bg-card p-6 text-card-foreground shadow-lg">
              <h2 class="mb-4 text-xl font-semibold text-card-foreground">Histórico das Últimas Compras</h2>

              <div class="w-full overflow-x-auto">
              <table class="w-full min-w-full table-fixed border-collapse border border-border bg-card text-card-foreground divide-y divide-border">
                  <thead class="w-full border-b border-border bg-card">
                      <tr class="w-full">
                          <th class="w-1/5 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Cliente</th>
                          <th class="w-1/5 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Produtos</th>
                          <th class="w-1/5 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Valor</th>
                          <th class="w-1/5 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Status</th>
                          <th class="w-1/5 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Data</th>
                      </tr>
                  </thead>

                  <tbody v-if="historico && historico.data" class="divide-y divide-border bg-card">
                      <tr v-for="(item, index) in historico.data" :key="index" class="transition-colors hover:bg-accent/40">

                          <td class="px-4 py-3">
                              {{ item.cliente }}
                              <div class="text-xs text-muted-foreground">
                                  {{ item.itens }} itens • {{ item.tempo }}
                              </div>
                          </td>

                          <td class="px-4 py-3">
                              {{ item.produtos }}
                          </td>

                          <td class="px-4 py-3">
                              R$ {{ Number(item.valor).toFixed(2) }}
                          </td>

                          <td class="px-4 py-3">
                              <span
                                  class="px-3 py-1 text-xs font-semibold rounded-full"
                                  :class="{
                                      'bg-green-100 text-green-700': item.status === 'Finalizado',
                                      'bg-yellow-100 text-yellow-700': item.status === 'A Caminho',
                                      'bg-blue-100 text-blue-700': item.status === 'Em Andamento',
                                      'bg-purple-100 text-purple-700': item.status === 'Pronto',
                                      'bg-orange-100 text-orange-700': item.status === 'Processando',
                                      'bg-red-100 text-red-700': item.status === 'Cancelado',
                                  }">
                                  {{ item.status }}
                              </span>
                          </td>

                          <td class="px-4 py-3">
                              {{ item.data }}
                          </td>

                      </tr>
                  </tbody>
              </table>
              </div>

              <!-- PAGINAÇÃO -->
              <div class="flex justify-center items-center gap-4 mt-4">

                  <Button
                      @click="carregarPagina(historico.prev_page_url)"
                      :disabled="!historico.prev_page_url"
                      class="rounded bg-primary px-4 py-2 text-primary-foreground hover:bg-primary/90 disabled:opacity-50"
                  >
                      Anterior
                  </Button>

                  <span class="text-sm text-muted-foreground">
                      Página {{ historico.current_page }} de {{ historico.last_page }}
                  </span>

                  <Button
                      @click="carregarPagina(historico.next_page_url)"
                      :disabled="!historico.next_page_url"
                      class="rounded bg-primary px-4 py-2 text-primary-foreground hover:bg-primary/90 disabled:opacity-50"
                  >
                      Próxima
                  </Button>

              </div>

          </div>
  </template>
