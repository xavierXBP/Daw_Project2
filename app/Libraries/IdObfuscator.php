<?php

namespace App\Libraries;

class IdObfuscator
{
    private static $salt = 'CAPARRELLA_2026_SECURE_SALT';
    private static $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    /**
     * Obfuscate an integer ID
     */
    public static function obfuscate(int $id): string
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('ID must be a positive integer');
        }

        // Add salt and encode
        $saltedId = $id . self::$salt . $id;
        $hash = hash('sha256', $saltedId);
        
        // Take first 8 characters and convert to base62
        $hexPart = substr($hash, 0, 8);
        $num = hexdec($hexPart);
        
        return self::toBase62($num + $id);
    }

    /**
     * Deobfuscate an ID back to integer
     */
    public static function deobfuscate(string $obfuscated): int
    {
        if (empty($obfuscated)) {
            throw new \InvalidArgumentException('Obfuscated ID cannot be empty');
        }

        try {
            $num = self::fromBase62($obfuscated);
            
            // Try to find original ID by checking nearby values (more efficient approach)
            for ($i = 0; $i < 100; $i++) {
                $testId = $num - $i;
                if ($testId > 0) {
                    try {
                        $testObfuscated = self::obfuscate($testId);
                        if ($testObfuscated === $obfuscated) {
                            return $testId;
                        }
                    } catch (\Exception $e) {
                        // Continue trying
                        continue;
                    }
                }
            }
            
            // If not found with the simple method, try a broader search
            for ($i = 1; $i <= 1000; $i++) {
                try {
                    $testObfuscated = self::obfuscate($i);
                    if ($testObfuscated === $obfuscated) {
                        return $i;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            throw new \RuntimeException('Unable to deobfuscate ID - no match found');
        } catch (\Exception $e) {
            throw new \RuntimeException('Invalid obfuscated ID format: ' . $e->getMessage());
        }
    }

    /**
     * Convert number to base62
     */
    private static function toBase62(int $num): string
    {
        if ($num === 0) {
            return self::$alphabet[0];
        }

        $result = '';
        $base = strlen(self::$alphabet);

        while ($num > 0) {
            $result = self::$alphabet[$num % $base] . $result;
            $num = floor($num / $base);
        }

        return $result;
    }

    /**
     * Convert base62 string to number
     */
    private static function fromBase62(string $str): int
    {
        $base = strlen(self::$alphabet);
        $num = 0;

        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str[$i];
            $pos = strpos(self::$alphabet, $char);
            
            if ($pos === false) {
                throw new \RuntimeException("Invalid character in base62 string: $char");
            }
            
            $num = $num * $base + $pos;
        }

        return $num;
    }

    /**
     * Generate a secure obfuscated URL segment
     */
    public static function generateUrlSegment(int $id): string
    {
        return self::obfuscate($id);
    }

    /**
     * Extract ID from URL segment
     */
    public static function extractIdFromUrl(string $segment): int
    {
        return self::deobfuscate($segment);
    }
}
