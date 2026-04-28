<?php

return [
    'familia_mogi' => [
        'base_url' => rtrim((string) env('FAMILIA_MOGI_BASE_URL', ''), '/'),
        'api_token' => (string) env('FAMILIA_MOGI_API_TOKEN', ''),
        'webhook_secret' => (string) env('FAMILIA_MOGI_WEBHOOK_SECRET', ''),
        'webhook_path' => (string) env('FAMILIA_MOGI_WEBHOOK_PATH', '/api/v1/integrations/e-grocery/webhooks'),
        'timeout_seconds' => (int) env('FAMILIA_MOGI_TIMEOUT_SECONDS', 5),
    ],
];
