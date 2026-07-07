<?php

if (! function_exists('hitung_biaya_admin')) {
    /**
     * Hitung biaya admin berdasarkan total_harga.
     * - <= 20.000.000 => 0.5%
     * - >  20.000.000 => 0.75%
     */
    function hitung_biaya_admin(float $total_harga): float
    {
        $rate = ($total_harga <= 20000000) ? 0.005 : 0.0075;
        return $total_harga * $rate;
    }
}

if (! function_exists('hitung_diskon_kupon')) {
    /**
     * Hitung diskon kupon.
     * Diskon dihitung dari total_harga sebelum biaya admin & cashback.
     */
    function hitung_diskon_kupon(float $total_harga, ?string $kupon_code): float
    {
        $code = strtoupper(trim((string) ($kupon_code ?? '')));

        $diskonRate = 0.0;
        if ($code === 'HEMAT') {
            $diskonRate = 0.15;
        } elseif ($code === 'SUPER') {
            $diskonRate = 0.20;
        }

        return $total_harga * $diskonRate;
    }
}

if (! function_exists('hitung_cashback')) {
    /**
     * Hitung cashback.
     * - > 10.000.000 => 2%
     * - <= 10.000.000 => 0%
     */
    function hitung_cashback(float $total_harga): float
    {
        if ($total_harga > 10000000) {
            return $total_harga * 0.02;
        }
        return 0.0;
    }
}

