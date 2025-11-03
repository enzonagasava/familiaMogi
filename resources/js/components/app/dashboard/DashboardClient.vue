<script setup lang="ts">
import { ref, reactive } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';

const page = usePage();

const user = reactive({
  name: page.props.user.name || '',
  telefone: page.props.user.telefone || '',
  email: page.props.user.email || '',
});

const novaSenha = ref('');
const confirmarSenha = ref('');
const editando = ref(false);

const activeTab = ref('info');

function salvar() {
  if (novaSenha.value !== confirmarSenha.value) {
    alert('As senhas não conferem!');
    return;
  }

  // Dados a enviar
  const dados = {
    name: user.name,
    telefone: user.telefone,
    email: user.email,
  };

  // Se a senha nova foi preenchida, envia também
  if (novaSenha.value) {
    dados['password'] = novaSenha.value;
    dados['password_confirmation'] = confirmarSenha.value;
  }

  // Envia via Inertia para a rota de atualização (exemplo: 'user.update')
  Inertia.post('/user/update', dados, {
    onSuccess: () => {
      editando.value = false;
      novaSenha.value = '';
      confirmarSenha.value = '';
      alert('Dados atualizados com sucesso!');
    },
    onError: (errors) => {
      alert('Erro ao atualizar: ' + JSON.stringify(errors));
    },
  });
}
</script>

<template>
  <div class="p-6 max-w-6xl mx-auto flex gap-8">
    <!-- Barra vertical de abas -->
    <nav class="flex flex-col w-48 border-r border-gray-300 pr-4">
      <button
        :class="['py-3 px-4 text-left rounded', activeTab === 'info' ? 'bg-blue-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100']"
        @click="activeTab = 'info'"
      >
        Informações Gerais
      </button>
      <button
        :class="['py-3 px-4 text-left rounded mt-2', activeTab === 'endereco' ? 'bg-blue-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100']"
        @click="activeTab = 'endereco'"
      >
        Endereço
      </button>
      <button
        :class="['py-3 px-4 text-left rounded mt-2', activeTab === 'historico' ? 'bg-blue-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100']"
        @click="activeTab = 'historico'"
      >
        Histórico de Compras
      </button>
    </nav>

    <!-- Conteúdo das abas -->
    <section class="flex-1">
      <h1 class="text-2xl font-bold mb-6">Dashboard do Cliente</h1>

    <div  v-if="activeTab === 'info'">
        <h2 class="text-xl font-semibold mb-4">Informações Gerais & Configurar Email e Senha</h2>

        <form @submit.prevent="salvar" class="max-w-md space-y-4">
        <div>
            <label class="block font-medium mb-1" for="nome">Nome:</label>
            <input
            id="nome"
            type="text"
            v-model="user.name"
            class="border rounded p-2 w-full"
            required
            />
        </div>

        <div>
            <label class="block font-medium mb-1" for="telefone">Telefone:</label>
            <input
            id="telefone"
            type="tel"
            v-model="user.telefone"
            class="border rounded p-2 w-full"
            required
            />
        </div>

        <div>
            <label class="block font-medium mb-1" for="email">Email:</label>
            <input
            id="email"
            type="email"
            v-model="user.email"
            class="border rounded p-2 w-full"
            required
            />
        </div>

        <div>
            <label class="block font-medium mb-1" for="novaSenha">Nova senha:</label>
            <input
            id="novaSenha"
            type="password"
            v-model="novaSenha"
            class="border rounded p-2 w-full"
            placeholder="Deixe vazio para não alterar"
            />
        </div>

        <div>
            <label class="block font-medium mb-1" for="confirmarSenha">Confirmar senha:</label>
            <input
            id="confirmarSenha"
            type="password"
            v-model="confirmarSenha"
            class="border rounded p-2 w-full"
            placeholder="Confirme a nova senha"
            />
        </div>

        <div class="flex space-x-4">
            <button
            v-if="!editando"
            type="button"
            class="px-4 py-2 bg-blue-600 text-white rounded"
            @click="editando = true"
            >
            Editar
            </button>

            <button
            v-else
            type="submit"
            class="px-4 py-2 bg-green-600 text-white rounded"
            >
            Salvar
            </button>

            <button
            v-if="editando"
            type="button"
            class="px-4 py-2 bg-gray-400 text-black rounded"
            @click="
                editando = false;
                novaSenha = '';
                confirmarSenha = '';
            "
            >
            Cancelar
            </button>
        </div>
        </form>
    </div>
  
    <div v-if="activeTab === 'endereco'">
        <h2 class="text-xl font-semibold mb-4">Endereço</h2>
        <p><strong>Rua:</strong></p>
        <p><strong>Número:</strong></p>
        <p><strong>Cidade:</strong></p>
        <p><strong>Estado:</strong></p>
        <p><strong>CEP:</strong></p>
      </div>

      <div v-if="activeTab === 'historico'">
        <h2 class="text-xl font-semibold mb-4">Histórico de Compras</h2>
        <table class="w-full border-collapse border border-gray-300">
          <thead>
            <tr class="bg-gray-100">
              <th class="border border-gray-300 p-2 text-left">ID</th>
              <th class="border border-gray-300 p-2 text-left">Produto</th>
              <th class="border border-gray-300 p-2 text-left">Data</th>
              <th class="border border-gray-300 p-2 text-left">Valor (R$)</th>
            </tr>
          </thead>
          <tbody>
            <tr >
              <td class="border border-gray-300 p-2"></td>
              <td class="border border-gray-300 p-2"></td>
              <td class="border border-gray-300 p-2"></td>
              <td class="border border-gray-300 p-2"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<style scoped>
/* Você pode ajustar estilos adicionais aqui */
</style>
