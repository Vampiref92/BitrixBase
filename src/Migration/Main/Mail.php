<?php

namespace Vf92\Migration\Main;

use CEventMessage;
use CEventType;
use CSite;
use RuntimeException;

class Mail
{
    public function addEvent(array $event)
    {
        global $APPLICATION;

        $et = new CEventType;
        $em = new CEventMessage();

        $lid = [];

        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $db = CSite::GetList($by = 'sort', $order = 'desc');
        while ($site = $db->Fetch()) {
            $lid[] = $site['ID'];
        }

        $event = array_replace_recursive(
            [
                'LID'         => 'ru',
                'EVENT_NAME'  => '',
                'NAME'        => '',
                'DESCRIPTION' => '',
                'TEMPLATE'    => [
                    'LID'              => $lid,
                    'SITE_TEMPLATE_ID' => '',
                    'EMAIL_TO'         => '#EMAIL#',
                    'ACTIVE'           => 'Y',
                    'EMAIL_FROM'       => '#DEFAULT_EMAIL_FROM#',
                    'BCC'              => '#BCC#',
                    'SUBJECT'          => '',
                    'BODY_TYPE'        => 'html',
                    'MESSAGE'          => '',
                ],
            ],
            $event
        );

        $template = $event['TEMPLATE'];
        $template['EVENT_NAME'] = $event['EVENT_NAME'];
        unset($event['TEMPLATE']);

        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $db = CEventType::GetByID($event['EVENT_NAME'], $event['LID']);
        $tmpEvent = $db->Fetch();

        if ($tmpEvent === false) {
            if (!$res = $et->Add($event)) {
                throw new RuntimeException(
                    sprintf(
                        'Ошибка добавления типа почтового события %s: %s',
                        $event['EVENT_NAME'],
                        $APPLICATION->GetException()->GetString()
                    )
                );
            }
        } else {
            if (!$res = $et->Update(['ID' => $tmpEvent['ID']], $event)) {
                throw new RuntimeException(
                    sprintf(
                        'Ошибка обновления типа почтового события %s: %s',
                        $event['EVENT_NAME'],
                        $APPLICATION->GetException()->GetString()
                    )
                );
            }
        }

        if ($res && $template) {
            /** @noinspection PhpDynamicAsStaticMethodCallInspection */
            $tmpMessage = CEventMessage::GetList($by = 'id', $order = 'asc', ['TYPE' => $event['EVENT_NAME']])->Fetch();
            if ($tmpMessage === false) {
                if (!$em->Add($template)) {
                    throw new RuntimeException(
                        sprintf(
                            'Ошибка добавления шаблона почтового события %s: ',
                            $event['EVENT_NAME'],
                            $em->LAST_ERROR
                        )
                    );
                }
            } else {
                if (!$em->Update($tmpMessage['ID'], $template)) {
                    throw new RuntimeException(
                        sprintf(
                            'Ошибка обновления шаблона почтового события %s: ',
                            $event['EVENT_NAME'],
                            $em->LAST_ERROR
                        )
                    );
                }
            }
        }
    }

}
