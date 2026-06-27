<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class PublicPage extends BaseController
{
    protected $helpers = ['url', 'text'];

    public function index()
    {
        $articles = $this->getArticles();

        $data = [
            'title'           => 'Pusat Layanan Publikasi Ilmiah',
            'stats'           => $this->getStats(),
            'latestRequests'  => $this->getLatestRequests(),
            'journals'        => $this->getJournals(),
            'articles'        => array_slice($articles, 0, 3),
        ];

        return view('public/home', $data);
    }

    public function artikel()
    {
        $data = [
            'title'    => 'Artikel Ilmiah',
            'articles' => $this->getArticles(),
        ];

        return view('public/articles/index', $data);
    }

    public function detailArtikel(string $slug)
    {
        $articles = $this->getArticles();

        $article = null;

        foreach ($articles as $item) {
            if ($item['slug'] === $slug) {
                $article = $item;
                break;
            }
        }

        if (! $article) {
            throw PageNotFoundException::forPageNotFound('Artikel tidak ditemukan.');
        }

        $relatedArticles = array_values(array_filter($articles, function ($item) use ($slug) {
            return $item['slug'] !== $slug;
        }));

        $data = [
            'title'           => $article['title'],
            'article'         => $article,
            'relatedArticles' => array_slice($relatedArticles, 0, 3),
        ];

        return view('public/articles/detail', $data);
    }

    public function ajukanLoa()
    {
        return redirect()->to('/')->with('message', 'Halaman pengajuan LoA akan dihubungkan ke sistem utama.');
    }

    public function verifikasiLoa()
    {
        return redirect()->to('/')->with('message', 'Halaman verifikasi LoA akan dihubungkan ke sistem utama.');
    }

    private function getStats(): array
    {
        return [
            [
                'label' => 'Permohonan',
                'value' => 52,
                'note'  => 'Total pengajuan masuk',
            ],
            [
                'label' => 'LoA Terbit',
                'value' => 48,
                'note'  => 'Sudah disetujui',
            ],
            [
                'label' => 'Menunggu',
                'value' => 2,
                'note'  => 'Perlu verifikasi',
            ],
            [
                'label' => 'Diproses',
                'value' => 2,
                'note'  => 'Sedang ditinjau',
            ],
        ];
    }

    private function getLatestRequests(): array
    {
        return [
            [
                'code'   => 'PLPI-00064',
                'title'  => 'Pelatihan Desain Grafis CorelDraw Untuk Membuat Poster di SMK Ibrahimy 1 Sukorejo Situbondo',
                'status' => 'Disetujui',
                'date'   => '27-06-2026',
            ],
            [
                'code'   => 'PLPI-00063',
                'title'  => 'Persepsi Guru terhadap Pemberian Insentif dan Tunjangan dalam Meningkatkan Kinerja Guru di Sekolah Swasta Kecamatan Ciputat, Kota Tangerang Selatan',
                'status' => 'Disetujui',
                'date'   => '27-06-2026',
            ],
            [
                'code'   => 'PLPI-00061',
                'title'  => 'Pendampingan Pelayanan Administrasi dalam Meningkatkan Kualitas Layanan bagi Guru dan Tenaga Kependidikan',
                'status' => 'Disetujui',
                'date'   => '27-06-2026',
            ],
            [
                'code'   => 'PLPI-00060',
                'title'  => 'Media Cakra Math untuk Menstimulasi Kemampuan Logika Matematika Anak Usia 5–6 Tahun',
                'status' => 'Diproses',
                'date'   => '26-06-2026',
            ],
            [
                'code'   => 'PLPI-00059',
                'title'  => 'Edukasi Teknik Nonfarmakologi untuk Mengatasi Ketidaknyamanan pada Penderita Hipertensi',
                'status' => 'Disetujui',
                'date'   => '26-06-2026',
            ],
        ];
    }

    private function getJournals(): array
    {
        return [
            [
                'name'     => 'Leibniz: Jurnal Matematika',
                'category' => 'Matematika',
                'issn'     => 'E-ISSN 2775-2356',
                'url'      => '#',
            ],
            [
                'name'     => 'Edukasi Tematik: Jurnal Pendidikan Sekolah Dasar',
                'category' => 'Pendidikan Dasar',
                'issn'     => 'E-ISSN -',
                'url'      => '#',
            ],
            [
                'name'     => 'Leksikon: Jurnal Pendidikan Bahasa, Sastra, & Budaya',
                'category' => 'Bahasa dan Sastra',
                'issn'     => 'E-ISSN -',
                'url'      => '#',
            ],
            [
                'name'     => 'Sibernetik: Jurnal Pendidikan dan Pembelajaran',
                'category' => 'Pendidikan dan Pembelajaran',
                'issn'     => 'E-ISSN -',
                'url'      => '#',
            ],
        ];
    }

    private function getArticles(): array
    {
        return [
            [
                'slug'      => 'cara-menulis-artikel-ilmiah-yang-baik',
                'title'     => 'Cara Menulis Artikel Ilmiah yang Baik dan Sistematis',
                'category'  => 'Penulisan Ilmiah',
                'date'      => '27 Juni 2026',
                'read_time' => '5 menit baca',
                'image'     => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=1200&q=80',
                'image_alt' => 'Meja belajar dengan buku dan catatan untuk menulis artikel ilmiah',
                'summary'   => 'Panduan ringkas memahami tahapan awal dalam menyusun artikel ilmiah, mulai dari pemilihan topik hingga penyusunan struktur naskah.',
                'content'   => [
                    'Artikel ilmiah merupakan karya tulis akademik yang disusun berdasarkan kaidah ilmiah, data, argumentasi, dan rujukan yang dapat dipertanggungjawabkan.',
                    'Langkah pertama dalam menulis artikel ilmiah adalah menentukan topik yang jelas, spesifik, dan relevan dengan bidang kajian. Topik yang terlalu luas akan menyulitkan penulis dalam membangun fokus pembahasan.',
                    'Setelah topik ditentukan, penulis perlu menyusun kerangka artikel. Struktur umum artikel ilmiah biasanya meliputi judul, abstrak, pendahuluan, metode, hasil dan pembahasan, kesimpulan, serta daftar pustaka.',
                    'Artikel yang baik tidak hanya menyajikan informasi, tetapi juga menunjukkan hubungan logis antara masalah, teori, metode, temuan, dan simpulan.',
                ],
            ],
            [
                'slug'      => 'memahami-struktur-imrad-dalam-artikel-ilmiah',
                'title'     => 'Memahami Struktur IMRAD dalam Artikel Ilmiah',
                'category'  => 'Struktur Artikel',
                'date'      => '27 Juni 2026',
                'read_time' => '4 menit baca',
                'image'     => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80',
                'image_alt' => 'Laptop terbuka untuk menyusun struktur artikel ilmiah',
                'summary'   => 'Mengenal struktur Introduction, Methods, Results, and Discussion sebagai format umum dalam penulisan artikel ilmiah.',
                'content'   => [
                    'IMRAD merupakan singkatan dari Introduction, Methods, Results, and Discussion. Struktur ini banyak digunakan dalam artikel ilmiah karena memudahkan pembaca memahami alur penelitian.',
                    'Bagian Introduction berisi latar belakang, masalah, gap penelitian, dan tujuan. Bagian Methods menjelaskan pendekatan, subjek, instrumen, prosedur, dan teknik analisis data.',
                    'Bagian Results menyajikan temuan penelitian secara objektif, sedangkan Discussion menafsirkan temuan tersebut dengan mengaitkannya pada teori atau hasil penelitian terdahulu.',
                    'Dengan struktur IMRAD, artikel menjadi lebih runtut, sistematis, dan mudah dievaluasi oleh editor maupun reviewer.',
                ],
            ],
            [
                'slug'      => 'tips-memilih-jurnal-yang-tepat',
                'title'     => 'Tips Memilih Jurnal yang Tepat untuk Publikasi',
                'category'  => 'Publikasi',
                'date'      => '26 Juni 2026',
                'read_time' => '5 menit baca',
                'image'     => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80',
                'image_alt' => 'Ruang kerja penulis dengan laptop dan buku referensi',
                'summary'   => 'Hal-hal penting yang perlu diperhatikan penulis sebelum mengirimkan artikel ke jurnal ilmiah.',
                'content'   => [
                    'Memilih jurnal yang tepat merupakan langkah penting dalam proses publikasi ilmiah. Penulis perlu memastikan bahwa ruang lingkup artikel sesuai dengan fokus dan cakupan jurnal.',
                    'Sebelum submit, baca terlebih dahulu template, pedoman penulis, frekuensi terbit, informasi biaya, dan etika publikasi yang berlaku pada jurnal tersebut.',
                    'Hindari mengirim artikel yang sama ke lebih dari satu jurnal secara bersamaan karena hal tersebut bertentangan dengan etika publikasi.',
                    'Jurnal yang tepat akan membantu artikel diproses secara lebih relevan, baik dari sisi editor, reviewer, maupun pembaca sasaran.',
                ],
            ],
            [
                'slug'      => 'etika-publikasi-ilmiah-yang-perlu-dipahami-penulis',
                'title'     => 'Etika Publikasi Ilmiah yang Perlu Dipahami Penulis',
                'category'  => 'Etika Publikasi',
                'date'      => '26 Juni 2026',
                'read_time' => '6 menit baca',
                'image'     => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1200&q=80',
                'image_alt' => 'Diskusi tim akademik tentang etika publikasi ilmiah',
                'summary'   => 'Membahas prinsip orisinalitas, sitasi, plagiarisme, konflik kepentingan, dan tanggung jawab penulis dalam publikasi ilmiah.',
                'content'   => [
                    'Etika publikasi ilmiah menjadi fondasi penting dalam menjaga kualitas dan integritas akademik.',
                    'Penulis wajib memastikan bahwa naskah yang dikirim merupakan karya orisinal, belum pernah diterbitkan, dan tidak sedang diproses pada jurnal lain.',
                    'Setiap gagasan, data, atau kutipan dari sumber lain harus dicantumkan secara benar melalui sitasi dan daftar pustaka.',
                    'Pemahaman terhadap etika publikasi membantu mencegah plagiarisme, duplikasi publikasi, manipulasi data, dan konflik kepentingan.',
                ],
            ],
        ];
    }
}
