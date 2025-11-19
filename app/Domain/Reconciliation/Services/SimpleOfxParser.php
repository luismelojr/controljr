<?php

namespace App\Domain\Reconciliation\Services;

class SimpleOfxParser
{
    public function loadFromString(string $content): array
    {
        // Clean up the content to ensure we can parse it
        // Some OFX files might have encoding issues or mixed line endings
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        
        // Remove header block (before <OFX>)
        if (($pos = strpos($content, '<OFX>')) !== false) {
            $content = substr($content, $pos);
        }

        // Split by STMTTRN to get each transaction block
        // Case insensitive splitting
        $blocks = preg_split('/<STMTTRN>/i', $content);
        
        // If no blocks found, try checking if it's inside BANKTRANLIST without distinct STMTTRN (rare but possible)
        // But standard OFX has STMTTRN.
        
        if (count($blocks) < 2) {
            return [];
        }
        
        // Remove the first block which contains header/metadata
        array_shift($blocks);

        $transactions = [];

        foreach ($blocks as $block) {
            // Stop if we hit the end tag
            if (stripos($block, '</BANKTRANLIST>') !== false) {
                $block = substr($block, 0, stripos($block, '</BANKTRANLIST>'));
            }
            
            $tx = [];
            
            // Extract fields using regex that handles both:
            // <TAG>VALUE (SGML style, no closing tag)
            // <TAG>VALUE</TAG> (XML style)
            
            $tx['type'] = $this->extractTag($block, 'TRNTYPE');
            $tx['date'] = $this->extractTag($block, 'DTPOSTED');
            $tx['amount'] = $this->extractTag($block, 'TRNAMT');
            $tx['unique_id'] = $this->extractTag($block, 'FITID');
            $tx['memo'] = $this->extractTag($block, 'MEMO');
            
            // Skip if essential data is missing
            if (!$tx['date'] || !$tx['amount'] || !$tx['unique_id']) {
                continue;
            }
            
            // Date parsing (OFX format: YYYYMMDDHHMMSS or YYYYMMDD)
            // 20251118100000[-03:EST]
            $dateStr = trim($tx['date']);
            if (strlen($dateStr) >= 8) {
                $tx['date_parsed'] = substr($dateStr, 0, 4) . '-' . substr($dateStr, 4, 2) . '-' . substr($dateStr, 6, 2);
            } else {
                continue; // Invalid date
            }

            $transactions[] = (object) $tx;
        }

        return $transactions;
    }

    private function extractTag(string $block, string $tag): ?string
    {
        // Regex explanation:
        // <TAG>        : Match opening tag
        // \s*          : Optional whitespace
        // (.*?)        : Capture the value (non-greedy)
        // (?=          : Lookahead (don't consume)
        //   <          : Next tag start
        //   |          : OR
        //   \n         : Newline
        //   |          : OR
        //   $          : End of string
        // )
        // Case insensitive (/i)
        
        if (preg_match('/<' . $tag . '>\s*(.*?)(?=<|\n|$)/i', $block, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
}
