<?php

namespace App\Service;

class BadWordFilter
{
    private const BAD_WORDS = [
        // English bad words
        'fuck', 'shit', 'bitch', 'asshole', 'bastard', 'damn', 'crap', 'dick', 'pussy',
        'cock', 'motherfucker', 'whore', 'slut', 'cunt', 'faggot', 'nigger', 'retard',

        // French bad words
        'merde', 'putain', 'salope', 'connard', 'enculé', 'bâtard', 'nique', 'bite',
        'couille', 'pd', 'bordel', 'ta gueule', 'fdp', 'trou du cul', 'chiotte'
    ];

    public function containsBadWords(string $text): bool
    {
        $lowerCaseText = mb_strtolower($text);
        
        foreach (self::BAD_WORDS as $badWord) {
            if (str_contains($lowerCaseText, $badWord)) {
                return true;
            }
        }
        
        return false;
    }
}