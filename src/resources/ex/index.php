<?php
require_once (__DIR__.'/crest.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
	{
        if (isset($_POST["fio"]))
            {
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
                if (preg_match('/[а-яА-ЯёЁ]/ui', $_POST['fio'])) { // Поиск по названию сделки
                    while ($i <= $deals[total]) {
                        foreach ($deals[result] as $subArr) {
                            if ($subArr['STAGE_ID'] == 'C9:FINAL_INVOICE' || $subArr['STAGE_ID'] == 'C9:WON') {
                                if(preg_match('/(\W|^)'. $fio .'(\W|$)/ui', $subArr[TITLE])) {
                                    $findDeals[] = $subArr;
                                }
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
                } elseif (preg_match('/^[0-9]{13}$/ui', $_POST['fio'])) { // Поиск по ОГРН
                    while ($i <= $deals[total]) {
                        foreach ($deals[result] as $subArr) {
                            if ($subArr['STAGE_ID'] == 'C9:FINAL_INVOICE' || $subArr['STAGE_ID'] == 'C9:WON') {
                                $companyId = $subArr['COMPANY_ID'];

                                if ($companyId != 0) {
                                    $reqList = CRest::call(
                                        'crm.requisite.list', // Запрос списка реквизитов (в цикле)
                                        [
                                            'FILTER[CATEGORY_ID]' => $companyId,
                                        ]);
                                    $reqOgrn = $reqList['result'][0]['RQ_OGRN'];
                                    if ($reqOgrn != 0) {
                                        $findDeals[] = $subArr;
                                    }
                                }
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
                            'ID' => "823",
                    ]);

                $specList = $specField[result]['LIST'];

                $dealsObjects = array();

                foreach($dealsFull as $deal) {
                    $spec = [];

                    $dateStart = date("d.m.Y",strtotime($deal[result][UF_CRM_1631183840]));
                    $dateEnd = date("d.m.Y",strtotime($deal[result][UF_CRM_1631183942]));

                    foreach ($specList as $specItem) {
                        $dealSpecList = $deal[result][UF_CRM_1649775779];
                        foreach ($dealSpecList as $specListItem) {
                            if ($specListItem == $specItem[ID]) {
                                $spec[] = $specItem[VALUE];
                            }
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

                echo json_encode($dealsObjects, JSON_UNESCAPED_UNICODE);
            }
    }
