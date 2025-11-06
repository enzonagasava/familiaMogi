export function useFormMask() {


  const maskPhone = (value: string) => {
    if (!value) return ''
    value = value.replace(/\D/g, '')
    if (value.length <= 10)
      return value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3')
    return value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3')
  }


  const maskCEP = (value: string) => {
    if (!value) return ''
    return value.replace(/\D/g, '').replace(/(\d{5})(\d)/, '$1-$2')
  }

  return { maskPhone, maskCEP }
}
