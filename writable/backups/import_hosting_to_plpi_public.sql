USE plpi_public;

SET FOREIGN_KEY_CHECKS = 0;

CREATE TEMPORARY TABLE tmp_current_app_mail AS
SELECT id, smtp_host, smtp_port, smtp_user, smtp_pass, smtp_crypto, mail_from_email, mail_from_name
FROM plpi_public.app_settings;

CREATE TEMPORARY TABLE tmp_current_email_templates AS
SELECT name, code, type, subject, message, is_active, created_at, updated_at
FROM plpi_public.whatsapp_templates
WHERE type = 'email';

DELETE FROM plpi_public.whatsapp_messages;
DELETE FROM plpi_public.loa_notifications;
DELETE FROM plpi_public.loa_letters;
DELETE FROM plpi_public.loa_requests;
DELETE FROM plpi_public.editor_reviewer_applications;
DELETE FROM plpi_public.invoice_jurnal;
DELETE FROM plpi_public.whatsapp_templates;
DELETE FROM plpi_public.journals;
DELETE FROM plpi_public.publishers;
DELETE FROM plpi_public.users;
DELETE FROM plpi_public.app_settings;

INSERT INTO plpi_public.app_settings (
    id, header_logo_path, login_logo_path, public_logo_path, favicon_path,
    app_timezone, created_at, updated_at, smtp_host, smtp_port, smtp_user,
    smtp_pass, smtp_crypto, mail_from_email, mail_from_name
)
SELECT
    s.id, s.header_logo_path, s.login_logo_path, s.public_logo_path, s.favicon_path,
    s.app_timezone, s.created_at, s.updated_at,
    m.smtp_host, m.smtp_port, m.smtp_user, m.smtp_pass, m.smtp_crypto, m.mail_from_email, m.mail_from_name
FROM plpi_hosting_backup.app_settings s
LEFT JOIN tmp_current_app_mail m ON m.id = s.id;

UPDATE plpi_public.app_settings
SET
    header_logo_path = 'uploads/app-settings/app_logo-1782915329_ffd1b154901ac53e5309.png',
    login_logo_path = 'uploads/app-settings/app_logo-1782915329_ffd1b154901ac53e5309.png',
    public_logo_path = 'uploads/app-settings/app_logo-1782915329_ffd1b154901ac53e5309.png',
    favicon_path = 'uploads/app-settings/favicon-1782915329_dbe7ceac17294e8ab2d0.png',
    updated_at = NOW()
WHERE id = 1;

INSERT INTO plpi_public.publishers (
    id, code, name, email, phone, address, logo_path, created_at, updated_at
)
SELECT id, code, name, email, phone, address, logo_path, created_at, updated_at
FROM plpi_hosting_backup.publishers;

INSERT INTO plpi_public.journals (
    id, publisher_id, name, code, slug, issn, e_issn, p_issn, website_url,
    commitment_statement_url, recruitment_intro, default_stamp_path, logo_path,
    default_signer_name, default_signer_title, default_signature_path,
    pdf_sig_left_px, pdf_sig_top_px, pdf_sig_height_px, pdf_sig_scale_percent,
    created_at, updated_at
)
SELECT
    id, publisher_id, name, code, slug, issn, e_issn, p_issn, website_url,
    commitment_statement_url, recruitment_intro, default_stamp_path, logo_path,
    default_signer_name, default_signer_title, default_signature_path,
    pdf_sig_left_px, pdf_sig_top_px, pdf_sig_height_px, pdf_sig_scale_percent,
    created_at, updated_at
FROM plpi_hosting_backup.journals;

INSERT INTO plpi_public.loa_requests (
    id, journal_id, request_code, article_url, article_id_external, title,
    authors_json, corresponding_email, whatsapp_number, affiliations_json,
    volume, issue_number, published_year, status, notes_admin, rejection_reason,
    approved_at, created_at, updated_at
)
SELECT
    id, journal_id, request_code, article_url, article_id_external, title,
    authors_json, corresponding_email, whatsapp_number, affiliations_json,
    volume, issue_number, published_year, status, notes_admin, rejection_reason,
    approved_at, created_at, updated_at
