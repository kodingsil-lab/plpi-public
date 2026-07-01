<!-- CSS ditanamkan langsung di loa_issued.php untuk dompdf -->
<style>
    @page {
        size: A4 portrait;
        margin: 20mm 15mm 20mm 15mm;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        font-family: "Times New Roman", serif;
        font-size: 11pt;
        color: #000;
        line-height: 1.45;
    }

    body {
        background: #fff;
    }

    .footer-section {
        width: 100%;
        margin: 0 auto;
        page-break-inside: avoid;
    }

    .penutup-paragraf {
        text-align: justify;
        margin: 0 0 14pt 0;
    }

    .footer-main-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        page-break-inside: avoid;
        margin-bottom: 12pt;
    }

    .footer-main-table td {
        vertical-align: top;
        padding: 0;
    }

    .verifikasi-left {
        width: 45%;
        padding-right: 12pt;
    }

    .ttd-right {
        width: 55%;
        padding-left: 12pt;
        text-align: left;
    }

    .verifikasi-left h2,
    .kontak-section h2 {
        margin: 0 0 6pt 0;
        font-size: 12pt;
        font-weight: 700;
    }

    .verifikasi-left .tiny {
        margin: 0 0 10pt 0;
        font-size: 10pt;
        line-height: 1.4;
    }

    .qr-image {
        display: block;
        width: 120px;
        max-width: 100%;
        height: auto;
        border: 1px solid #ccc;
    }

    .nomor-dokumen {
        margin: 8pt 0 0 0;
        font-size: 10.5pt;
        line-height: 1.3;
        word-break: break-word;
    }

    .tanggal {
        margin: 0 0 6pt 0;
    }

    .jabatan {
        margin: 0 0 16pt 0;
        font-weight: 700;
    }

    .ttd-wrapper {
        width: 100%;
        max-width: 180px;
        margin-bottom: 10pt;
    }

    .ttd-image {
        display: block;
        width: 100%;
        max-width: 180px;
        height: auto;
    }

    .stempel-image {
        display: block;
        width: 70px;
        max-width: 70px;
        height: auto;
        margin-top: -28px;
        margin-left: 70px;
        opacity: 0.94;
    }

    .nama-penandatangan {
        margin: 0;
        font-weight: 700;
    }

    .garis-pemisah {
        width: 100%;
        border-top: 1px solid #444;
        margin: 14pt 0 12pt 0;
        page-break-inside: avoid;
    }

    .kontak-section {
        width: 100%;
        page-break-inside: avoid;
    }

    .kontak-list {
        margin: 6pt 0 0 0;
        padding: 0;
        list-style: none;
    }

    .kontak-list li {
        margin-bottom: 4pt;
        line-height: 1.4;
    }

    .pdf-header,
    .meta {
        border-collapse: collapse;
        table-layout: fixed;
        width: 100%;
    }

    .pdf-header td {
        vertical-align: middle;
        text-align: center;
    }

    .pdf-header-logo img {
        height: 2.55cm;
        width: auto;
    }

    .pdf-header-logo-right img {
        height: 2.95cm;
        width: auto;
    }

    .hdr-title {
        font-size: 18pt;
        font-weight: 700;
        line-height: 1.2;
        margin: 0;
        text-transform: uppercase;
    }

    .hdr-sub {
        font-size: 16pt;
        font-weight: 400;
        line-height: 1.2;
        margin: 0;
    }

    .hdr-meta {
        font-size: 9pt;
        line-height: 1.25;
        margin: 0;
    }

    .hdr-meta p {
        margin: 0;
    }

    .pdf-loa-title-block {
        margin-top: 18px;
        margin-bottom: 18px;
        line-height: 1.2;
    }

    .pdf-loa-title {
        font-weight: 700;
        text-decoration: underline;
        font-size: 18pt;
        margin-bottom: 6px;
    }

    .pdf-loa-number {
        font-weight: 700;
        font-size: 12pt;
    }

    .pdf-section {
        margin-top: 28px;
    }

    .pdf-section-title {
        font-weight: 700;
        margin-bottom: 6px;
    }

    .pdf-qrcode-wrap {
        margin-top: 6px;
        text-align: left;
    }

    .pdf-number {
        margin-top: 6px;
        font-weight: 700;
    }

    .pdf-right-cell {
        text-align: left;
        padding-left: 0;
    }

    .pdf-footer-label {
        font-weight: 700;
        margin-top: 4px;
    }

    .pdf-spacing {
        height: 18px;
    }

    .pdf-signature-block {
        display: inline-block;
        width: 260px;
        height: auto;
        margin: 0;
    }

    .pdf-signature-image {
        max-width: 100%;
        width: auto;
        height: auto;
        display: block;
        margin: 0;
    }

    .pdf-signer-name {
        font-weight: 700;
        margin-top: 6px;
    }

    .pdf-paragraph {
        margin-top: 6px;
    }

    /* LoA Signature Section Styles */
    .loa-signature-cell {
        width: 65%;
        padding-left: 30px;
        text-align: right;
        vertical-align: top;
        font-family: "Times New Roman", serif;
    }

    .loa-signature-date {
        font-size: 11pt;
        font-weight: 400;
        margin-bottom: 10px;
        text-align: right;
    }

    .loa-signature-title {
        font-size: 11pt;
        font-weight: 400;
        margin-bottom: 18px;
        text-align: right;
    }

    .loa-signature-box {
        width: 260px;
        height: 110px;
        margin: 0 0 14px auto;
        text-align: center;
    }

    .loa-signature-box img {
        max-height: 110px;
        width: auto;
        object-fit: contain;
    }

    .loa-signer-name {
        font-size: 11pt;
        font-weight: 700;
        line-height: 1.35;
        text-align: right;
    }

    .loa-signer-meta {
        font-size: 9pt;
        font-weight: 400;
        margin-top: 3px;
        text-align: right;
    }

    /* Footer Kontak Redaksi - Simple Pattern from loa-center */
    .footer-meta {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        padding-top: 8px;
        padding-left: 16mm;
        padding-right: 16mm;
        padding-bottom: 8px;
        margin: 0;
        border-top: 1px solid #9ca3af;
        background-color: #fff;
        font-size: 10pt;
        line-height: 1.25;
        color: #111;
        text-align: left;
        page-break-inside: avoid;
    }

    .footer-meta p {
        margin: 0 0 1px 0;
        padding: 0;
    }

    .footer-meta p:first-child {
        margin-bottom: 4px;
    }
</style>
