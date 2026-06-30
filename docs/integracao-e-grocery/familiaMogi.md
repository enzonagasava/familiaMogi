# Integracao E-grocery -> familiaMogi (v1)

Data de referencia: 27/04/2026

## 1. Mapa de endpoints (E-grocery)

Base path: `/api/v1`

Autenticacao service-to-service:
- Header `Authorization: Bearer <token>`
- Middleware aplicado: `api.token`
- Token configurado em `API_NEXA_AUTH`

Endpoints consumidos pelo `familiaMogi`:
- `GET /api/v1/anuncios`
- `GET /api/v1/produtos`
- `GET /api/v1/produtos/{sku}`
- `GET /api/v1/imagens/{image_id}`
- `POST /api/v1/pedidos`

## 2. Contratos de payload

### 2.1 `GET /api/v1/anuncios`

Query params suportados:
- `updated_since` (ISO-8601, opcional)
- `status` (`active|inactive`, opcional)
- `cursor` (opcional)
- `per_page` (1..100, opcional)

Resposta:
```json
{
  "data": [
    {
      "id": "ad_123",
      "title": "Oferta de fim de semana",
      "description": "Descontos em hortifruti",
      "status": "active",
      "priority": 10,
      "starts_at": "2026-04-27T09:00:00Z",
      "ends_at": null,
      "updated_at": "2026-04-27T10:22:11Z"
    }
  ],
  "meta": {
    "next_cursor": "..."
  }
}
```

### 2.2 `GET /api/v1/produtos`

Query params suportados:
- `updated_since` (ISO-8601, opcional)
- `active` (`true|false`, opcional)
- `cursor` (opcional)
- `per_page` (1..100, opcional)

Resposta:
```json
{
  "data": [
    {
      "sku": "1",
      "name": "Arroz Tipo 1 5kg",
      "category": "Geral",
      "price": 29.9,
      "stock": 85,
      "status": "active",
      "image_id": "img_10",
      "updated_at": "2026-04-27T10:20:00Z"
    }
  ],
  "meta": {
    "next_cursor": null
  }
}
```

### 2.3 `GET /api/v1/produtos/{sku}`

Resposta inclui dados para PDP:
- `description_long`
- `weights`
- `variations`
- `images`

### 2.4 `GET /api/v1/imagens/{image_id}`

Resposta:
```json
{
  "id": "img_10",
  "storage_key": "produtos/teste.jpg",
  "url": "https://.../storage/produtos/teste.jpg",
  "mime_type": "image/jpeg",
  "width": 1200,
  "height": 1200,
  "checksum": "sha256:..."
}
```

### 2.5 `POST /api/v1/pedidos`

Request esperado (resumo):
- `external_order_id` (opcional; gerado se ausente)
- `customer` (obrigatorio)
- `items` (obrigatorio)
- `totals.grand_total` (obrigatorio)

Idempotencia:
- Chave: `external_order_id`
- Reenvio com mesmo `external_order_id` retorna `200` com o mesmo payload de resposta

Resposta de sucesso:
```json
{
  "order_id": "eg-550198",
  "status": "received",
  "received_at": "2026-04-27T12:30:11Z"
}
```

Formato de erro:
```json
{
  "message": "...",
  "code": "..."
}
```

## 3. Emissao de webhook (E-grocery -> familiaMogi)

Destino:
- `POST {FAMILIA_MOGI_BASE_URL}/api/v1/integrations/e-grocery/webhooks`

Eventos publicados:
- `ad.created`
- `ad.updated`
- `ad.deleted`
- `product.updated`
- `price.updated`
- `stock.updated`
- `image.updated`
- `image.deleted`
- `order.created`
- `order.paid`
- `order.cancelled`
- `order.fulfilled`

Headers enviados:
- `X-Event-Id`
- `X-Event-Type`
- `X-Event-Time`
- `X-Signature`

Assinatura:
- `X-Signature = HMAC_SHA256(raw_body, FAMILIA_MOGI_WEBHOOK_SECRET)`

Payload padrao:
```json
{
  "event_id": "uuid",
  "event_type": "product.updated",
  "occurred_at": "2026-04-27T12:36:01Z",
  "source": "nexaSystem_E-grocery",
  "entity": {
    "type": "product",
    "id": "1",
    "version": 14
  },
  "data": {}
}
```

## 4. Politica de retry e idempotencia

Retry configurado no job de envio:
- tentativa inicial
- retries com backoff: `1m`, `5m`, `15m`, `1h`

Idempotencia de webhook:
- `event_id` unico em `e_grocery_webhook_events`
- novo enqueue do mesmo `event_id` nao cria novo evento

Observabilidade:
- Tabela de auditoria outbound: `e_grocery_webhook_events`
- Tabela de idempotencia inbound: `e_grocery_order_imports`
- Log channel: `familia_mogi_integration`

## 5. Checklist de deploy/homologacao com familiaMogi

1. Configurar variaveis de ambiente no E-grocery:
- `API_NEXA_AUTH`
- `FAMILIA_MOGI_BASE_URL`
- `FAMILIA_MOGI_API_TOKEN` (opcional)
- `FAMILIA_MOGI_WEBHOOK_SECRET`
- `FAMILIA_MOGI_WEBHOOK_PATH` (default `/api/v1/integrations/e-grocery/webhooks`)
- `FAMILIA_MOGI_TIMEOUT_SECONDS`

2. Rodar migrations no banco tenant content:
- inclui `e_grocery_order_imports`
- inclui `e_grocery_webhook_events`

3. Validar autenticação API:
- chamar `GET /api/v1/produtos` com Bearer token valido
- confirmar `401` sem token

4. Validar contratos de payload:
- testar os 5 endpoints `/api/v1` com payload esperado

5. Validar idempotencia de pedido:
- enviar `POST /api/v1/pedidos` 2x com mesmo `external_order_id`
- confirmar apenas um registro em `e_grocery_order_imports`

6. Validar recepção no familiaMogi:
- confirmar assinatura HMAC (`X-Signature`)
- confirmar idempotencia por `event_id` no receptor

7. Validar retries:
- simular indisponibilidade do webhook receptor
- confirmar retentativas no backoff 1m/5m/15m/1h

8. Homologacao final:
- criar/editar/remover anuncio
- atualizar produto/preco/estoque/imagem
- criar pedido e transicionar status
- verificar recebimento dos 12 tipos de evento no familiaMogi
