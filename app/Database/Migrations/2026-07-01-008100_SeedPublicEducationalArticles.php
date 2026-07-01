<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedPublicEducationalArticles extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('article_categories') || ! $this->db->tableExists('educational_articles')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $categories = [
            'Penulisan Ilmiah' => $this->categoryId('Penulisan Ilmiah', 'penulisan-ilmiah', 1),
            'Struktur Artikel' => $this->categoryId('Struktur Artikel', 'struktur-artikel', 2),
            'Publikasi'        => $this->categoryId('Publikasi', 'publikasi', 3),
        ];

        $articles = [
            [
                'category_id'  => $categories['Penulisan Ilmiah'],
                'title'        => 'Cara Menulis Artikel Ilmiah yang Baik dan Sistematis',
                'slug'         => 'cara-menulis-artikel-ilmiah-yang-baik',
                'summary'      => 'Panduan ringkas memahami tahapan awal dalam menyusun artikel ilmiah, mulai dari pemilihan topik hingga penyusunan struktur naskah.',
                'content'      => '<p>Artikel ilmiah merupakan karya tulis akademik yang disusun berdasarkan kaidah ilmiah, data, argumentasi, dan rujukan yang dapat dipertanggungjawabkan.</p><p>Langkah pertama dalam menulis artikel ilmiah adalah menentukan topik yang jelas, spesifik, dan relevan dengan bidang kajian. Topik yang terlalu luas akan menyulitkan penulis dalam membangun fokus pembahasan.</p><p>Setelah topik ditentukan, penulis perlu menyusun kerangka artikel. Struktur umum artikel ilmiah biasanya meliputi judul, abstrak, pendahuluan, metode, hasil dan pembahasan, kesimpulan, serta daftar pustaka.</p><p>Artikel yang baik tidak hanya menyajikan informasi, tetapi juga menunjukkan hubungan logis antara masalah, teori, metode, temuan, dan simpulan.</p><blockquote><p>Artikel ilmiah yang baik tidak hanya benar secara struktur, tetapi juga jelas, etis, mudah dipahami, dan relevan dengan pembaca sasaran.</p></blockquote>',
                'image_alt'    => 'Meja belajar dengan buku dan catatan untuk menulis artikel ilmiah',
                'status'       => 'published',
                'published_at' => '2026-06-27 09:00:00',
                'sort_order'   => 1,
            ],
            [
                'category_id'  => $categories['Struktur Artikel'],
                'title'        => 'Memahami Struktur IMRAD dalam Artikel Ilmiah',
                'slug'         => 'memahami-struktur-imrad-dalam-artikel-ilmiah',
                'summary'      => 'Mengenal struktur Introduction, Methods, Results, and Discussion sebagai format umum dalam penulisan artikel ilmiah.',
                'content'      => '<p>IMRAD merupakan singkatan dari Introduction, Methods, Results, and Discussion. Struktur ini banyak digunakan dalam artikel ilmiah karena memudahkan pembaca memahami alur penelitian.</p><p>Bagian Introduction berisi latar belakang, masalah, gap penelitian, dan tujuan. Bagian Methods menjelaskan pendekatan, subjek, instrumen, prosedur, dan teknik analisis data.</p><p>Bagian Results menyajikan temuan penelitian secara objektif, sedangkan Discussion menafsirkan temuan tersebut dengan mengaitkannya pada teori atau hasil penelitian terdahulu.</p><p>Dengan struktur IMRAD, artikel menjadi lebih runtut, sistematis, dan mudah dievaluasi oleh editor maupun reviewer.</p>',
                'image_alt'    => 'Laptop terbuka untuk menyusun struktur artikel ilmiah',
                'status'       => 'published',
                'published_at' => '2026-06-27 08:00:00',
                'sort_order'   => 2,
            ],
            [
                'category_id'  => $categories['Publikasi'],
                'title'        => 'Tips Memilih Jurnal yang Tepat untuk Publikasi',
                'slug'         => 'tips-memilih-jurnal-yang-tepat',
                'summary'      => 'Hal-hal penting yang perlu diperhatikan penulis sebelum mengirimkan artikel ke jurnal ilmiah.',
                'content'      => '<p>Memilih jurnal yang tepat merupakan langkah penting dalam proses publikasi ilmiah. Penulis perlu memastikan bahwa ruang lingkup artikel sesuai dengan fokus dan cakupan jurnal.</p><p>Sebelum submit, baca terlebih dahulu template, pedoman penulis, frekuensi terbit, informasi biaya, dan etika publikasi yang berlaku pada jurnal tersebut.</p><p>Hindari mengirim artikel yang sama ke lebih dari satu jurnal secara bersamaan karena hal tersebut bertentangan dengan etika publikasi.</p><p>Jurnal yang tepat akan membantu artikel diproses secara lebih relevan, baik dari sisi editor, reviewer, maupun pembaca sasaran.</p>',
                'image_alt'    => 'Ruang kerja penulis dengan laptop dan buku referensi',
                'status'       => 'published',
                'published_at' => '2026-06-26 09:00:00',
                'sort_order'   => 3,
            ],
        ];

        foreach ($articles as $article) {
            $exists = $this->db->table('educational_articles')
                ->where('slug', $article['slug'])
                ->countAllResults();

            if ($exists > 0) {
                continue;
            }

            $this->db->table('educational_articles')->insert($article + [
                'cover_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('educational_articles')) {
            return;
        }

        $this->db->table('educational_articles')
            ->whereIn('slug', [
                'cara-menulis-artikel-ilmiah-yang-baik',
                'memahami-struktur-imrad-dalam-artikel-ilmiah',
                'tips-memilih-jurnal-yang-tepat',
            ])
            ->delete();
    }

    private function categoryId(string $name, string $slug, int $sortOrder): int
    {
        $row = $this->db->table('article_categories')
            ->select('id')
            ->where('slug', $slug)
            ->get()
            ->getRowArray();

        if (is_array($row) && ! empty($row['id'])) {
            return (int) $row['id'];
        }

        $now = date('Y-m-d H:i:s');
        $this->db->table('article_categories')->insert([
            'name'        => $name,
            'slug'        => $slug,
            'description' => null,
            'is_active'   => 1,
            'sort_order'  => $sortOrder,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        return (int) $this->db->insertID();
    }
}
