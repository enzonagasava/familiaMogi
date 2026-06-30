import { ref } from 'vue'

const CIDADES_PADRAO = [
  'Sao Paulo',
  'Rio de Janeiro',
  'Belo Horizonte',
  'Curitiba',
  'Porto Alegre',
  'Salvador',
  'Brasilia',
]

export function useCidadeAutocomplete() {
  const filteredCidades = ref<string[]>([])

  function updateSearch(term: string) {
    const value = (term ?? '').trim().toLowerCase()
    if (!value) {
      filteredCidades.value = []
      return
    }

    filteredCidades.value = CIDADES_PADRAO.filter((cidade) =>
      cidade.toLowerCase().includes(value),
    )
  }

  return {
    filteredCidades,
    updateSearch,
  }
}