FROM plpi_hosting_backup.loa_requests;

INSERT INTO plpi_public.loa_letters (
    id, journal_id, loa_request_id, loa_number, article_url, article_id_external,
    title, authors_json, corresponding_email, affiliations_json, volume,
    issue_number, published_year, status, verification_hash, public_token,
    pdf_path, published_at, revoked_at, revoked_reason, created_at, updated_at
)
SELECT
    id, journal_id, loa_request_id, loa_number, article_url, article_id_external,
    title, authors_json, corresponding_email, affiliations_json, volume,
    issue_number, published_year, status, verification_hash, public_token,
    pdf_path, published_at, revoked_at, revoked_reason, created_at, updated_at
FROM plpi_hosting_backup.loa_letters;

INSERT INTO plpi_public.loa_notifications (
    id, loa_letter_id, status, sent_to_email, sent_at, created_at, updated_at
)
SELECT id, loa_letter_id, status, sent_to_email, sent_at, created_at, updated_at
FROM plpi_hosting_backup.loa_notifications;

INSERT INTO plpi_public.invoice_jurnal (
    id, nomor_invoice, tanggal_invoice, jatuh_tempo, judul_artikel,
    nama_penulis, institusi_penulis, nama_jurnal, jumlah_tagihan,
    status_pembayaran, keterangan, created_at, updated_at, deleted_at
)
SELECT
    id, nomor_invoice, tanggal_invoice, jatuh_tempo, judul_artikel,
    nama_penulis, institusi_penulis, nama_jurnal, jumlah_tagihan,
    status_pembayaran, keterangan, created_at, updated_at, deleted_at
FROM plpi_hosting_backup.invoice_jurnal;

INSERT INTO plpi_public.users (
    id, username, name, email, password, role, is_active, created_at, updated_at
)
SELECT id, username, name, email, password, role, is_active, created_at, updated_at
FROM plpi_hosting_backup.users;

INSERT INTO plpi_public.editor_reviewer_applications (
    id, journal_id, application_code, full_name, institution, role_requested,
    email, phone, google_scholar_id, sinta_id, scopus_id, orcid_id, expertise,
    status, notification_sent_at, notification_error, created_at, updated_at
)
SELECT
    id, journal_id, application_code, full_name, institution, role_requested,
    email, phone, google_scholar_id, sinta_id, scopus_id, orcid_id, expertise,
    status, notification_sent_at, notification_error, created_at, updated_at
FROM plpi_hosting_backup.editor_reviewer_applications;

INSERT INTO plpi_public.whatsapp_templates (
    id, name, code, type, subject, message, is_active, created_at, updated_at
)
SELECT
    id, name, slug, 'whatsapp', NULL, body, is_active, created_at, updated_at
FROM plpi_hosting_backup.whatsapp_message_templates;

INSERT INTO plpi_public.whatsapp_templates (
    name, code, type, subject, message, is_active, created_at, updated_at
)
SELECT name, code, type, subject, message, is_active, created_at, updated_at
FROM tmp_current_email_templates;

INSERT INTO plpi_public.whatsapp_templates (
    name, code, type, subject, message, is_active, created_at, updated_at
)
SELECT
    'Email Notifikasi LoA Terbit',
    'email_loa_terbit',
    'email',
    'Notifikasi Letter of Acceptance (LoA) - {judul_artikel}',
    'Yth. Bapak/Ibu {nama_penerima}\n\nLetter of Acceptance (LoA) untuk artikel berikut telah diterbitkan:\n\nJudul: {judul_artikel}\nJurnal: {nama_jurnal}\n\nHormat kami,\nTim Editor\n{nama_jurnal}',
    1,
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM plpi_public.whatsapp_templates WHERE code = 'email_loa_terbit'
);

SET FOREIGN_KEY_CHECKS = 1;
