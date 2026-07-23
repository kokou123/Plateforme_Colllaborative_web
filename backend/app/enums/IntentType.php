<?php

namespace App\Enums;

enum IntentType: string
{
    case PROJECT_SEARCH = 'PROJECT_SEARCH';

    case DOCUMENT_SEARCH = 'DOCUMENT_SEARCH';

    case TASK_SEARCH = 'TASK_SEARCH';

    case PROCESS_SEARCH = 'PROCESS_SEARCH';

    case REPORT = 'REPORT';

    case SUMMARY = 'SUMMARY';

    case SUGGESTION = 'SUGGESTION';

    case UNKNOWN = 'UNKNOWN';
}