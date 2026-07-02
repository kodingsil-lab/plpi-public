<?= $this->extend('public/layouts/main') ?>
<?= $this->section('content') ?>

<section class="section loa-request-section">
    <div class="container">
        <div class="loa-page-title">
            <h1>Ajukan Letter of Acceptance Anda</h1>
        </div>

        <?php if (session('error')): ?>
            <div class="public-alert error"><?= esc((string) session('error')) ?></div>
        <?php endif; ?>

        <form class="loa-public-form" method="post" action="<?= site_url('ajukan-loa') ?>">
            <div class="loa-form-panel">
                <div class="loa-form-heading">
                    <span>01</span>
                    <div>
                        <h2>Data Artikel</h2>
                        <p>Pilih jurnal dan isi metadata artikel yang akan diajukan.</p>
                    </div>
                </div>

                <div class="loa-form-grid">
                    <label class="span-2">
                        <span>Jurnal</span>
                        <select name="journal_id" required>
                            <option value="">Pilih Jurnal</option>
                            <?php foreach (($journals ?? []) as $journal): ?>
                                <option value="<?= (int) $journal['id'] ?>" <?= (int) old('journal_id') === (int) $journal['id'] ? 'selected' : '' ?>>
                                    <?= esc((string) $journal['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="span-2">
                        <span>Judul Artikel</span>
                        <textarea name="title" rows="3" required placeholder="Masukkan judul artikel"><?= esc((string) old('title')) ?></textarea>
                    </label>

                    <label>
                        <span>Email Korespondensi</span>
                        <input id="correspondingEmail" type="email" name="corresponding_email" value="<?= esc((string) old('corresponding_email'), 'attr') ?>" required placeholder="contoh@gmail.com">
                    </label>

                    <label>
                        <span>Nomor WhatsApp</span>
                        <input name="whatsapp_number" value="<?= esc((string) old('whatsapp_number'), 'attr') ?>" required placeholder="0812xxxxxx / +62812xxxxxx">
                    </label>

                    <label>
                        <span>Volume</span>
                        <input name="volume" value="<?= esc((string) old('volume'), 'attr') ?>" placeholder="Contoh: 12">
                    </label>

                    <label>
                        <span>Nomor</span>
                        <input name="issue_number" value="<?= esc((string) old('issue_number'), 'attr') ?>" placeholder="Contoh: 2">
                    </label>

                    <label>
                        <span>URL Artikel</span>
                        <input type="url" name="article_url" value="<?= esc((string) old('article_url'), 'attr') ?>" placeholder="https://...">
                    </label>

                    <label>
                        <span>Tahun</span>
                        <input name="published_year" value="<?= esc((string) old('published_year'), 'attr') ?>" placeholder="<?= date('Y') ?>">
                    </label>
                </div>
            </div>

            <div class="loa-form-panel">
                <div class="loa-form-heading">
                    <span>02</span>
                    <div>
                        <h2>Identitas Penulis</h2>
                        <p>Tambahkan ketua dan anggota penulis. Nama disimpan dalam daftar terstruktur.</p>
                    </div>
                </div>

                <div class="loa-builder">
                    <div class="loa-builder-row">
                        <select id="authorRole">
                            <option value="ketua">Ketua</option>
                            <option value="anggota">Anggota</option>
                        </select>
                        <input id="authorName" placeholder="Isi nama penulis tanpa gelar">
                        <button type="button" id="addAuthorBtn">Tambah</button>
                    </div>
                    <div id="authorList" class="loa-builder-list"></div>
                    <textarea class="is-hidden" name="authors_text" id="authorsText"><?= esc((string) old('authors_text')) ?></textarea>
                </div>
            </div>

            <div class="loa-form-panel">
                <div class="loa-form-heading">
                    <span>03</span>
                    <div>
                        <h2>Afiliasi Penulis</h2>
                        <p>Gunakan satu afiliasi untuk semua penulis, atau atur berbeda untuk tiap peran.</p>
                    </div>
                </div>

                <div class="loa-affiliation-mode">
                    <label class="loa-check">
                        <input type="checkbox" id="affSameForAll" checked>
                        <span>Afiliasi sama untuk semua penulis</span>
                    </label>

                    <div id="affSingleWrap" class="loa-affiliation-box">
                        <input id="affSingleInput" placeholder="Isi nama institusi atau perguruan tinggi">
                    </div>

                    <div id="affListWrap" class="loa-affiliation-box is-hidden">
                        <div class="loa-builder-row">
                            <select id="affRoleSelect">
                                <option value="">Tambahkan penulis dulu</option>
                            </select>
                            <input id="affLineInput" placeholder="Ketik afiliasi penulis">
                            <button type="button" id="addAffBtn">Tambah</button>
                        </div>
                        <div id="affList" class="loa-builder-list"></div>
                    </div>

                    <textarea class="is-hidden" name="affiliations_text" id="affiliationsText"><?= esc((string) old('affiliations_text')) ?></textarea>
                </div>
            </div>

            <div class="loa-form-actions">
                <a href="<?= site_url('/') ?>" class="btn-secondary">Kembali</a>
                <button type="submit" class="btn-primary">Kirim Permohonan</button>
            </div>
        </form>
    </div>
</section>

<script>
    (() => {
        const form = document.querySelector('.loa-public-form');
        if (!form) return;

        const allowedEmailDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'live.com', 'icloud.com', 'aol.com', 'proton.me', 'protonmail.com', 'zoho.com', 'gmx.com', 'mail.com', 'yandex.com'];
        const emailInput = document.getElementById('correspondingEmail');
        const roleSelect = document.getElementById('authorRole');
        const nameInput = document.getElementById('authorName');
        const addAuthorBtn = document.getElementById('addAuthorBtn');
        const authorList = document.getElementById('authorList');
        const authorsText = document.getElementById('authorsText');
        const affSameForAll = document.getElementById('affSameForAll');
        const affSingleWrap = document.getElementById('affSingleWrap');
        const affSingleInput = document.getElementById('affSingleInput');
        const affListWrap = document.getElementById('affListWrap');
        const affRoleSelect = document.getElementById('affRoleSelect');
        const affLineInput = document.getElementById('affLineInput');
        const addAffBtn = document.getElementById('addAffBtn');
        const affList = document.getElementById('affList');
        const affiliationsText = document.getElementById('affiliationsText');

        const authors = [];
        const affiliations = [];

        function validateEmail() {
            const value = emailInput.value.trim().toLowerCase();
            const domain = value.includes('@') ? value.split('@').pop() : '';
            const valid = !value || allowedEmailDomains.includes(domain);
            emailInput.setCustomValidity(valid ? '' : 'Gunakan domain email umum yang diizinkan.');
            return valid;
        }

        function renumberAuthors() {
            let member = 1;
            authors.forEach((author) => {
                if (author.role === 'Ketua') return;
                author.role = `Anggota ${member}`;
                member += 1;
            });
        }

        function syncAuthors() {
            authorsText.value = authors.map((author) => `${author.role}: ${author.name}`).join('\n');
        }

        function renderAuthors() {
            authorList.innerHTML = '';
            if (!authors.length) {
                authorList.innerHTML = '<div class="loa-builder-empty">Belum ada penulis ditambahkan.</div>';
                syncAuthors();
                updateAffRoles();
                return;
            }

            authors.forEach((author, index) => {
                const item = document.createElement('div');
                item.className = 'loa-builder-item';
                item.innerHTML = `<div><strong>${author.role}</strong><span>${author.name}</span></div><button type="button" data-author-index="${index}">Hapus</button>`;
                authorList.appendChild(item);
            });
            syncAuthors();
            updateAffRoles();
        }

        function updateAffRoles() {
            const current = affRoleSelect.value;
            affRoleSelect.innerHTML = authors.length ? '<option value="">Pilih peran penulis</option>' : '<option value="">Tambahkan penulis dulu</option>';
            authors.forEach((author) => {
                const option = document.createElement('option');
                option.value = author.role;
                option.textContent = author.role;
                affRoleSelect.appendChild(option);
            });
            if (current && [...affRoleSelect.options].some(option => option.value === current)) {
                affRoleSelect.value = current;
            }
        }

        function syncAffiliations() {
            if (affSameForAll.checked) {
                affiliationsText.value = affSingleInput.value.trim();
                return;
            }
            affiliationsText.value = affiliations.map((item) => `${item.role}: ${item.affiliation}`).join('\n');
        }

        function renderAffiliations() {
            affList.innerHTML = '';
            if (!affiliations.length) {
                affList.innerHTML = '<div class="loa-builder-empty">Belum ada afiliasi ditambahkan.</div>';
                syncAffiliations();
                return;
            }

            affiliations.forEach((item, index) => {
                const row = document.createElement('div');
                row.className = 'loa-builder-item';
                row.innerHTML = `<div><strong>${item.role}</strong><span>${item.affiliation}</span></div><button type="button" data-aff-index="${index}">Hapus</button>`;
                affList.appendChild(row);
            });
            syncAffiliations();
        }

        function toggleAffMode() {
            affSingleWrap.classList.toggle('is-hidden', !affSameForAll.checked);
            affListWrap.classList.toggle('is-hidden', affSameForAll.checked);
            syncAffiliations();
        }

        function parseInitialAuthors() {
            const raw = authorsText.value.trim();
            if (!raw) {
                renderAuthors();
                return;
            }

            raw.split(/\r?\n/).map(line => line.trim()).filter(Boolean).forEach((line, index) => {
                const match = line.match(/^([^:|-]+)\s*[:|-]\s*(.+)$/);
                authors.push({
                    role: match ? (match[1].toLowerCase().startsWith('ketua') ? 'Ketua' : 'Anggota') : (index === 0 ? 'Ketua' : 'Anggota'),
                    name: match ? match[2].trim() : line
                });
            });
            renumberAuthors();
            renderAuthors();
        }

        function parseInitialAffiliations() {
            const raw = affiliationsText.value.trim();
            if (!raw) {
                renderAffiliations();
                return;
            }

            const lines = raw.split(/\r?\n/).map(line => line.trim()).filter(Boolean);
            if (lines.length <= 1 && !lines[0]?.includes(':')) {
                affSameForAll.checked = true;
                affSingleInput.value = lines[0] || '';
            } else {
                affSameForAll.checked = false;
                lines.forEach((line) => {
                    const match = line.match(/^([^:]+)\s*:\s*(.+)$/);
                    affiliations.push({ role: match ? match[1].trim() : 'Afiliasi', affiliation: match ? match[2].trim() : line });
                });
            }
            renderAffiliations();
            toggleAffMode();
        }

        addAuthorBtn.addEventListener('click', () => {
            const name = nameInput.value.trim();
            if (!name) {
                nameInput.focus();
                return;
            }

            if (roleSelect.value === 'ketua') {
                if (authors.some(author => author.role === 'Ketua')) {
                    alert('Ketua sudah ditambahkan. Gunakan peran Anggota untuk penulis berikutnya.');
                    return;
                }
                authors.push({ role: 'Ketua', name });
            } else {
                authors.push({ role: 'Anggota', name });
                renumberAuthors();
            }

            nameInput.value = '';
            nameInput.focus();
            renderAuthors();
        });

        authorList.addEventListener('click', (event) => {
            const button = event.target.closest('[data-author-index]');
            if (!button) return;
            authors.splice(Number(button.dataset.authorIndex), 1);
            renumberAuthors();
            renderAuthors();
        });

        addAffBtn.addEventListener('click', () => {
            const role = affRoleSelect.value;
            const affiliation = affLineInput.value.trim();
            if (!role) {
                affRoleSelect.focus();
                return;
            }
            if (!affiliation) {
                affLineInput.focus();
                return;
            }
            affiliations.push({ role, affiliation });
            affLineInput.value = '';
            affLineInput.focus();
            renderAffiliations();
        });

        affList.addEventListener('click', (event) => {
            const button = event.target.closest('[data-aff-index]');
            if (!button) return;
            affiliations.splice(Number(button.dataset.affIndex), 1);
            renderAffiliations();
        });

        emailInput.addEventListener('input', validateEmail);
        emailInput.addEventListener('blur', validateEmail);
        affSameForAll.addEventListener('change', toggleAffMode);
        affSingleInput.addEventListener('input', syncAffiliations);

        form.addEventListener('submit', (event) => {
            syncAuthors();
            syncAffiliations();

            if (!validateEmail()) {
                event.preventDefault();
                emailInput.reportValidity();
                return;
            }
            if (!authors.length) {
                event.preventDefault();
                alert('Tambahkan minimal satu penulis.');
                nameInput.focus();
                return;
            }
        });

        parseInitialAuthors();
        parseInitialAffiliations();
    })();
</script>

<?= $this->endSection() ?>
