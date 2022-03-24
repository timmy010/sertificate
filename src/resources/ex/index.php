<?php

require_once (__DIR__.'/crest.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
	{
        if (isset($_POST["fio"]))
            {
                if ($_POST['fio'] == '')
                {
                    echo 'Не заполнено поле ФИО';
                    return;
                }
                $fio = $_POST['fio'];

                $start = 1;

                $deals = CRest::call( //Запрос списка сделок (первый)
                    'crm.deal.list',
                    [
                            'FILTER[CATEGORY_ID]' => 9,
                            'start' => $start,
                    ]);

                $findDeals = array();
                $i = 1;
                while ($i <= $deals[total]) {
                    foreach ($deals[result] as $subArr) {
                        if(preg_match('/'. $fio .'/ui', $subArr[TITLE])) {
                            $findDeals[] = $subArr;
                        } else {
                        }
                    }
                    $i = $i + 49;
                    $start = $deals[next];
                    $deals = CRest::call(
                        'crm.deal.list', // Запрос списка сделок (в цикле)
                        [
                                'FILTER[CATEGORY_ID]' => 9,
                                'start' => $start,
                        ]);
                }

                // Получение полных данных о сделках

                $dealsFull = array();

                foreach ($findDeals as $deal) {
                    $dealsFull[] = CRest::call(
                        'crm.deal.get',
                        [
                                'ID' => $deal[ID],
                        ]);
                }

                $specField = CRest::call(
                    'crm.deal.userfield.get',
                    [
                            'ID' => "516",
                    ]);

                $specList = $specField[result]['LIST'];

                $dealsObjects = array();

                foreach($dealsFull as $deal) {
                    $spec = null;

                    $dateStart = date("d.m.Y",strtotime($deal[result][UF_CRM_1631183840]));
                    $dateEnd = date("d.m.Y",strtotime($deal[result][UF_CRM_1631183942]));

                    foreach ($specList as $specItem) {
                        if ($specItem[ID] == $deal[result][UF_CRM_1631185088]) {
                            $spec = $specItem[VALUE];
                        } else {
                            'Специальность не найдена';
                        }
                    }

                    $dealsObjects[] = [
                        "FIO" => $deal[result][UF_CRM_1631183803],
                        "SERT_NUMBER" => $deal[result][UF_CRM_1579599545],
                        "DATE_START" => $dateStart,
                        "DATE_END" => $dateEnd,
                        "SPEC" => $spec,
                    ];
                }
                // echo json_encode($dealsObjects, JSON_UNESCAPED_UNICODE);
            }
    }
