<?php

namespace App\Classes\eHealth\Errors;

class ErrorHandler
{
    public function handleError(array $error)
    {
        $errorMessages = [];

        if (isset($error['error']['invalid']) && is_array($error['error']['invalid'])) {
            foreach ($error['error']['invalid'] as $invalidEntry) {
                $entry = $invalidEntry['entry'] ?? 'Unknown';
                $entryType = $invalidEntry['entry_type'] ?? 'Unknown';
                $rules = $invalidEntry['rules'] ?? [];
                foreach ($rules as $rule) {
                    $description = $rule['description'] ?? 'No description';
                    $errorMessages[] = "Error in entry '{$entry}' (Type: {$entryType}): {$description}";
                }
            }
        } else {
            $errorMessages[] = "No valid error information provided.";
        }

        return (['errors' => $errorMessages]);
    }
}
