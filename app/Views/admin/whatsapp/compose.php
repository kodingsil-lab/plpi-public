<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$templatePayload = [];
foreach (($templates ?? []) as $template) {
    $templatePayload[] = [
        'id' => (string) ($template['id'] ?? ''),
        'name' => (string) ($template['name'] ?? ''),
        'message' => (string) ($template['message'] ?? ''),
    ];
}
?>
<section class="admin-panel user-form-panel">
    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <form class="whatsapp-compose" method="post" action="<?= site_url('dashboard/messages/whatsapp/send') ?>">
        <input type="hidden" name="message" id="messageInput" value="<?= esc((string) old('message'), 'attr') ?>">

        <div class="whatsapp-compose-card">
            <div class="whatsapp-compose-head">
                <h3><iconify-icon icon="mdi:whatsapp"></iconify-icon>Data Pesan</h3>
                <a class="admin-btn secondary" href="<?= site_url('dashboard/messages/templates') ?>"><iconify-icon icon="mdi:pencil-box-outline"></iconify-icon>Template</a>
            </div>

            <div class="whatsapp-compose-body">
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

                <input type="hidden" name="recipient_name" value="">

                <label>
                    <span>Judul Artikel</span>
                    <input id="articleTitleInput" value="<?= esc((string) old('judul_artikel'), 'attr') ?>" placeholder="Masukkan judul artikel">
                </label>

                <label>
                    <span>Nomor WhatsApp</span>
                    <input name="phone_number" value="<?= esc((string) old('phone_number'), 'attr') ?>" placeholder="08xxxxxxxxxx" required>
                    <small>Nomor 08... otomatis menjadi 62...</small>
                </label>

                <label>
                    <span>Nama Jurnal</span>
                    <select name="nama_jurnal" id="journalNameInput">
                        <option value="">Pilih jurnal</option>
                        <?php foreach (($journals ?? []) as $journal): ?>
                            <?php
                                $journalName = (string) ($journal['name'] ?? '');
                                $journalUrl = (string) ($journal['website_url'] ?? '');
                            ?>
                            <option
                                value="<?= esc($journalName, 'attr') ?>"
                                data-url="<?= esc($journalUrl, 'attr') ?>"
                                <?= old('nama_jurnal') === $journalName ? 'selected' : '' ?>
                            >
                                <?= esc($journalName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <div class="placeholder-box">
                    <span>Placeholder Template</span>
                    <div>
                        <button type="button" data-token="{judul_artikel}">{judul_artikel}</button>
                        <button type="button" data-token="{nama_jurnal}">{nama_jurnal}</button>
                        <button type="button" data-token="{jurnal}">{jurnal}</button>
                        <button type="button" data-token="{link_jurnal}">{link_jurnal}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="whatsapp-compose-card preview-card">
            <div class="whatsapp-compose-head">
                <h3><iconify-icon icon="mdi:message-processing-outline"></iconify-icon>Preview Pesan</h3>
            </div>

            <div class="whatsapp-preview-wrap">
                <textarea id="previewMessage" rows="18" data-editor="plain" required><?= esc((string) old('message')) ?></textarea>
            </div>

            <div class="whatsapp-preview-actions">
                <button class="admin-btn secondary" type="button" id="refreshPreviewBtn"><iconify-icon icon="mdi:refresh"></iconify-icon>Refresh Preview</button>
                <button class="admin-btn primary" type="submit"><iconify-icon icon="mdi:whatsapp"></iconify-icon>Kirim</button>
            </div>
        </div>
    </form>
</section>

<?php if (! empty($recentMessages)): ?>
<section class="admin-panel mt-panel">
    <div class="admin-table compact-history">
        <div class="table-row table-head">
            <span>Penerima</span>
            <span>Nomor</span>
            <span>Waktu</span>
            <span>Aksi</span>
        </div>
        <?php foreach ($recentMessages as $message): ?>
            <div class="table-row">
                <span><?= esc((string) ($message['recipient_name'] ?? '-')) ?></span>
                <span><?= esc((string) ($message['phone_number'] ?? '-')) ?></span>
                <span><?= esc((string) ($message['created_at'] ?? '-')) ?></span>
                <span><a class="admin-btn secondary" href="<?= esc((string) ($message['wa_url'] ?? '#'), 'attr') ?>" target="_blank" rel="noopener noreferrer">Buka</a></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const templates = <?= json_encode($templatePayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const select = document.getElementById('templateSelect');
    const messageInput = document.getElementById('messageInput');
    const previewMessage = document.getElementById('previewMessage');
    const refreshPreviewBtn = document.getElementById('refreshPreviewBtn');
    const articleTitleInput = document.getElementById('articleTitleInput');
    const journalNameInput = document.getElementById('journalNameInput');
    let activeTemplate = '';
    if (!select || !messageInput || !previewMessage) return;

    function selectedTemplateMessage() {
        const selected = templates.find(function (item) {
            return item.id === select.value;
        });

        return selected ? selected.message : previewMessage.value;
    }

    function selectedJournalUrl() {
        if (!journalNameInput || !journalNameInput.selectedOptions || !journalNameInput.selectedOptions.length) {
            return '';
        }

        return journalNameInput.selectedOptions[0].dataset.url || '';
    }

    function replaceTokens(text) {
        const articleTitle = articleTitleInput && articleTitleInput.value.trim() ? articleTitleInput.value.trim() : '';
        const journalName = journalNameInput && journalNameInput.value.trim() ? journalNameInput.value.trim() : '';
        const journalUrl = selectedJournalUrl();
        const replacements = {
            '{judul_artikel}': articleTitle,
            '{nama_jurnal}': journalName,
            '{jurnal}': journalName,
            '{link_jurnal}': journalUrl
        };

        Object.keys(replacements).forEach(function (token) {
            if (replacements[token]) {
                text = text.split(token).join(replacements[token]);
            }
        });

        return text;
    }

    function renderPreview() {
        let text = activeTemplate || selectedTemplateMessage();
        text = replaceTokens(text);
        previewMessage.value = text;
        messageInput.value = text;
    }

    select.addEventListener('change', function () {
        const selected = templates.find(function (item) {
            return item.id === select.value;
        });
        if (selected) {
            activeTemplate = selected.message;
            renderPreview();
        } else {
            activeTemplate = previewMessage.value;
            messageInput.value = previewMessage.value;
        }
    });

    [articleTitleInput, journalNameInput].forEach(function (field) {
        if (!field) return;
        field.addEventListener('input', renderPreview);
        field.addEventListener('change', renderPreview);
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

    previewMessage.addEventListener('input', function () {
        activeTemplate = previewMessage.value;
        messageInput.value = previewMessage.value;
    });

    if (refreshPreviewBtn) {
        refreshPreviewBtn.addEventListener('click', renderPreview);
    }

    if (!previewMessage.value.trim() && templates.length) {
        select.value = templates[0].id;
        activeTemplate = templates[0].message;
    } else {
        activeTemplate = previewMessage.value;
    }
    renderPreview();
});
</script>
<?= $this->endSection() ?>
