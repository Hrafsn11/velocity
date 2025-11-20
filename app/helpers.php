<?php

if (!function_exists('app_config')) {
    /**
     * Get application configuration value.
     *
     * @param string $key
     * @return string
     */
    function app_config($key)
    {
        // Default config values
        $config = [
            'app_name' => 'Admin Starter',
            'app_logo' => null,
            'primary_hex' => '#7367f0',
            'secondary_hex' => '#82868b',
        ];

        // Get stored config from database
        try {
            $stored_config = App\Models\Config::all()->pluck('config_value', 'config_name');
            $config = array_merge($config, $stored_config->toArray());
        } catch (\Exception $e) {
            // If database not ready, use defaults
        }

        // Generate color variations using hex2hsl
        if (isset($config['primary_color'])) {
            $config['primary_hex'] = $config['primary_color'];
        }
        if (isset($config['secondary_color'])) {
            $config['secondary_hex'] = $config['secondary_color'];
        }

        $config['primary_color'] = hex2hsl($config['primary_hex']);
        $config['primary_color_label'] = hex2hsl($config['primary_hex'], -15, 20);
        $config['primary_color_hover'] = hex2hsl($config['primary_hex'], -15);
        $config['primary_color_shadow'] = hex2hsl($config['primary_hex'], -15, 50);

        $config['secondary_color'] = hex2hsl($config['secondary_hex']);
        $config['secondary_color_label'] = hex2hsl($config['secondary_hex'], -15, 20);
        $config['secondary_color_hover'] = hex2hsl($config['secondary_hex'], -15);
        $config['secondary_color_shadow'] = hex2hsl($config['secondary_hex'], -15, 50);

        return $config[$key] ?? "";
    }
}

if (!function_exists('hex2hsl')) {
    /**
     * Convert a hex color to hsl.
     *
     * @param string|array $RGB hex color value
     * @param int $ladj lightness adjustment
     * @param int $oadj opacity adjustment
     * @return string
     */
    function hex2hsl($RGB, $ladj = 0, $oadj = 0)
    {
        // Have we got an RGB array or a string of hex RGB values
        if (!is_array($RGB)) {
            $hexstr = ltrim($RGB, '#');
            if (strlen($hexstr) == 3) {
                $hexstr = $hexstr[0] . $hexstr[0] . $hexstr[1] . $hexstr[1] . $hexstr[2] . $hexstr[2];
            }
            $R = hexdec($hexstr[0] . $hexstr[1]);
            $G = hexdec($hexstr[2] . $hexstr[3]);
            $B = hexdec($hexstr[4] . $hexstr[5]);
            $RGB = array($R, $G, $B);
        }

        // Scale the RGB values to 0 to 1 (percentages)
        $r = $RGB[0] / 255;
        $g = $RGB[1] / 255;
        $b = $RGB[2] / 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        // Lightness calculation. 0 to 1 value, scale to 0 to 100% at end
        $l = ($max + $min) / 2;

        // Saturation calculation. Also 0 to 1, scale to percent at end.
        $d = $max - $min;
        if ($d == 0) {
            // Achromatic (grey) so hue and saturation both zero
            $h = $s = 0;
        } else {
            $s = $d / (1 - abs((2 * $l) - 1));
            // Hue (if not grey) This is being calculated directly in degrees (0 to 360)
            switch ($max) {
                case $r:
                    $h = 60 * fmod((($g - $b) / $d), 6);
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;
                case $g:
                    $h = 60 * (($b - $r) / $d + 2);
                    break;
                case $b:
                    $h = 60 * (($r - $g) / $d + 4);
                    break;
            }
        }

        // Make any lightness adjustment required
        if ($ladj > 0) {
            $l += (1 - $l) * $ladj / 100;
        } elseif ($ladj < 0) {
            $l += $l * $ladj / 100;
        }

        // Put the values in an array and scale the saturation and lightness to be percentages
        $hsl = array(round($h), round($s * 100), round($l * 100));

        // Build a CSS compatible string
        $hslstr = 'hsl(' . $hsl[0] . ',' . $hsl[1] . '%,' . $hsl[2] . '%)';

        // Adjust opacity and turn into hsla
        if ($oadj != 0) {
            $hslstr = 'hsla(' . $hsl[0] . ',' . $hsl[1] . '%,' . $hsl[2] . '%,' . ($oadj / 100) . ')';
        }

        return $hslstr;
    }
}
