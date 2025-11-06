<script setup lang="ts">
import { reactive, watch } from 'vue'
import Button from '@/components/ui/button/Button.vue'
import { Inertia } from '@inertiajs/inertia'
import { vMaska } from 'maska/vue'
import HeadingSmall from '@/components/ui/header/HeadingSmall.vue'

interface Cliente {
  id?: number
  nome: string
  numero: string
  email: string
  cep: string
  endereco: string
  numero_endereco: string
  municipio: string
  estado: string
}

const props = defineProps<{
  cliente?: Cliente
  isEditing?: boolean
}>()

const cliente = reactive<Cliente>({
  id: props.cliente?.id ?? undefined,
  nome: props.cliente?.nome ?? '',
  numero: props.cliente?.numero ?? '',
  email: props.cliente?.email ?? '',
  cep: props.cliente?.cep ?? '',
  endereco: props.cliente?.endereco ?? '',
  numero_endereco: props.cliente?.numero_endereco ?? '',
  municipio: props.cliente?.municipio ?? '',
  estado: props.cliente?.estado ?? '',
})

watch(
  () => props.cliente,
  (novo) => {
    if (novo) Object.assign(cliente, novo)
  }
)

function handleSubmit() {
  const formData = new FormData()
  Object.entries(cliente).forEach(([key, value]) => {
    if (value !== undefined && value !== null) {
      formData.append(key, value.toString())
    }
  })

  if (props.isEditing && cliente.id) {
    // Atualizar
    Inertia.post(`/clientes/atualizarCliente/${cliente.id}`, formData, {
      forceFormData: true,
      onSuccess: () => alert('Cliente atualizado com sucesso!'),
      onError: (errors) => console.error(errors),
    })
  } else {
    // Criar
    Inertia.post('/clientes/adicionarCliente', formData, {
      forceFormData: true,
      onSuccess: () => {
        alert('Cliente salvo com sucesso!')
        Object.keys(cliente).forEach((key) => (cliente[key] = ''))
      },
      onError: (errors) => console.error(errors),
    })
  }
}
</script>

<template>
    <div class="flex min-h-screen bg-gray-100">
      <!-- Main Content -->
      <main class="flex-1 p-10">
        <div class="mx-auto bg-white rounded shadow p-8">
            <HeadingSmall title='Adicionar Novo Cliente'/>

            <form @submit.prevent="handleSubmit" class="grid gap-6">
            <!-- Linha 1 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                <label for="nome" class="block font-semibold mb-2 text-gray-700">Nome do Cliente</label>
                <input
                    id="nome"
                    v-model="cliente.nome"
                    type="text"
                    placeholder="Digite o nome do cliente"
                    class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                />
                </div>

                <div>
                <label for="numero" class="block font-semibold mb-2 text-gray-700">Número</label>
                <input
                    id="numero"
                    v-model="cliente.numero"
                    v-maska="'(##) #####-####'"
                    type="text"
                    placeholder="Ex: (11) 23456-7890"
                    class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                />
                </div>

                <div>
                <label for="email" class="block font-semibold mb-2 text-gray-700">E-mail</label>
                <input
                    id="email"
                    v-model="cliente.email"
                    type="email"
                    placeholder="Ex: cliente@email.com"
                    class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                />
                </div>
            </div>

            <!-- Linha 2 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                <label for="cep" class="block font-semibold mb-2 text-gray-700">CEP</label>
                <input
                    id="cep"
                    v-model="cliente.cep"
                    v-maska="'#####-###'"
                    type="text"
                    placeholder="Ex: 00000-000"
                    class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                />
                </div>

                <div class="md:col-span-2">
                <label for="endereco" class="block font-semibold mb-2 text-gray-700">Endereço</label>
                <input
                    id="endereco"
                    v-model="cliente.endereco"
                    type="text"
                    placeholder="Digite o endereço do cliente"
                    class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                />
                </div>
            </div>

            <!-- Linha 3 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                <label for="numero_endereco" class="block font-semibold mb-2 text-gray-700">Número do Endereço</label>
                <input
                    id="numero_endereco"
                    v-model="cliente.numero_endereco"
                    type="text"
                    v-maska="'########'"
                    placeholder="Digite o número do endereço"
                    class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                />
                </div>

                <div>
                <label for="municipio" class="block font-semibold mb-2 text-gray-700">Município</label>
                <input
                    id="municipio"
                    v-model="cliente.municipio"
                    type="text"
                    placeholder="Digite o município do cliente"
                    class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                />
                </div>

                <div>
                <label for="estado" class="block font-semibold mb-2 text-gray-700">UF</label>
                <input
                    id="estado"
                    v-model="cliente.estado"
                    type="text"
                    placeholder="Digite o estado do cliente"
                    class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                />
                </div>
            </div>

            <div class="flex justify-end">
                <Button
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded transition"
                >
                Salvar Cliente
                </Button>
            </div>
            </form>
        </div>
      </main>
    </div>
</template>
