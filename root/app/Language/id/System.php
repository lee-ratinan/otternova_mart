<?php
return [
    'site-title' => 'OtterNovaMart',
    'page'       => [
        'home'             => 'Beranda',
        'search'           => 'Hasil Pencarian',
        'terms-of-service' => 'Ketentuan Layanan',
        'privacy-policy'   => 'Kebijakan Privasi',
    ],
    // the search for shops (Home::searchResults()) and the header's search box
    'search'     => [
        'submit'       => 'Cari',
        'label'        => 'Cari toko',
        'placeholder'  => 'Cari toko berdasarkan nama atau yang ditawarkan…',
        'title-for'    => 'Hasil untuk “{0}”',
        'prompt'       => 'Cari toko',
        'prompt-help'  => 'Ketik nama toko, atau yang Anda cari, misalnya “pijat Thailand”.',
        'none'         => 'Tidak ada toko untuk “{0}”',
        'none-help'    => 'Coba dengan lebih sedikit kata, atau kata lain yang sama artinya.',
        'failed'       => 'Pencarian sedang tidak berfungsi. Silakan coba lagi sebentar lagi.',
        'count'        => '{0} toko ditemukan',
        'reviews'      => '({0} ulasan)',
        'adults-only'  => 'Khusus dewasa',
        'pages'        => 'Halaman hasil',
        'previous'     => 'Halaman sebelumnya',
        'next'         => 'Halaman berikutnya',
    ],
    // OtterNovaMart's own home page (Home::index())
    'home'       => [
        'meta-description' => 'OtterNovaMart adalah tempat toko-toko independen di Otternova menerima pesanan dan reservasi.',
        'hero'             => [
            'eyebrow'    => 'Toko di Otternova',
            'title'      => 'Toko independen, satu checkout yang mudah',
            'lead'       => 'OtterNovaMart adalah tempat usaha kecil di Otternova menjual produk, menerima reservasi, dan mencatat pesanan Anda. Setiap toko punya halaman sendiri, harga sendiri, dan caranya sendiri untuk mengantarkan pesanan kepada Anda.',
            'find-order' => 'Cari pesanan saya',
            'for-owners' => 'Punya usaha?',
        ],
        'stack'            => [
            'product'      => 'Tambah ke keranjang',
            'product-note' => 'Produk langsung dari tokonya',
            'booking'      => 'Pesan waktu',
            'booking-note' => 'Pilih slot atau ikuti sesi',
            'order'        => 'Pesanan IN2611-0001',
            'order-note'   => 'Cek statusnya kapan saja',
        ],
        'what'             => [
            'title'        => 'Yang bisa Anda lakukan di toko',
            'lead'         => 'Setiap toko memilih apa yang ditawarkannya. Sebagian besar menawarkan satu atau lebih dari hal berikut.',
            'products'     => [
                'title' => 'Beli produk',
                'text'  => 'Pilih ukuran atau warna, tambahkan ke keranjang, lalu checkout. Toko akan memberi tahu cara pengiriman atau tempat pengambilannya.',
            ],
            'appointments' => [
                'title' => 'Buat janji temu',
                'text'  => 'Lihat waktu yang masih kosong di toko yang Anda inginkan, dengan orang yang Anda inginkan, lalu pesan.',
            ],
            'sessions'     => [
                'title' => 'Ikuti sesi',
                'text'  => 'Kelas, tur, dan acara berlangsung pada waktu yang sudah ditentukan. Pilih tanggal dan amankan tempat sebelum penuh.',
            ],
        ],
        'orders'           => [
            'title'   => 'Mencari pesanan?',
            'lead'    => 'Setiap pesanan milik toko tempat Anda memesan, jadi cara kembali ke pesanan Anda adalah melalui toko tersebut.',
            'email'   => [
                'title' => 'Buka email konfirmasi Anda',
                'text'  => 'Email tersebut berisi nomor pesanan dan tautan langsung ke toko tempat Anda memesan.',
            ],
            'status'  => [
                'title' => 'Cek status pesanan',
                'text'  => 'Di halaman toko, pilih “Cek Status Pesanan” di bagian bawah, lalu masukkan nomor pesanan beserta alamat email atau nomor telepon Anda.',
            ],
            'history' => [
                'title' => 'Lihat semua pesanan Anda di toko itu',
                'text'  => 'Pilih “Riwayat Pesanan Saya” dan masukkan alamat email Anda. Kami akan mengirimkan tautan untuk membuka semua pesanan Anda di sana.',
            ],
            'help'    => [
                'title' => 'Tidak menemukan emailnya?',
                'text'  => 'Periksa dulu folder spam atau promosi. Jika tidak ada, hubungi toko: nomor telepon dan alamat emailnya ada di bagian bawah setiap halamannya.',
            ],
        ],
        'owners'           => [
            'title' => 'Punya usaha?',
            'text'  => 'Tampilkan produk, layanan, dan reservasi Anda secara online dengan Otternova, dan terima pesanan di OtterNovaMart.',
            'cta'   => 'Lihat Otternova',
        ],
    ],
];
