# Padrao de Labels de Formulario (Admin)

Data: 2026-04-25

## Objetivo
Padronizar a cor de labels de inputs nas telas admin do E-grocery, garantindo boa leitura no tema claro e no tema escuro.

## Implementacao
Foi criada uma classe global no SCSS:

- Arquivo: `resources/css/_custom.scss`
- Classe: `.nexa-form-label`

Regras aplicadas:

- Tema claro: `color: var(--text-primary)`
- Tema escuro (`.dark`): `color: #ffffff`

## Onde foi aplicado
- `resources/js/components/admin/ecommerce/clientes/ClientesForm.vue`

Como a page `admin/clientes/adicionarCliente` usa esse componente, a tela passou a herdar automaticamente o novo padrao.

## Uso nas proximas telas
Para novos formularios admin, aplicar nos labels:

```html
<label class="nexa-form-label mb-2 block font-semibold">...</label>
```

Evitar hardcode de cor no label (`text-gray-*`) quando houver possibilidade de dark mode.
