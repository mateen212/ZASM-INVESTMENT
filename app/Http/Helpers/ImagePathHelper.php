<?php

if (!function_exists('imagePath')) {
    function imagePath()
    {
        $data = [
            'profile' => [
                'admin' => [
                    'path' => 'assets/admin/images/profile',
                    'size' => '400x400',
                ],
                'user' => [
                    'path' => 'assets/images/user/profile',
                    'size' => '400x400',
                ],
                'partner' => [
                    'path' => 'assets/images/partner/profile',
                    'size' => '400x400',
                ]
            ],
            'logo' => [
                'path' => 'assets/images/logoIcon',
            ],
        ];
        return $data;
    }
}
