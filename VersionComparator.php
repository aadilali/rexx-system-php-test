<?php


class VersionComparator {
    public static function compare($version1, $version2) {
        // Split versions into parts
        $parts1 = explode('+', $version1);
        $parts2 = explode('+', $version2);

        // Extract the numeric part for comparison
        $numericPart1 = $parts1[0];
        $numericPart2 = $parts2[0];

        // Compare numeric parts as integers
        $numericPart1 = intval(str_replace('.', '', $numericPart1));
        $numericPart2 = intval(str_replace('.', '', $numericPart2));

        if ($numericPart1 > $numericPart2) {
            return 1;
        } elseif ($numericPart1 < $numericPart2) {
            return -1;
        } else {
            // If numeric parts are equal, compare the "+X" part
            if (isset($parts1[1]) && isset($parts2[1])) {
                $extraPart1 = intval($parts1[1]);
                $extraPart2 = intval($parts2[1]);

                if ($extraPart1 > $extraPart2) {
                    return 1;
                } elseif ($extraPart1 < $extraPart2) {
                    return -1;
                }
            }

            return 0;
        }
    }
}