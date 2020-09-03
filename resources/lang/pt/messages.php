<!-- PT -->
<?php

return [

    'crud' => [
        'create' => 'Registro criado com éxito.',
        'read' => 'Registro retornado com éxito',
        'update' => 'Registro atualizado com éxito.',
        'delete' => 'Registro apagado com éxito.',
        'pagination' => 'Registros retornados com éxito.'
    ],
    'invoice' => [
        'total-negative' => 'O total é negativo.',
        'anull' => 'Fatura anulada com éxito.',
        'detail-not-found' => 'Detalhe da fatura não achado.',
        'already-anulled' => 'A Fatura já foi anulada.',
        'detail-gt-subtotal' => 'O Detalhe da fatura é maior o que o subtotal.',
        'out-date' => 'A fatura está fora do intervalo de datas.',
    ],

    'item' => [
        'has-stock' => 'O item tem stock, não pode ser modificado.',
        'has-no-stock' => 'O item não tem stock.',
    ],

    'identification' => [
        'length' => 'Sua identificação deve ter :digits caractere acorde o pais de sua empresa.',
    ],

    'phone' => [
        'format' => 'O formato do telefone não está certo acorde o pais de sua empresa.',
    ],

];