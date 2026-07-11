<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$templatePayload = [];
foreach (($templates ?? []) as $template) {
    $templatePayload[] = [
        'id' => (string) ($template['id'] ?? ''),
        'name' => (string) ($template['name'] ?? ''),
        'subject' => (string) ($template['subject'] ?? ''),
        'message' => (string) ($template['message'] ?? ''),
    ];
}
?>
<section class="admin-panel user-form-panel">
    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <?php if (empty($mailReady)): ?>
        <div class="admin-alert warning">
            SMTP email resmi belum lengkap. Isi konfigurasi di
            <a href="<?= site_url('dashboard/settings/application') ?>">Pengaturan Aplikasi</a>.
        </div>
    <?php endif; ?>

    <form class="whatsapp-compose" method="post" action="<?= site_url('dashboard/messages/email/send') ?>">
        <input type="hidden" name="subject" id="subjectInput" value="<?= esc((string) old('subject'), 'attr') ?>">
        <input type="hidden" name="message" id="messageInput" value="<?= esc((string) old('message'), 'attr') ?>">

        <div class="whatsapp-compose-card">
            <div class="whatsapp-compose-head">
                <h3><iconify-icon icon="mdi:email-send-outline"></iconify-icon>Data Email</h3>
                <a class="admin-btn secondary" href="<?= site_url('dashboard/messages/templates') ?>">Template</a>
            </div>

            <div class="whatsapp-compose-body">
                <label>
                    <span>Nama Jurnal</span>
                    <select name="nama_jurnal" id="journalNameInput">
                        <option value="">Pilih jurnal</option>
                        <?php foreach (($journals ?? []) as $journal): ?>
                            <?php
                                $journalName = (string) ($journal['name'] ?? '');
                                $journalUrl = (string) ($journal['website_url'] ?? '');
                                $commitmentUrl = (string) ($journal['commitment_statement_url'] ?? '');
                            ?>
                            <option
                                value="<?= esc($journalName, 'attr') ?>"
                                data-url="<?= esc($journalUrl, 'attr') ?>"
                                data-commitment-url="<?= esc($commitmentUrl, 'attr') ?>"
                                <?= old('nama_jurnal') === $journalName ? 'selected' : '' ?>
                            >
                                <?= esc($journalName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Pilih Template</span>
                    <select name="template_id" id="templateSelect">
                        <option value="">Tulis manual</option>
                        <?php foreach (($templates ?? []) as $template): ?>
                            <option value="<?= (int) $template['id'] ?>" <?= (string) old('template_id') === (string) $template['id'] ? 'selected' : '' ?>>
                                <?= esc((string) $template['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Nama Penerima</span>
                    <input name="recipient_name" id="recipientNameInput" value="<?= esc((string) old('recipient_name'), 'attr') ?>" placeholder="Bapak/Ibu Penulis">
                </label>

                <label>
                    <span>Email Penerima</span>
                    <input type="email" name="recipient_email" value="<?= esc((string) old('recipient_email'), 'attr') ?>" placeholder="penulis@email.com" required>
                </label>

                <label>
                    <span>Judul Artikel</span>
                    <input id="articleTitleInput" value="<?= esc((string) old('judul_artikel'), 'attr') ?>" placeholder="Masukkan judul artikel">
                </label>

                <div class="placeholder-box">
                    <span>Placeholder Template</span>
                    <div>
                        <button type="button" data-token="{nama_penerima}">{nama_penerima}</button>
                        <button type="button" data-token="{judul_artikel}">{judul_artikel}</button>
                        <button type="button" data-token="{judul}">{judul}</button>
                        <button type="button" data-token="{nama_jurnal}">{nama_jurnal}</button>
                        <button type="button" data-token="{jurnal}">{jurnal}</button>
                        <button type="button" data-token="{link_jurnal}">{link_jurnal}</button>
                        <button type="button" data-token="{link jurnal}">{link jurnal}</button>
                        <button type="button" data-token="{pernyataan_komitmen_penulis}">{pernyataan_komitmen_penulis}</button>
                        <button type="button" data-token="{pernyataan komitmen penulis}">{pernyataan komitmen penulis}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="whatsapp-compose-card preview-card">
            <div class="whatsapp-compose-head">
                <h3><iconify-icon icon="mdi:email-newsletter"></iconify-icon>Preview Email</h3>
            </div>

            <div class="whatsapp-preview-wrap">
                <label>
                    <span>Subjek</span>
                    <input id="previewSubject" value="<?= esc((string) old('subject'), 'attr') ?>" required>
                </label>
                <textarea id="previewMessage" rows="16" data-editor="plain" required><?= esc((string) old('message')) ?></textarea>
            </div>

            <div class="whatsapp-preview-actions">
                <button class="admin-btn secondary" type="button" id="refreshPreviewBtn">Refresh Preview</button>
                <button class="admin-btn primary" type="submit" <?= empty($mailReady) ? 'disabled' : '' ?>>Kirim Email</button>
            </div>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const templates = <?= json_encode($templatePayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const select = document.getElementById('templateSelect');
    const subjectInput = document.getElementById('subjectInput');
    const messageInput = document.getElementById('messageInput');
    const previewSubject = document.getElementById('previewSubject');
    const previewMessage = document.getElementById('previewMessage');
    const refreshPreviewBtn = document.getElementById('refreshPreviewBtn');
    const recipientNameInput = document.getElementById('recipientNameInput');
    const articleTitleInput = document.getElementById('articleTitleInput');
    const journalNameInput = document.getElementById('journalNameInput');
    const fields = [recipientNameInput, articleTitleInput, journalNameInput];
    let activeSubject = '';
    let activeTemplate = '';
    if (!select || !subjectInput || !messageInput || !previewSubject || !previewMessage) return;

    function selectedTemplate() {
        return templates.find(function (item) {
            return item.id === select.value;
        });
    }

    function selectedJournalUrl() {
        if (!journalNameInput || !journalNameInput.selectedOptions || !journalNameInput.selectedOptions.length) {
            return '';
        }

        return journalNameInput.selectedOptions[0].dataset.url || '';
    }

    function selectedCommitmentUrl() {
        if (!journalNameInput || !journalNameInput.selectedOptions || !journalNameInput.selectedOptions.length) {
            return '';
        }

        return journalNameInput.selectedOptions[0].dataset.commitmentUrl || '';
    }

    function replaceTokens(text) {
        const articleTitle = articleTitleInput && articleTitleInput.value.trim() ? articleTitleInput.value.trim() : '';
        const journalName = journalNameInput && journalNameInput.value.trim() ? journalNameInput.value.trim() : '';
        const recipientName = recipientNameInput && recipientNameInput.value.trim() ? recipientNameInput.value.trim() : '';
        const replacements = {
            '{nama_penerima}': recipientName,
            '{judul_artikel}': articleTitle,
            '{judul}': articleTitle,
            '{nama_jurnal}': journalName,
            '{jurnal}': journalName,
            '{link_jurnal}': selectedJournalUrl(),
            '{link jurnal}': selectedJournalUrl(),
            '{pernyataan_komitmen_penulis}': selectedCommitmentUrl(),
            '{pernyataan komitmen penulis}': selectedCommitmentUrl(),
            '{link_pernyataan_komitmen_penulis}': selectedCommitmentUrl(),
            '{tanggal}': new Date().toLocaleDateString('id-ID'),
            '{nama_admin}': <?= json_encode((string) ($adminName ?? 'Admin')) ?>
        };

        Object.keys(replacements).forEach(function (token) {
            if (replacements[token]) {
                text = text.split(token).join(replacements[token]);
            }
        });

        return text;
    }

    function renderPreview() {
        previewSubject.value = replaceTokens(activeSubject || previewSubject.value);
        previewMessage.value = replaceTokens(activeTemplate || previewMessage.value);
        subjectInput.value = previewSubject.value;
        messageInput.value = previewMessage.value;
    }

    select.addEventListener('change', function () {
        const selected = selectedTemplate();
        if (selected) {
            activeSubject = selected.subject || '';
            activeTemplate = selected.message || '';
        } else {
            activeSubject = previewSubject.value;
            activeTemplate = previewMessage.value;
        }
        renderPreview();
    });

    fields.forEach(function (input) {
        if (input && input.addEventListener) {
            input.addEventListener('input', renderPreview);
            input.addEventListener('change', renderPreview);
        }
    });

    document.querySelectorAll('[data-token]').forEach(function (button) {
        button.addEventListener('click', function () {
            const token = button.getAttribute('data-token') || '';
            previewMessage.focus();
            const start = previewMessage.selectionStart || 0;
            const end = previewMessage.selectionEnd || 0;
            previewMessage.value = previewMessage.value.slice(0, start) + token + previewMessage.value.slice(end);
            previewMessage.selectionStart = previewMessage.selectionEnd = start + token.length;
            activeTemplate = previewMessage.value;
            messageInput.value = previewMessage.value;
        });
    });

    previewSubject.addEventListener('input', function () {
        activeSubject = previewSubject.value;
        subjectInput.value = previewSubject.value;
    });
    previewMessage.addEventListener('input', function () {
        activeTemplate = previewMessage.value;
        messageInput.value = previewMessage.value;
    });
    if (refreshPreviewBtn) {
        refreshPreviewBtn.addEventListener('click', renderPreview);
    }

    if (!previewMessage.value.trim() && templates.length) {
        select.value = templates[0].id;
        activeSubject = templates[0].subject || '';
        activeTemplate = templates[0].message || '';
    } else {
        activeSubject = previewSubject.value;
        activeTemplate = previewMessage.value;
    }
    renderPreview();
});
</script>
<?= $this->endSection() ?>
