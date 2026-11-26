<?php
/** Loose check that a string looks like a UK postcode. */

function calculateResponseTime(DateTime $created, DateTime $responded): float
{
    $diffInSeconds = $responded->getTimestamp() - $created->getTimestamp();
    return round($diffInSeconds / 60, 2);
}


function isValidPostcode(string $postcode): bool
{
    // Standard UK postcode regex (case-insensitive, optional space)
    $pattern = '/^[A-Z]{1,2}[0-9][A-Z0-9]?\s?[0-9][A-Z]{2}$/i';
    return preg_match($pattern, trim($postcode)) === 1;
}

/** Return a list of error messages for ticket data (empty list = valid). */
function validateTicketInput(array $input): array
{
   $errors = [];

    $title = trim($input['title'] ?? '');
    if ($title === '') {
        $errors[] = 'Title is required.';
    } elseif (mb_strlen($title) > 100) {
        $errors[] = 'Title must be 100 characters or fewer.';
    }

    if (trim($input['description'] ?? '') === '') {
        $errors[] = 'Description is required.';
    }

    $validPriorities = ['low', 'medium', 'high'];
    if (!in_array($input['priority'] ?? '', $validPriorities, true)) {
        $errors[] = 'Invalid priority.';
    }

    return $errors;
}