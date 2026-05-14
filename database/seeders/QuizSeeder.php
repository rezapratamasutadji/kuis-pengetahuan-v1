<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        Category::query()->delete();

        foreach ($this->categories() as $categoryData) {
            $category = Category::query()->create([
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'],
                'description' => $categoryData['description'],
                'icon' => $categoryData['icon'],
            ]);

            foreach ($this->buildQuestions($categoryData) as $question) {
                $category->questions()->create($question);
            }
        }
    }

    private function buildQuestions(array $categoryData): array
    {
        $questions = [];

        foreach ($categoryData['rounds'] as $round => $roundData) {
            $items = array_merge(
                $roundData['difficulty']['easy'] ?? [],
                $roundData['difficulty']['medium'] ?? [],
                $roundData['difficulty']['hard'] ?? [],
            );

            $answerPool = array_values(array_unique(array_map(
                fn (array $item): string => $item['answer'],
                $items,
            )));

            $letters = ['a', 'b', 'c', 'd'];

            foreach ($items as $index => $item) {
                $correctAnswer = $item['answer'];
                $difficulty = $item['difficulty'];
                $wrongAnswers = [];
                $cursor = $index + 2;

                while (count($wrongAnswers) < 3) {
                    $candidate = $answerPool[$cursor % count($answerPool)];
                    $cursor++;

                    if ($candidate === $correctAnswer || in_array($candidate, $wrongAnswers, true)) {
                        continue;
                    }

                    $wrongAnswers[] = $candidate;
                }

                $correctPosition = $index % 4;
                $optionValues = $wrongAnswers;
                array_splice($optionValues, $correctPosition, 0, [$correctAnswer]);

                $questions[] = [
                    'round' => $round,
                    'number' => $index + 1,
                    'difficulty' => $difficulty,
                    'prompt' => $item['prompt'],
                    'option_a' => $optionValues[0],
                    'option_b' => $optionValues[1],
                    'option_c' => $optionValues[2],
                    'option_d' => $optionValues[3],
                    'correct_option' => $letters[$correctPosition],
                    'explanation' => null,
                ];
            }
        }

        return $questions;
    }

    private function categories(): array
    {
        return [
            [
                'name' => 'Astronomi & Antariksa',
                'slug' => 'astronomi-antariksa',
                'description' => 'Soal seputar planet, bintang, dan fenomena di luar angkasa.',
                'icon' => 'globe',
                'rounds' => [
                    'qualification' => [
                        'difficulty' => [
                            'easy' => $this->markDifficulty([
                                ['prompt' => 'Planet terdekat dengan Matahari adalah ...', 'answer' => 'Merkurius'],
                                ['prompt' => 'Planet terbesar di tata surya adalah ...', 'answer' => 'Jupiter'],
                                ['prompt' => 'Planet yang dikenal sebagai Planet Merah adalah ...', 'answer' => 'Mars'],
                                ['prompt' => 'Satelit alami Bumi adalah ...', 'answer' => 'Bulan'],
                                ['prompt' => 'Bintang pusat tata surya kita adalah ...', 'answer' => 'Matahari'],
                                ['prompt' => 'Planet bercincin paling terkenal adalah ...', 'answer' => 'Saturnus'],
                                ['prompt' => 'Planet ketiga dari Matahari adalah ...', 'answer' => 'Bumi'],
                                ['prompt' => 'Gugusan bintang yang membentuk pola disebut ...', 'answer' => 'Konstelasi'],
                                ['prompt' => 'Benda langit yang memiliki ekor bercahaya disebut ...', 'answer' => 'Komet'],
                                ['prompt' => 'Lapisan gas yang menyelimuti Bumi disebut ...', 'answer' => 'Atmosfer'],
                                ['prompt' => 'Alat untuk mengamati benda langit jauh adalah ...', 'answer' => 'Teleskop'],
                                ['prompt' => 'Planet yang paling dekat dengan Bumi setelah Venus adalah ...', 'answer' => 'Mars'],
                            ], 'easy'),
                            'medium' => $this->markDifficulty([
                                ['prompt' => 'Planet dengan rotasi tercepat di tata surya adalah ...', 'answer' => 'Jupiter'],
                                ['prompt' => 'Galaksi tempat tata surya berada bernama ...', 'answer' => 'Bima Sakti'],
                                ['prompt' => 'Fenomena ketika Bulan menutupi Matahari disebut ...', 'answer' => 'Gerhana Matahari'],
                                ['prompt' => 'Fenomena saat Bumi menutupi cahaya Matahari ke Bulan disebut ...', 'answer' => 'Gerhana Bulan'],
                                ['prompt' => 'Planet yang memiliki hari lebih panjang daripada tahunnya adalah ...', 'answer' => 'Venus'],
                                ['prompt' => 'Batu luar angkasa yang sampai ke permukaan Bumi disebut ...', 'answer' => 'Meteorit'],
                                ['prompt' => 'Wahana yang membawa manusia pertama ke Bulan adalah ...', 'answer' => 'Apollo 11'],
                                ['prompt' => 'Nama planet yang memiliki kemiringan sumbu sangat ekstrem adalah ...', 'answer' => 'Uranus'],
                            ], 'medium'),
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Bintang terang yang menjadi pusat sistem Alpha Centauri terdekat dengan Bumi adalah ...', 'answer' => 'Proxima Centauri'],
                                ['prompt' => 'Batas imajiner di sekitar lubang hitam yang tidak bisa dilewati cahaya disebut ...', 'answer' => 'Event horizon'],
                                ['prompt' => 'Nama teleskop luar angkasa penerus Hubble yang diluncurkan tahun 2021 adalah ...', 'answer' => 'James Webb'],
                                ['prompt' => 'Planet kerdil terbesar di sabuk Kuiper adalah ...', 'answer' => 'Pluto'],
                                ['prompt' => 'Teori yang menjelaskan asal mula alam semesta modern disebut ...', 'answer' => 'Big Bang'],
                            ], 'hard'),
                        ],
                    ],
                    'semifinal' => [
                        'difficulty' => [
                            'easy' => $this->markDifficulty([
                                ['prompt' => 'Planet yang paling panas di tata surya adalah ...', 'answer' => 'Venus'],
                                ['prompt' => 'Planet yang dikenal dengan banyak bulan besar seperti Europa dan Ganymede adalah ...', 'answer' => 'Jupiter'],
                                ['prompt' => 'Benda langit kecil yang mengorbit Matahari dan banyak ditemukan di antara Mars dan Jupiter disebut ...', 'answer' => 'Asteroid'],
                                ['prompt' => 'Planet yang letaknya paling jauh dari Matahari dalam klasifikasi modern adalah ...', 'answer' => 'Neptunus'],
                                ['prompt' => 'Nama gaya yang membuat planet tetap mengorbit Matahari adalah ...', 'answer' => 'Gravitasi'],
                            ], 'easy'),
                            'medium' => $this->markDifficulty([
                                ['prompt' => 'Rasi bintang yang sering dipakai petunjuk arah utara adalah ...', 'answer' => 'Ursa Major'],
                                ['prompt' => 'Lapisan Matahari yang tampak saat gerhana total disebut ...', 'answer' => 'Korona'],
                                ['prompt' => 'Nama satelit alami terbesar milik Saturnus adalah ...', 'answer' => 'Titan'],
                                ['prompt' => 'Objek superpadat hasil ledakan bintang masif yang bisa memancarkan pulsa radio disebut ...', 'answer' => 'Pulsar'],
                                ['prompt' => 'Satuan jarak yang kira-kira sama dengan 9,46 triliun kilometer disebut ...', 'answer' => 'Tahun cahaya'],
                            ], 'medium'),
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Bintang terang di konstelasi Orion yang berwarna kemerahan adalah ...', 'answer' => 'Betelgeuse'],
                                ['prompt' => 'Nama misi luar angkasa yang pertama kali mendaratkan rover Perseverance di Mars adalah ...', 'answer' => 'Mars 2020'],
                                ['prompt' => 'Satelit alami Jupiter yang diyakini memiliki lautan bawah es dan menarik untuk pencarian kehidupan adalah ...', 'answer' => 'Europa'],
                                ['prompt' => 'Fenomena perubahan posisi semu bintang akibat gerak Bumi mengelilingi Matahari disebut ...', 'answer' => 'Paralaks'],
                                ['prompt' => 'Nama batas gravitasi tempat benda masih terikat pada pengaruh Bumi sebelum lepas ke luar angkasa disebut ...', 'answer' => 'Kecepatan lepas'],
                            ], 'hard'),
                        ],
                    ],
                    'final' => [
                        'difficulty' => [
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Bulan terbesar di tata surya yang mengorbit Jupiter adalah ...', 'answer' => 'Ganymede'],
                                ['prompt' => 'Jenis galaksi Bima Sakti diklasifikasikan sebagai galaksi ...', 'answer' => 'Spiral batang'],
                                ['prompt' => 'Bintang paling terang di langit malam selain Matahari adalah ...', 'answer' => 'Sirius'],
                                ['prompt' => 'Nama hukum yang menjelaskan hubungan kuadrat periode orbit dengan kubik jarak orbit planet adalah hukum ...', 'answer' => 'Kepler ketiga'],
                                ['prompt' => 'Molekul dominan penyusun atmosfer Titan adalah ...', 'answer' => 'Nitrogen'],
                                ['prompt' => 'Astronaut pertama yang berjalan di Bulan adalah ...', 'answer' => 'Neil Armstrong'],
                                ['prompt' => 'Objek dengan massa sangat besar di pusat galaksi Bima Sakti yang diyakini sebagai lubang hitam supermasif bernama ...', 'answer' => 'Sagittarius A*'],
                                ['prompt' => 'Nama efek ketika panjang gelombang cahaya dari galaksi jauh bergeser menandakan alam semesta mengembang adalah ...', 'answer' => 'Redshift'],
                            ], 'hard'),
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Tubuh Manusia',
                'slug' => 'tubuh-manusia',
                'description' => 'Menguji pemahaman organ, sistem tubuh, dan fungsi dasar tubuh manusia.',
                'icon' => 'flask',
                'rounds' => [
                    'qualification' => [
                        'difficulty' => [
                            'easy' => $this->markDifficulty([
                                ['prompt' => 'Organ yang memompa darah ke seluruh tubuh adalah ...', 'answer' => 'Jantung'],
                                ['prompt' => 'Organ utama pernapasan manusia adalah ...', 'answer' => 'Paru-paru'],
                                ['prompt' => 'Tulang terpanjang pada tubuh manusia adalah ...', 'answer' => 'Femur'],
                                ['prompt' => 'Bagian tubuh yang berfungsi mencerna makanan setelah lambung adalah ...', 'answer' => 'Usus halus'],
                                ['prompt' => 'Organ yang menyaring darah dan menghasilkan urine adalah ...', 'answer' => 'Ginjal'],
                                ['prompt' => 'Indra yang digunakan untuk melihat adalah ...', 'answer' => 'Mata'],
                                ['prompt' => 'Indra yang digunakan untuk mendengar adalah ...', 'answer' => 'Telinga'],
                                ['prompt' => 'Cairan merah yang mengalir di pembuluh darah adalah ...', 'answer' => 'Darah'],
                                ['prompt' => 'Bagian mulut yang membantu mengecap rasa adalah ...', 'answer' => 'Lidah'],
                                ['prompt' => 'Organ yang berfungsi memecah makanan secara mekanis di rongga mulut adalah ...', 'answer' => 'Gigi'],
                                ['prompt' => 'Sistem tubuh yang bertanggung jawab mengangkut oksigen adalah sistem ...', 'answer' => 'Peredaran darah'],
                                ['prompt' => 'Bagian rangka yang melindungi otak adalah ...', 'answer' => 'Tengkorak'],
                            ], 'easy'),
                            'medium' => $this->markDifficulty([
                                ['prompt' => 'Hormon insulin diproduksi oleh organ ...', 'answer' => 'Pankreas'],
                                ['prompt' => 'Bagian mata yang mengatur banyaknya cahaya masuk adalah ...', 'answer' => 'Iris'],
                                ['prompt' => 'Organ terbesar pada tubuh manusia adalah ...', 'answer' => 'Kulit'],
                                ['prompt' => 'Sel darah merah mengikat oksigen menggunakan protein ...', 'answer' => 'Hemoglobin'],
                                ['prompt' => 'Struktur yang menghubungkan otot dengan tulang disebut ...', 'answer' => 'Tendon'],
                                ['prompt' => 'Bagian otak yang berperan besar dalam koordinasi gerak adalah ...', 'answer' => 'Serebelum'],
                                ['prompt' => 'Katup antara lambung dan usus dua belas jari disebut ...', 'answer' => 'Pilorus'],
                                ['prompt' => 'Pembuluh darah yang membawa darah dari jantung ke seluruh tubuh disebut ...', 'answer' => 'Arteri'],
                            ], 'medium'),
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Sel saraf utama dalam sistem saraf disebut ...', 'answer' => 'Neuron'],
                                ['prompt' => 'Bagian ginjal tempat filtrasi awal darah terjadi adalah ...', 'answer' => 'Glomerulus'],
                                ['prompt' => 'Tulang kecil pada telinga tengah yang berbentuk seperti sanggurdi disebut ...', 'answer' => 'Stapes'],
                                ['prompt' => 'Bagian membran sel yang mengatur keluar masuknya zat secara selektif disebut ...', 'answer' => 'Permeabilitas'],
                                ['prompt' => 'Nama hormon yang berperan besar dalam respons darurat fight or flight adalah ...', 'answer' => 'Adrenalin'],
                            ], 'hard'),
                        ],
                    ],
                    'semifinal' => [
                        'difficulty' => [
                            'easy' => $this->markDifficulty([
                                ['prompt' => 'Organ yang menghasilkan empedu adalah ...', 'answer' => 'Hati'],
                                ['prompt' => 'Ruang jantung manusia berjumlah ...', 'answer' => 'Empat'],
                                ['prompt' => 'Tulang yang melindungi jantung dan paru-paru adalah ...', 'answer' => 'Tulang rusuk'],
                                ['prompt' => 'Tempat pertukaran oksigen dan karbon dioksida di paru-paru disebut ...', 'answer' => 'Alveolus'],
                                ['prompt' => 'Gerak mendorong makanan di saluran pencernaan disebut ...', 'answer' => 'Peristaltik'],
                            ], 'easy'),
                            'medium' => $this->markDifficulty([
                                ['prompt' => 'Bagian darah yang berperan penting dalam pembekuan adalah ...', 'answer' => 'Trombosit'],
                                ['prompt' => 'Saraf yang membawa impuls dari reseptor menuju otak atau sumsum tulang belakang adalah saraf ...', 'answer' => 'Sensorik'],
                                ['prompt' => 'Bagian telinga yang berfungsi menjaga keseimbangan adalah ...', 'answer' => 'Kanalis semisirkularis'],
                                ['prompt' => 'Lapisan pelindung lambung yang mencegah asam merusak dinding lambung berupa ...', 'answer' => 'Mukus'],
                                ['prompt' => 'Pigmen yang memberi warna pada kulit manusia disebut ...', 'answer' => 'Melanin'],
                            ], 'medium'),
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Tulang rawan yang menutup trakea saat menelan disebut ...', 'answer' => 'Epiglotis'],
                                ['prompt' => 'Bagian otak yang mengatur suhu tubuh dan rasa lapar adalah ...', 'answer' => 'Hipotalamus'],
                                ['prompt' => 'Pembuluh darah sangat kecil tempat pertukaran zat dengan jaringan disebut ...', 'answer' => 'Kapiler'],
                                ['prompt' => 'Proses pembentukan sel darah merah terutama terjadi di ...', 'answer' => 'Sumsum tulang'],
                                ['prompt' => 'Kelainan mata yang menyebabkan bayangan jatuh di depan retina disebut ...', 'answer' => 'Miopi'],
                            ], 'hard'),
                        ],
                    ],
                    'final' => [
                        'difficulty' => [
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Enzim pemecah protein yang aktif di lambung adalah ...', 'answer' => 'Pepsin'],
                                ['prompt' => 'Struktur kecil pada nefron tempat reabsorpsi utama glukosa terjadi adalah ...', 'answer' => 'Tubulus proksimal'],
                                ['prompt' => 'Lapisan terluar bola mata yang berwarna putih disebut ...', 'answer' => 'Sklera'],
                                ['prompt' => 'Katup jantung yang terletak antara atrium kiri dan ventrikel kiri adalah ...', 'answer' => 'Bikuspid'],
                                ['prompt' => 'Sel darah putih yang menghasilkan antibodi dalam sistem imun adaptif adalah ...', 'answer' => 'Limfosit B'],
                                ['prompt' => 'Jaringan penghubung yang menautkan tulang dengan tulang disebut ...', 'answer' => 'Ligamen'],
                                ['prompt' => 'Bagian otak yang berperan besar dalam memori jangka panjang adalah ...', 'answer' => 'Hipokampus'],
                                ['prompt' => 'Proses perubahan zat sisa nitrogen menjadi urea utama terjadi di ...', 'answer' => 'Hati'],
                            ], 'hard'),
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Flora & Fauna',
                'slug' => 'flora-fauna',
                'description' => 'Soal tentang hewan, tumbuhan, habitat, dan klasifikasi makhluk hidup.',
                'icon' => 'lightbulb',
                'rounds' => [
                    'qualification' => [
                        'difficulty' => [
                            'easy' => $this->markDifficulty([
                                ['prompt' => 'Hewan yang dikenal sebagai raja hutan adalah ...', 'answer' => 'Singa'],
                                ['prompt' => 'Tumbuhan yang menghasilkan beras adalah ...', 'answer' => 'Padi'],
                                ['prompt' => 'Hewan terbesar di dunia adalah ...', 'answer' => 'Paus biru'],
                                ['prompt' => 'Hewan yang dapat berubah warna untuk kamuflase adalah ...', 'answer' => 'Bunglon'],
                                ['prompt' => 'Bagian tumbuhan yang menyerap air dan mineral dari tanah adalah ...', 'answer' => 'Akar'],
                                ['prompt' => 'Hewan berkantung khas Australia adalah ...', 'answer' => 'Kanguru'],
                                ['prompt' => 'Bunga nasional Indonesia yang langka adalah ...', 'answer' => 'Rafflesia'],
                                ['prompt' => 'Hewan yang bertelur dan hidup di air serta darat disebut ...', 'answer' => 'Amfibi'],
                                ['prompt' => 'Daun berfungsi utama untuk proses ...', 'answer' => 'Fotosintesis'],
                                ['prompt' => 'Tumbuhan kaktus cocok hidup di daerah ...', 'answer' => 'Gurun'],
                                ['prompt' => 'Hewan pemakan tumbuhan disebut ...', 'answer' => 'Herbivora'],
                                ['prompt' => 'Hewan pemakan daging disebut ...', 'answer' => 'Karnivora'],
                            ], 'easy'),
                            'medium' => $this->markDifficulty([
                                ['prompt' => 'Hewan endemik Indonesia yang hidup di Pulau Komodo adalah ...', 'answer' => 'Komodo'],
                                ['prompt' => 'Tumbuhan yang berkembang biak dengan spora antara lain ...', 'answer' => 'Paku'],
                                ['prompt' => 'Mamalia yang dapat terbang adalah ...', 'answer' => 'Kelelawar'],
                                ['prompt' => 'Organ tumbuhan yang berkembang menjadi buah setelah pembuahan adalah ...', 'answer' => 'Bunga'],
                                ['prompt' => 'Hewan yang memiliki paruh lebar dan mampu menyimpan air di kantong tenggorokan adalah ...', 'answer' => 'Pelikan'],
                                ['prompt' => 'Tumbuhan bakau paling umum hidup di daerah ...', 'answer' => 'Pantai'],
                                ['prompt' => 'Kelompok hewan tanpa tulang belakang disebut ...', 'answer' => 'Invertebrata'],
                                ['prompt' => 'Penyerbukan yang dibantu angin disebut ...', 'answer' => 'Anemogami'],
                            ], 'medium'),
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Hewan purba mirip kadal besar yang kini menjadi ikon Nusa Tenggara Timur adalah ...', 'answer' => 'Varanus komodoensis'],
                                ['prompt' => 'Proses tumbuhan membuat makanan dengan bantuan cahaya disebut ...', 'answer' => 'Fotosintesis'],
                                ['prompt' => 'Hewan nokturnal yang menggunakan ekolokasi untuk berburu adalah ...', 'answer' => 'Kelelawar'],
                                ['prompt' => 'Jaringan pengangkut air dari akar ke daun pada tumbuhan disebut ...', 'answer' => 'Xilem'],
                                ['prompt' => 'Hewan laut yang memiliki delapan lengan utama adalah ...', 'answer' => 'Gurita'],
                            ], 'hard'),
                        ],
                    ],
                    'semifinal' => [
                        'difficulty' => [
                            'easy' => $this->markDifficulty([
                                ['prompt' => 'Hewan yang mengalami metamorfosis sempurna dari ulat menjadi kupu-kupu adalah ...', 'answer' => 'Kupu-kupu'],
                                ['prompt' => 'Tumbuhan yang bijinya terbuka digolongkan sebagai ...', 'answer' => 'Gymnospermae'],
                                ['prompt' => 'Burung nasional Indonesia adalah ...', 'answer' => 'Elang Jawa'],
                                ['prompt' => 'Hewan air yang bernapas dengan insang adalah ...', 'answer' => 'Ikan'],
                                ['prompt' => 'Batang pohon mengangkut hasil fotosintesis melalui jaringan ...', 'answer' => 'Floem'],
                            ], 'easy'),
                            'medium' => $this->markDifficulty([
                                ['prompt' => 'Hubungan dua makhluk hidup yang saling menguntungkan disebut ...', 'answer' => 'Mutualisme'],
                                ['prompt' => 'Hewan yang memakan tumbuhan dan daging sekaligus disebut ...', 'answer' => 'Omnivora'],
                                ['prompt' => 'Tumbuhan insektivora yang terkenal menangkap serangga adalah ...', 'answer' => 'Kantong semar'],
                                ['prompt' => 'Mamalia bertelur dari Australia seperti platipus digolongkan sebagai ...', 'answer' => 'Monotremata'],
                                ['prompt' => 'Hewan yang berkembang biak dengan membelah diri pada organisme mikroskopis contohnya adalah ...', 'answer' => 'Amoeba'],
                            ], 'medium'),
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Tumbuhan yang hidup menempel pada tumbuhan lain tanpa mengambil makanannya disebut ...', 'answer' => 'Epifit'],
                                ['prompt' => 'Nama latin padi adalah ...', 'answer' => 'Oryza sativa'],
                                ['prompt' => 'Hewan khas Papua dengan bulu indah yang terkenal sebagai bird of paradise adalah ...', 'answer' => 'Cenderawasih'],
                                ['prompt' => 'Tahap awal perkecambahan biji saat kulit biji pecah disebut ...', 'answer' => 'Imbibisi'],
                                ['prompt' => 'Kelompok hewan dengan suhu tubuh cenderung mengikuti lingkungan disebut ...', 'answer' => 'Ektoterm'],
                            ], 'hard'),
                        ],
                    ],
                    'final' => [
                        'difficulty' => [
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Hubungan organisme yang satu untung sementara yang lain tidak dirugikan disebut ...', 'answer' => 'Komensalisme'],
                                ['prompt' => 'Proses penguapan air melalui stomata tumbuhan disebut ...', 'answer' => 'Transpirasi'],
                                ['prompt' => 'Hewan primata besar endemik Kalimantan adalah ...', 'answer' => 'Orangutan'],
                                ['prompt' => 'Jaringan pada daun tempat paling aktif terjadinya fotosintesis adalah ...', 'answer' => 'Palisade'],
                                ['prompt' => 'Hewan yang memecah cangkang keras dengan rahang sangat kuat dan hidup di daerah hutan Sulawesi adalah ...', 'answer' => 'Babirusa'],
                                ['prompt' => 'Mikroorganisme yang berperan penting mengubah nitrogen udara menjadi bentuk yang dapat digunakan tumbuhan adalah ...', 'answer' => 'Rhizobium'],
                                ['prompt' => 'Tahap metamorfosis katak setelah berudu dan sebelum dewasa disebut ...', 'answer' => 'Kecebong berkaki'],
                                ['prompt' => 'Lapisan lilin pada permukaan daun yang membantu mengurangi penguapan disebut ...', 'answer' => 'Kutikula'],
                            ], 'hard'),
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Sejarah Indonesia',
                'slug' => 'sejarah-indonesia',
                'description' => 'Fokus pada tokoh, organisasi, dan momen penting dalam sejarah Indonesia.',
                'icon' => 'scroll',
                'rounds' => [
                    'qualification' => [
                        'difficulty' => [
                            'easy' => $this->markDifficulty([
                                ['prompt' => 'Tahun proklamasi kemerdekaan Indonesia adalah ...', 'answer' => '1945'],
                                ['prompt' => 'Naskah proklamasi dibacakan oleh ...', 'answer' => 'Soekarno'],
                                ['prompt' => 'Tanggal Sumpah Pemuda diperingati setiap ...', 'answer' => '28 Oktober'],
                                ['prompt' => 'Pahlawan wanita asal Jepara yang dikenal memperjuangkan emansipasi adalah ...', 'answer' => 'Kartini'],
                                ['prompt' => 'Peristiwa perobekan bendera Belanda di Hotel Yamato terjadi di kota ...', 'answer' => 'Surabaya'],
                                ['prompt' => 'Lagu Indonesia Raya pertama kali diperdengarkan saat peristiwa ...', 'answer' => 'Sumpah Pemuda'],
                                ['prompt' => 'Pendiri organisasi Budi Utomo adalah dr. ...', 'answer' => 'Sutomo'],
                                ['prompt' => 'Hari Kebangkitan Nasional diperingati pada tanggal ...', 'answer' => '20 Mei'],
                                ['prompt' => 'Tokoh yang dikenal dengan sebutan Bapak Pendidikan Nasional adalah ...', 'answer' => 'Ki Hajar Dewantara'],
                                ['prompt' => 'Teks Proklamasi diketik oleh ...', 'answer' => 'Sayuti Melik'],
                                ['prompt' => 'Lembaga yang mempersiapkan kemerdekaan Indonesia pada masa Jepang adalah ...', 'answer' => 'BPUPKI'],
                                ['prompt' => 'Ibu kota Republik Indonesia pertama setelah proklamasi adalah ...', 'answer' => 'Jakarta'],
                            ], 'easy'),
                            'medium' => $this->markDifficulty([
                                ['prompt' => 'Perundingan yang mengakui wilayah RI secara de facto di Jawa, Madura, dan Sumatra adalah ...', 'answer' => 'Linggarjati'],
                                ['prompt' => 'Agresi Militer Belanda II terjadi pada tahun ...', 'answer' => '1948'],
                                ['prompt' => 'Wakil presiden pertama Republik Indonesia adalah ...', 'answer' => 'Mohammad Hatta'],
                                ['prompt' => 'Pemimpin perang gerilya setelah Agresi Militer Belanda II adalah Jenderal ...', 'answer' => 'Sudirman'],
                                ['prompt' => 'Organisasi pemuda bentukan Jepang yang melatih kedisiplinan militer adalah ...', 'answer' => 'PETA'],
                                ['prompt' => 'Konferensi Asia Afrika dilaksanakan di Bandung pada tahun ...', 'answer' => '1955'],
                                ['prompt' => 'Gerakan Reformasi di Indonesia memuncak pada tahun ...', 'answer' => '1998'],
                                ['prompt' => 'Mosi integral yang menyatukan negara bagian RIS dipelopori oleh ...', 'answer' => 'Mohammad Natsir'],
                            ], 'medium'),
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Tokoh yang menjahit bendera pusaka Merah Putih adalah ...', 'answer' => 'Fatmawati'],
                                ['prompt' => 'Kabinet pertama Republik Indonesia dipimpin secara administratif oleh ...', 'answer' => 'Sutan Sjahrir'],
                                ['prompt' => 'Peristiwa Rengasdengklok terjadi pada tanggal ...', 'answer' => '16 Agustus 1945'],
                                ['prompt' => 'Piagam Jakarta dirumuskan oleh panitia yang dikenal sebagai ...', 'answer' => 'Panitia Sembilan'],
                                ['prompt' => 'Operasi Trikora dicanangkan untuk merebut ...', 'answer' => 'Irian Barat'],
                            ], 'hard'),
                        ],
                    ],
                    'semifinal' => [
                        'difficulty' => [
                            'easy' => $this->markDifficulty([
                                ['prompt' => 'Pahlawan asal Aceh yang terkenal memimpin perang melawan Belanda adalah ...', 'answer' => 'Cut Nyak Dhien'],
                                ['prompt' => 'Kerajaan maritim besar di Sumatra yang terkenal pada abad ke-7 adalah ...', 'answer' => 'Sriwijaya'],
                                ['prompt' => 'Kerajaan besar di Jawa Timur yang mencapai puncak kejayaan pada masa Hayam Wuruk adalah ...', 'answer' => 'Majapahit'],
                                ['prompt' => 'Deklarasi Djuanda diumumkan pada tahun ...', 'answer' => '1957'],
                                ['prompt' => 'Bahasa persatuan Indonesia disepakati dalam peristiwa ...', 'answer' => 'Sumpah Pemuda'],
                            ], 'easy'),
                            'medium' => $this->markDifficulty([
                                ['prompt' => 'Presiden kedua Republik Indonesia adalah ...', 'answer' => 'Soeharto'],
                                ['prompt' => 'Sistem tanam paksa pada masa kolonial Belanda dikenal dengan istilah ...', 'answer' => 'Cultuurstelsel'],
                                ['prompt' => 'Lembaga bentukan Jepang yang bertugas membantu kepolisian disebut ...', 'answer' => 'Keibodan'],
                                ['prompt' => 'Perundingan yang menghasilkan pembentukan Republik Indonesia Serikat adalah ...', 'answer' => 'KMB'],
                                ['prompt' => 'Tokoh yang memimpin DI/TII di Jawa Barat adalah ...', 'answer' => 'Kartosuwiryo'],
                            ], 'medium'),
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Pemberontakan PKI di Madiun terjadi pada tahun ...', 'answer' => '1948'],
                                ['prompt' => 'Naskah Supersemar ditandatangani pada tanggal ...', 'answer' => '11 Maret 1966'],
                                ['prompt' => 'Pelopor politik etis di Belanda yang sering dikaitkan dengan Trilogi van Deventer adalah ...', 'answer' => 'Van Deventer'],
                                ['prompt' => 'Tokoh yang memimpin ekspedisi Pamalayu dari Singasari adalah ...', 'answer' => 'Kertanegara'],
                                ['prompt' => 'Istilah untuk masa pendudukan Jepang yang menempatkan kerja paksa rakyat Indonesia adalah ...', 'answer' => 'Romusha'],
                            ], 'hard'),
                        ],
                    ],
                    'final' => [
                        'difficulty' => [
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Tokoh pers yang mendirikan surat kabar Medan Prijaji adalah ...', 'answer' => 'Tirto Adhi Soerjo'],
                                ['prompt' => 'Ikrar Sumpah Pemuda dibacakan pada kongres pemuda yang ke- ...', 'answer' => 'Kedua'],
                                ['prompt' => 'Kesultanan yang dikenal kuat di Maluku sebagai pusat perdagangan rempah adalah ...', 'answer' => 'Ternate'],
                                ['prompt' => 'Nama operasi militer untuk membebaskan sandera di Woyla pada 1981 dipimpin pasukan ...', 'answer' => 'Kopassandha'],
                                ['prompt' => 'Peristiwa Malari terjadi pada tahun ...', 'answer' => '1974'],
                                ['prompt' => 'Majalah yang diasosiasikan erat dengan perjuangan intelektual pergerakan nasional di bawah HOS Tjokroaminoto adalah ...', 'answer' => 'Oetoesan Hindia'],
                                ['prompt' => 'Serangan Umum 1 Maret 1949 berlangsung di kota ...', 'answer' => 'Yogyakarta'],
                                ['prompt' => 'Ketua BPUPKI yang berasal dari Jepang adalah ...', 'answer' => 'Radjiman Wedyodiningrat'],
                            ], 'hard'),
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Teknologi Digital',
                'slug' => 'teknologi-digital',
                'description' => 'Topik komputer, internet, aplikasi modern, dan konsep teknologi masa kini.',
                'icon' => 'cpu',
                'rounds' => [
                    'qualification' => [
                        'difficulty' => [
                            'easy' => $this->markDifficulty([
                                ['prompt' => 'Perangkat keras utama yang sering disebut otak komputer adalah ...', 'answer' => 'CPU'],
                                ['prompt' => 'Tempat penyimpanan data sementara saat komputer bekerja adalah ...', 'answer' => 'RAM'],
                                ['prompt' => 'Aplikasi untuk menjelajah internet disebut ...', 'answer' => 'Browser'],
                                ['prompt' => 'Tombol kombinasi umum untuk menyalin teks di komputer adalah ...', 'answer' => 'Ctrl+C'],
                                ['prompt' => 'Jaringan global yang menghubungkan komputer di seluruh dunia disebut ...', 'answer' => 'Internet'],
                                ['prompt' => 'Alamat unik sebuah situs web disebut ...', 'answer' => 'Domain'],
                                ['prompt' => 'Program berbahaya yang dapat merusak sistem komputer disebut ...', 'answer' => 'Virus'],
                                ['prompt' => 'Layanan penyimpanan data melalui internet dikenal sebagai ...', 'answer' => 'Cloud'],
                                ['prompt' => 'Aplikasi lembar kerja populer dari Microsoft adalah ...', 'answer' => 'Excel'],
                                ['prompt' => 'Perangkat yang mencetak dokumen ke kertas adalah ...', 'answer' => 'Printer'],
                                ['prompt' => 'Mesin pencari paling populer di dunia adalah ...', 'answer' => 'Google'],
                                ['prompt' => 'Sistem operasi pada banyak smartphone non-Apple adalah ...', 'answer' => 'Android'],
                            ], 'easy'),
                            'medium' => $this->markDifficulty([
                                ['prompt' => 'Teknologi untuk mengamankan data dengan mengubahnya ke bentuk sandi disebut ...', 'answer' => 'Enkripsi'],
                                ['prompt' => 'Format file gambar yang mendukung latar transparan adalah ...', 'answer' => 'PNG'],
                                ['prompt' => 'Istilah untuk serangan yang mencoba mencuri data dengan menyamar sebagai pihak terpercaya adalah ...', 'answer' => 'Phishing'],
                                ['prompt' => 'Bahasa pemrograman yang umum digunakan untuk halaman web interaktif adalah ...', 'answer' => 'JavaScript'],
                                ['prompt' => 'Alamat numerik perangkat di jaringan disebut ...', 'answer' => 'IP address'],
                                ['prompt' => 'Sistem yang digunakan untuk mengelola basis data disebut ...', 'answer' => 'DBMS'],
                                ['prompt' => 'Tombol kombinasi untuk menempelkan hasil salinan di komputer adalah ...', 'answer' => 'Ctrl+V'],
                                ['prompt' => 'Program yang menerjemahkan nama domain menjadi alamat IP adalah ...', 'answer' => 'DNS'],
                            ], 'medium'),
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Model pengembangan perangkat lunak yang menekankan iterasi cepat dan kolaborasi disebut ...', 'answer' => 'Agile'],
                                ['prompt' => 'Teknik verifikasi tambahan selain kata sandi dikenal sebagai ...', 'answer' => 'MFA'],
                                ['prompt' => 'Jenis serangan yang membanjiri server dengan lalu lintas palsu disebut ...', 'answer' => 'DDoS'],
                                ['prompt' => 'Versi kontrol yang sangat populer untuk pengembangan perangkat lunak modern adalah ...', 'answer' => 'Git'],
                                ['prompt' => 'Struktur data yang bekerja dengan prinsip first in first out disebut ...', 'answer' => 'Queue'],
                            ], 'hard'),
                        ],
                    ],
                    'semifinal' => [
                        'difficulty' => [
                            'easy' => $this->markDifficulty([
                                ['prompt' => 'Perangkat yang menghubungkan komputer ke jaringan lokal secara nirkabel disebut ...', 'answer' => 'Router'],
                                ['prompt' => 'Ikon tempat file yang dihapus sementara pada komputer Windows disebut ...', 'answer' => 'Recycle Bin'],
                                ['prompt' => 'Aplikasi presentasi dari Microsoft Office adalah ...', 'answer' => 'PowerPoint'],
                                ['prompt' => 'Perangkat lunak untuk menghapus malware dari komputer disebut ...', 'answer' => 'Antivirus'],
                                ['prompt' => 'Fitur keamanan untuk membuka ponsel dengan sidik jari termasuk ...', 'answer' => 'Biometrik'],
                            ], 'easy'),
                            'medium' => $this->markDifficulty([
                                ['prompt' => 'Bagian URL yang menunjukkan protokol pengiriman web aman adalah ...', 'answer' => 'HTTPS'],
                                ['prompt' => 'Penyimpanan data permanen solid-state modern banyak menggunakan teknologi ...', 'answer' => 'SSD'],
                                ['prompt' => 'Konsep menjalankan aplikasi dalam wadah terisolasi dikenal sebagai ...', 'answer' => 'Container'],
                                ['prompt' => 'Layanan yang memungkinkan banyak pengguna bekerja pada dokumen yang sama secara daring disebut ...', 'answer' => 'Kolaborasi real-time'],
                                ['prompt' => 'Bahasa markah dasar untuk struktur halaman web adalah ...', 'answer' => 'HTML'],
                            ], 'medium'),
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Serangan yang memanfaatkan celah keamanan yang belum ditambal disebut ...', 'answer' => 'Zero-day'],
                                ['prompt' => 'Metode pemrograman yang mengorganisasi kode ke dalam objek dan kelas disebut ...', 'answer' => 'OOP'],
                                ['prompt' => 'Protokol yang umum digunakan untuk transfer file terenkripsi adalah ...', 'answer' => 'SFTP'],
                                ['prompt' => 'Model komputasi yang menyediakan server virtual sesuai kebutuhan melalui internet disebut ...', 'answer' => 'IaaS'],
                                ['prompt' => 'Sistem yang memecah proses build, test, dan deploy otomatis dikenal sebagai ...', 'answer' => 'CI/CD'],
                            ], 'hard'),
                        ],
                    ],
                    'final' => [
                        'difficulty' => [
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Algoritma kriptografi yang umum dipakai untuk hash kata sandi modern adalah ...', 'answer' => 'Bcrypt'],
                                ['prompt' => 'Arsitektur aplikasi yang memecah sistem menjadi layanan-layanan kecil disebut ...', 'answer' => 'Microservices'],
                                ['prompt' => 'Istilah untuk area penyimpanan cepat di prosesor yang lebih kecil daripada RAM adalah ...', 'answer' => 'Cache'],
                                ['prompt' => 'Teknik menyusun data untuk mempercepat pencarian pada database disebut ...', 'answer' => 'Indexing'],
                                ['prompt' => 'Model bahasa besar yang digunakan untuk memahami dan menghasilkan teks sering disingkat ...', 'answer' => 'LLM'],
                                ['prompt' => 'Pola desain yang memisahkan tampilan, logika, dan data secara umum dikenal sebagai ...', 'answer' => 'MVC'],
                                ['prompt' => 'Sistem yang membatasi jumlah permintaan ke API dalam jangka waktu tertentu disebut ...', 'answer' => 'Rate limiting'],
                                ['prompt' => 'Kumpulan aturan yang menentukan bentuk komunikasi antarperangkat lunak disebut ...', 'answer' => 'API'],
                            ], 'hard'),
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Bahasa Indonesia',
                'slug' => 'bahasa-indonesia',
                'description' => 'Soal sinonim, antonim, makna kata, dan ejaan dalam bahasa Indonesia.',
                'icon' => 'book-open',
                'rounds' => [
                    'qualification' => [
                        'difficulty' => [
                            'easy' => $this->markDifficulty([
                                ['prompt' => 'Antonim dari kata "besar" adalah ...', 'answer' => 'Kecil'],
                                ['prompt' => 'Sinonim dari kata "indah" adalah ...', 'answer' => 'Elok'],
                                ['prompt' => 'Antonim dari kata "panjang" adalah ...', 'answer' => 'Pendek'],
                                ['prompt' => 'Sinonim dari kata "cepat" adalah ...', 'answer' => 'Lekas'],
                                ['prompt' => 'Antonim dari kata "tinggi" adalah ...', 'answer' => 'Rendah'],
                                ['prompt' => 'Sinonim dari kata "rajin" adalah ...', 'answer' => 'Giat'],
                                ['prompt' => 'Antonim dari kata "terang" adalah ...', 'answer' => 'Gelap'],
                                ['prompt' => 'Sinonim dari kata "murid" adalah ...', 'answer' => 'Siswa'],
                                ['prompt' => 'Antonim dari kata "maju" adalah ...', 'answer' => 'Mundur'],
                                ['prompt' => 'Sinonim dari kata "senang" adalah ...', 'answer' => 'Gembira'],
                                ['prompt' => 'Antonim dari kata "ramai" adalah ...', 'answer' => 'Sepi'],
                                ['prompt' => 'Sinonim dari kata "pintar" adalah ...', 'answer' => 'Cerdas'],
                            ], 'easy'),
                            'medium' => $this->markDifficulty([
                                ['prompt' => 'Sinonim dari kata "cermat" adalah ...', 'answer' => 'Teliti'],
                                ['prompt' => 'Antonim dari kata "optimis" adalah ...', 'answer' => 'Pesimis'],
                                ['prompt' => 'Sinonim dari kata "absah" adalah ...', 'answer' => 'Sah'],
                                ['prompt' => 'Antonim dari kata "konkret" adalah ...', 'answer' => 'Abstrak'],
                                ['prompt' => 'Sinonim dari kata "lugas" adalah ...', 'answer' => 'Jelas'],
                                ['prompt' => 'Antonim dari kata "stabil" adalah ...', 'answer' => 'Labil'],
                                ['prompt' => 'Sinonim dari kata "usang" adalah ...', 'answer' => 'Lama'],
                                ['prompt' => 'Antonim dari kata "rinci" adalah ...', 'answer' => 'Umum'],
                            ], 'medium'),
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Makna kata "paripurna" yang paling tepat adalah ...', 'answer' => 'Lengkap'],
                                ['prompt' => 'Sinonim dari kata "gagasan" adalah ...', 'answer' => 'Ide'],
                                ['prompt' => 'Antonim dari kata "subur" adalah ...', 'answer' => 'Tandus'],
                                ['prompt' => 'Sinonim dari kata "sintesis" adalah ...', 'answer' => 'Paduan'],
                                ['prompt' => 'Antonim dari kata "legitim" dalam konteks makna adalah ...', 'answer' => 'Ilegal'],
                            ], 'hard'),
                        ],
                    ],
                    'semifinal' => [
                        'difficulty' => [
                            'easy' => $this->markDifficulty([
                                ['prompt' => 'Antonim dari kata "siang" adalah ...', 'answer' => 'Malam'],
                                ['prompt' => 'Sinonim dari kata "cantik" adalah ...', 'answer' => 'Ayu'],
                                ['prompt' => 'Antonim dari kata "panas" adalah ...', 'answer' => 'Dingin'],
                                ['prompt' => 'Sinonim dari kata "berani" adalah ...', 'answer' => 'Gagah'],
                                ['prompt' => 'Antonim dari kata "baru" adalah ...', 'answer' => 'Lama'],
                            ], 'easy'),
                            'medium' => $this->markDifficulty([
                                ['prompt' => 'Sinonim dari kata "andal" adalah ...', 'answer' => 'Tangguh'],
                                ['prompt' => 'Antonim dari kata "ekspansif" adalah ...', 'answer' => 'Restriktif'],
                                ['prompt' => 'Sinonim dari kata "capaian" adalah ...', 'answer' => 'Prestasi'],
                                ['prompt' => 'Antonim dari kata "akurat" adalah ...', 'answer' => 'Keliru'],
                                ['prompt' => 'Sinonim dari kata "wacana" adalah ...', 'answer' => 'Bahasan'],
                            ], 'medium'),
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Makna kata "implisit" adalah ...', 'answer' => 'Tersirat'],
                                ['prompt' => 'Antonim dari kata "objektif" adalah ...', 'answer' => 'Subjektif'],
                                ['prompt' => 'Sinonim dari kata "konsekuen" adalah ...', 'answer' => 'Konsisten'],
                                ['prompt' => 'Antonim dari kata "progresif" adalah ...', 'answer' => 'Konservatif'],
                                ['prompt' => 'Sinonim dari kata "esensial" adalah ...', 'answer' => 'Pokok'],
                            ], 'hard'),
                        ],
                    ],
                    'final' => [
                        'difficulty' => [
                            'hard' => $this->markDifficulty([
                                ['prompt' => 'Makna kata "ambigu" adalah ...', 'answer' => 'Bermakna ganda'],
                                ['prompt' => 'Sinonim dari kata "pragmatis" adalah ...', 'answer' => 'Praktis'],
                                ['prompt' => 'Antonim dari kata "kredibel" adalah ...', 'answer' => 'Meragukan'],
                                ['prompt' => 'Makna kata "parsial" adalah ...', 'answer' => 'Sebagian'],
                                ['prompt' => 'Sinonim dari kata "inovatif" adalah ...', 'answer' => 'Kreatif'],
                                ['prompt' => 'Antonim dari kata "koheren" adalah ...', 'answer' => 'Acak'],
                                ['prompt' => 'Makna kata "vernakular" yang paling tepat dalam konteks bahasa adalah ...', 'answer' => 'Daerah'],
                                ['prompt' => 'Sinonim dari kata "akomodatif" adalah ...', 'answer' => 'Fleksibel'],
                            ], 'hard'),
                        ],
                    ],
                ],
            ],
        ];
    }

    private function markDifficulty(array $items, string $difficulty): array
    {
        return array_map(
            fn (array $item): array => [...$item, 'difficulty' => $difficulty],
            $items,
        );
    }
}
