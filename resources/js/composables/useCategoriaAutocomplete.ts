import { ref } from 'vue'

const CATEGORIAS_PADRAO = [
  'Apartamento',
  'Casa',
  'Cobertura',
  'Terreno',
  'Comercial',
]

export function useCategoriaAutocomplete() {
  const filteredCategorias = ref<string[]>([])

  function updateSearch(term: string) {
    const value = (term ?? '').trim().toLowerCase()
    if (!value) {
      filteredCategorias.value = []
      return
    }

    filteredCategorias.value = CATEGORIAS_PADRAO.filter((categoria) =>
      categoria.toLowerCase().includes(value),
    )
  }

  return {
    filteredCategorias,
    updateSearch,
  }
}
