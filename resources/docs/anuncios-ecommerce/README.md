# Anuncios no E-grocery (Produto como Base)

Data: 2026-04-25

## Objetivo
Replicar no painel E-grocery a logica de anuncios do painel corretor, usando `Produto` no lugar de `Imovel`.

## O que foi implementado

- CRUD de anuncios no admin ecommerce:
  - Listar anuncios
  - Criar anuncio para um produto
  - Editar anuncio
  - Excluir anuncio
- Campos de anuncio:
  - `produto_id`
  - `anuncio_ativo`
  - `anuncio_status`
  - `anuncio_tipos[]`

## Arquivos principais

### Backend
- `app/Http/Controllers/Admin/Ecommerce/AnuncioController.php`
- `app/Http/Requests/Admin/Ecommerce/StoreAnuncioRequest.php`
- `app/Http/Requests/Admin/Ecommerce/UpdateAnuncioRequest.php`
- `app/Models/Listing.php` (agora suporta `produto()` e `produto_id`)
- `app/Models/Produto.php` (agora possui `listings()`)
- `database/migrations/content/2026_04_25_200000_create_or_update_listings_for_produtos.php`

### Rotas
- `routes/admin.php`
  - `admin.anuncio.config` -> index de anuncios
  - `admin.anuncios.create`
  - `admin.anuncios.store`
  - `admin.anuncios.edit`
  - `admin.anuncios.update`
  - `admin.anuncios.destroy`

### Frontend
- `resources/js/pages/admin/ecommerce/anuncios/AnunciosIndex.vue`
- `resources/js/pages/admin/ecommerce/anuncios/AnunciosCreate.vue`
- `resources/js/pages/admin/ecommerce/anuncios/AnunciosEdit.vue`
- `resources/js/components/admin/ecommerce/anuncios/AnuncioForm.vue`
- `resources/js/pages/admin/ecommerce/produtos/ProdutosConfig.vue` (atalho "Anunciar")

## Fluxo
1. Usuario acessa `Anuncios` no menu do admin ecommerce.
2. Seleciona um produto e define tipos/estado do anuncio.
3. Pode editar status, tipos, ativo/inativo e produto vinculado.
4. Exclusao remove somente o anuncio (nao remove o produto).

## Observacoes de compatibilidade
- O model `Listing` manteve `imovel_id` para nao quebrar legados do modulo corretor.
- O fluxo novo filtra anuncios por `produto_id` no ecommerce.
