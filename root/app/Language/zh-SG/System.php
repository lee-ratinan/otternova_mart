<?php
return [
    'site-title' => 'OtterNovaMart',
    'page'       => [
        'home'             => '主页',
        'search'           => '搜索结果',
        'terms-of-service' => '服务条款',
        'privacy-policy'   => '隐私政策',
    ],
    // the search for shops (Home::searchResults()) and the header's search box
    'search'     => [
        'submit'       => '搜索',
        'label'        => '搜索商店',
        'placeholder'  => '按名称或提供的服务搜索商店…',
        'title-for'    => '“{0}”的搜索结果',
        'prompt'       => '搜索商店',
        'prompt-help'  => '输入商店名称，或您要找的东西，例如“泰式按摩”。',
        'none'         => '找不到与“{0}”相符的商店',
        'none-help'    => '请试试更少的词语，或意思相同的其他词语。',
        'failed'       => '搜索目前无法使用。请稍后再试。',
        'count'        => '找到 {0} 家商店',
        'reviews'      => '（{0} 条评价）',
        'adults-only'  => '仅限成人',
        'pages'        => '搜索结果页',
        'previous'     => '上一页',
        'next'         => '下一页',
    ],
    // OtterNovaMart's own home page (Home::index())
    'home'       => [
        'meta-description' => 'OtterNovaMart 是 Otternova 上的独立商店接受订单和预约的地方。',
        'hero'             => [
            'eyebrow'    => 'Otternova 上的商店',
            'title'      => '独立小店，一次轻松结账',
            'lead'       => 'OtterNovaMart 是 Otternova 上的小型商家销售产品、接受预约并跟进您订单的地方。每家商店都有自己的页面、自己的价格，以及自己的送货方式。',
            'find-order' => '查找我的订单',
            'for-owners' => '您是商家吗？',
        ],
        'stack'            => [
            'product'      => '加入购物车',
            'product-note' => '商店直接提供的产品',
            'booking'      => '预约时间',
            'booking-note' => '选择时段或报名课程',
            'order'        => '订单 IN2611-0001',
            'order-note'   => '随时查看订单状态',
        ],
        'what'             => [
            'title'        => '您可以在商店做什么',
            'lead'         => '每家商店自行决定提供的内容，大多数提供以下一项或多项。',
            'products'     => [
                'title' => '购买产品',
                'text'  => '选择尺码或颜色，加入购物车后结账。商店会告诉您送货方式或取货地点。',
            ],
            'appointments' => [
                'title' => '预约服务',
                'text'  => '查看您想去的分店、想指定的人员有哪些空档，然后预约。',
            ],
            'sessions'     => [
                'title' => '报名课程活动',
                'text'  => '课程、导览和活动都在固定时间举行。选好日期，趁满额前报名。',
            ],
        ],
        'orders'           => [
            'title'   => '在找订单吗？',
            'lead'    => '每张订单都属于您下单的那家商店，所以要通过那家商店查找。',
            'email'   => [
                'title' => '打开您的确认电邮',
                'text'  => '电邮里有您的订单号码，以及直接前往下单商店的链接。',
            ],
            'status'  => [
                'title' => '查看订单状态',
                'text'  => '在商店页面底部选择“查看订单状态”，输入订单号码以及您的电邮地址或电话号码。',
            ],
            'history' => [
                'title' => '查看您在该商店的所有订单',
                'text'  => '选择“我的订单记录”并输入您的电邮地址。我们会发送一个链接给您，打开您在该商店的所有订单。',
            ],
            'help'    => [
                'title' => '找不到电邮？',
                'text'  => '请先查看垃圾邮件或促销邮件文件夹。如果还是找不到，请联系商店：电话号码和电邮地址都在每个页面的底部。',
            ],
        ],
        'owners'           => [
            'title' => '您是商家吗？',
            'text'  => '通过 Otternova 把您的产品、服务和预约放上网，并在 OtterNovaMart 接受订单。',
            'cta'   => '了解 Otternova',
        ],
    ],
];
