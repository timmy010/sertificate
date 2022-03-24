<?php

require_once (__DIR__.'/crest.php');

$deals = CRest::call(
    'crm.deal.list',
    [
            'FILTER[CATEGORY_ID]' => 9,
    ]);

// $deals = CRest::call(
//     'crm.deal.list',
//     [
//         'order' => [
//             'STAGE_ID'   => 'ASC'
//          ],
//           'filter'   => [
//             '=UF_CRM_1631183803'   => '/./'
//          ],
//     ]);
?>

<script>
    const deals = <?= json_encode($deals);?>;
</script>