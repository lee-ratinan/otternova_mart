<?php
return [
    'site-title' => 'OtterNovaMart',
    'page'       => [
        'home'             => 'Home',
        'search'           => 'Search Results',
        'terms-of-service' => 'Terms of Service',
        'privacy-policy'   => 'Privacy Policy',
    ],
    // the search for shops (Home::searchResults()) and the header's search box
    'search'     => [
        'submit'       => 'Search',
        'label'        => 'Search for a shop',
        'placeholder'  => 'Search shops by name or what they offer…',
        'title-for'    => 'Results for “{0}”',
        'prompt'       => 'Search for a shop',
        'prompt-help'  => 'Type a shop’s name, or what you are looking for, such as “Thai massage”.',
        'none'         => 'No shops found for “{0}”',
        'none-help'    => 'Try fewer words, or another word for the same thing.',
        'failed'       => 'The search is not working right now. Please try again in a moment.',
        'count'        => '{0, plural, =1{# shop found} other{# shops found}}',
        'reviews'      => '{0, plural, =1{(# review)} other{(# reviews)}}',
        'adults-only'  => 'For adults only',
        'pages'        => 'Pages of results',
        'previous'     => 'Previous page',
        'next'         => 'Next page',
    ],
    // OtterNovaMart's own home page (Home::index())
    'home'       => [
        'meta-description' => 'OtterNovaMart is where independent shops on Otternova take orders and bookings.',
        'hero'             => [
            'eyebrow'    => 'Shops on Otternova',
            'title'      => 'Independent shops, one simple checkout',
            'lead'       => 'OtterNovaMart is where small businesses on Otternova sell their products, take bookings and keep track of your orders. Each shop has its own page, its own prices and its own way of getting things to you.',
            'find-order' => 'Find my order',
            'for-owners' => 'Run a business?',
        ],
        'stack'            => [
            'product'      => 'Add to cart',
            'product-note' => 'Products from the shop itself',
            'booking'      => 'Book a time',
            'booking-note' => 'Pick a slot or join a session',
            'order'        => 'Order IN2611-0001',
            'order-note'   => 'Check its status any time',
        ],
        'what'             => [
            'title'        => 'What you can do at a shop',
            'lead'         => 'Every shop chooses what it offers. Most offer one or more of these.',
            'products'     => [
                'title' => 'Buy products',
                'text'  => 'Choose a size or colour, add it to your cart and check out. The shop tells you how it ships or where to pick up.',
            ],
            'appointments' => [
                'title' => 'Book appointments',
                'text'  => 'See the times that are free at the store you want, with the person you want, and book one.',
            ],
            'sessions'     => [
                'title' => 'Join sessions',
                'text'  => 'Classes, tours and events run at set times. Pick a date and take a place before it fills up.',
            ],
        ],
        'orders'           => [
            'title'   => 'Looking for an order?',
            'lead'    => 'Each order belongs to the shop you placed it with, so the way back is through that shop.',
            'email'   => [
                'title' => 'Open your confirmation email',
                'text'  => 'It has your order number and a link straight to the shop where you placed it.',
            ],
            'status'  => [
                'title' => 'Check the order status',
                'text'  => 'On the shop\'s page, choose “Check Order Status” at the bottom and enter your order number with your email address or telephone number.',
            ],
            'history' => [
                'title' => 'See all your orders with that shop',
                'text'  => 'Choose “My Order History” and enter your email address. We\'ll email you a link that opens every order you placed there.',
            ],
            'help'    => [
                'title' => 'Can\'t find the email?',
                'text'  => 'Look in your spam or promotions folder first. If it isn\'t there, contact the shop: its phone number and email address are at the bottom of every one of its pages.',
            ],
        ],
        'owners'           => [
            'title' => 'Run a business?',
            'text'  => 'Put your products, services and bookings online with Otternova, and take orders on OtterNovaMart.',
            'cta'   => 'See Otternova',
        ],
    ],
];
