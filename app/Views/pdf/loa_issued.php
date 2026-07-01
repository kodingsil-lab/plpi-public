<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page { size: A4; margin: 18mm 16mm; }
    body {
      font-family: "Times New Roman", "Times", "Liberation Serif", "DejaVu Serif", serif;
      font-size: 12pt;
      color: #111;
      line-height: 1.45;
    }
    .small { font-size:9pt; color:#444; }
    .tiny { font-size:8.7pt; color:#444; }
    .hr { border-top:2px solid #000; margin: 0 0 10px; }
    .center { text-align: center; }
    .justify { text-align:justify; line-height:1.45; }
    .nowrap { white-space: nowrap; }
    .avoid { page-break-inside: avoid; }
    .meta { width:100%; border-collapse:collapse; margin-top: 6px; }
    .meta td { padding:2px 0; vertical-align:top; }
    .meta .k { width:34mm; }
    .meta .s { width:4mm; }
    .value { overflow-wrap: break-word; word-break: normal; text-align: justify; }
    .url { word-break: break-word; overflow-wrap: anywhere; }
    p { margin: 7px 0; }
    .pdf-header { width: 100%; border-collapse: collapse; margin: 0; }
    .pdf-header td { vertical-align: middle; text-align: center; }
    .pdf-header-logo img { object-fit: contain; width: auto; max-width: 100%; }
    .hdr-title { font-size: 18pt; font-weight: 700; line-height: 1.2; margin: 0; text-transform: uppercase; }
    .hdr-sub { font-size: 16pt; font-weight: 400; line-height: 1.2; margin: 0; }
    .hdr-meta { font-size: 9pt; line-height: 1.25; margin: 0; }
    .hdr-meta p { margin: 0; }
    .hdr-meta .mono { letter-spacing: 0.2px; }
    .footer-meta { position: fixed; left: 0; right: 0; bottom: 0; padding-top: 8px; border-top: 1px solid #9ca3af; font-size: 10pt; line-height: 1.18; color: #111; padding-left: 16mm; padding-right: 16mm; }
    .footer-meta p { margin: 0 0 1px 0; }
    .loa-title-block { margin-bottom: 16px; line-height: 1.2; }
    .loa-title-main { margin-bottom: 7px; }
    .section-gap-sm { margin-top: 8px; }
  </style>
</head>
<body>
<?php
  // Fungsi untuk membersihkan label role (Ketua:, Anggota 1:, dll)
  $cleanAuthorName = static function ($raw): string {
    $raw = trim((string) $raw);
    // Hapus label "Ketua:", "Anggota:", "Anggota 1:", dll
    $raw = preg_replace('/^(Ketua|Anggota(?:\s*\d*)?)\s*[:\-]\s*/iu', '', $raw);
    return trim($raw);
  };

  // Fungsi untuk membersihkan label afiliasi
  $cleanAffiliationText = static function ($raw): string {
    $raw = trim((string) $raw);
    // Hapus label "Afiliasi:", "UPT:", "Unit:", dll di awal
    $raw = preg_replace('/^(Afiliasi|Departemen|Unit|UPT|Institusi)\s*[:\-]\s*/iu', '', $raw);
    return trim($raw);
  };

  // Proses data penulis dari array JSON
  $authorNames = [];
  if (!empty($authors) && is_array($authors)) {
    foreach ($authors as $author) {
      $name = '';
      if (is_array($author)) {
        $name = isset($author['name']) ? $cleanAuthorName($author['name']) : '';
      } else {
        $name = $cleanAuthorName($author);
      }
      if ($name !== '') {
        $authorNames[] = $name;
      }
    }
  }
  
  // Format penulis dengan benar
  $authorsText = '-';
  $count = count($authorNames);
  if ($count === 1) {
    $authorsText = $authorNames[0];
  } elseif ($count === 2) {
    $authorsText = $authorNames[0] . ' dan ' . $authorNames[1];
  } elseif ($count > 2) {
    $last = array_pop($authorNames);
    $authorsText = implode(', ', $authorNames) . ', dan ' . $last;
  }
  
  // Proses afiliasi: hanya ambil dari penulis pertama (Ketua)
  $affText = '-';
  if (!empty($affiliations) && is_array($affiliations)) {
    // Ambil afiliasi pertama (dari Ketua)
    $firstAff = $affiliations[0] ?? null;
    if (!empty($firstAff)) {
      if (is_string($firstAff)) {
        $affText = $cleanAffiliationText($firstAff);
      } elseif (is_array($firstAff) && isset($firstAff['affiliation'])) {
        $affText = $cleanAffiliationText($firstAff['affiliation']);
      } elseif (is_array($firstAff) && isset($firstAff['name'])) {
        $affText = $cleanAffiliationText($firstAff['name']);
      }
    }
  }
  
  // Proses edisi
  $editionParts = [];
  if (!empty($letter['volume'])) {
    $editionParts[] = 'Volume ' . $letter['volume'];
  }
  if (!empty($letter['issue_number'])) {
    $editionParts[] = 'Nomor ' . $letter['issue_number'];
  }
  $editionText = !empty($editionParts) ? implode(', ', $editionParts) : 'edisi yang ditetapkan redaksi';
  // Prioritas: published_year (input user) > published_at (tanggal sistem)
  $editionYear = null;
  if (!empty($letter['published_year'])) {
    $editionYear = (string) $letter['published_year'];
  } elseif (!empty($letter['published_at'])) {
    $editionYear = date('Y', strtotime((string) $letter['published_at']));
  }
  if ($editionYear !== null && $editionYear !== '') {
    $editionText = $editionText === 'edisi yang ditetapkan redaksi'
      ? 'Tahun ' . $editionYear
      : $editionText . ' Tahun ' . $editionYear;
  }
  
  // Pengaturan layout
  $publisherLogoHeightCm = 2.55;
  $journalLogoHeightCm = 2.95;
  $headerTextPt = (int) ($journal['pdf_header_title_pt'] ?? 18);
  $headerTextPt = max(12, min(24, $headerTextPt));
  $headerTitlePt = $headerTextPt;
  $headerSubPt = max(10, $headerTextPt - 2);
  $headerMetaPt = 9;
  $publisherPhone = trim((string) ($publisher['phone'] ?? '-'));
  $publisherEmail = trim((string) ($publisher['email'] ?? '-'));
  $eissnText = trim((string) ($journal['e_issn'] ?? ($journal['issn'] ?? '-')));
  $headerMetaText = 'HP: ' . ($publisherPhone !== '' ? $publisherPhone : '-') . ' ; E-Mail: ' . ($publisherEmail !== '' ? $publisherEmail : '-') . ' ; E-ISSN: ' . ($eissnText !== '' ? $eissnText : '-');
  
  $publisherNameRaw = trim((string) ($publisher['name'] ?? 'PUSAT LAYANAN PUBLIKASI ILMIAH'));
  $publisherNameLines = [$publisherNameRaw];
  
  $loaTitlePt = 18;
  $loaNumberPt = 12;
  $titleMarginTopPx = 34;
  $signatureMarginTopPx = (int) ($journal['pdf_signature_margin_top_px'] ?? 28);
  $overlayWidth = 300;
  $overlayHeight = 116;
  $sigLeft = ($journal['pdf_sig_left_px'] ?? '') !== '' ? (int) $journal['pdf_sig_left_px'] : 28;
  $sigTop = ($journal['pdf_sig_top_px'] ?? '') !== '' ? (int) $journal['pdf_sig_top_px'] : 14;
  $sigHeight = ($journal['pdf_sig_height_px'] ?? '') !== '' ? (int) $journal['pdf_sig_height_px'] : 78;
  $sigHeight = max(45, min(180, $sigHeight));
  $sigScalePercent = ($journal['pdf_sig_scale_percent'] ?? '') !== '' ? (int) $journal['pdf_sig_scale_percent'] : 100;
  $sigScalePercent = max(50, min(250, $sigScalePercent));
  $sigScale = $sigScalePercent / 100;
  $stampMaxHeightPx = max(36, min(140, (int) round($sigHeight * 0.9)));
  $stampMaxWidthPx = max(48, min(140, (int) round($stampMaxHeightPx * 1.05)));
  $signatureMaxHeightPx = (int) round($sigHeight * $sigScale);
  $signatureMaxWidthPx = max(90, min(520, (int) round($signatureMaxHeightPx * 2.35)));
  $signatureWrapWidthPx = $stampMaxWidthPx + $signatureMaxWidthPx + 24;
  
  $journalName = trim((string) ($journal['name'] ?? '-'));
  $publisherAddress = trim((string) ($publisher['address'] ?? '-'));
  $city = trim((string) ($journal['city'] ?? 'Kupang'));
  $editorName = trim((string) ($journal['default_signer_name'] ?? '-'));
  $editorTitle = trim((string) ($journal['default_signer_title'] ?? 'Pimpinan Redaksi'));
  $editorNidn = trim((string) ($journal['editor_nidn'] ?? ''));
?>

  <!-- HEADER: logo kiri + teks tengah + logo kanan -->
  <table class="pdf-header avoid">
    <tr>
      <td width="15%">
        <div class="pdf-header-logo">
          <?php if (!empty($publisherLogoBase64)): ?>
            <img src="<?= esc((string) $publisherLogoBase64) ?>" style="height:<?= $publisherLogoHeightCm ?>cm; width:auto;">
          <?php endif; ?>
        </div>
      </td>
      <td width="70%">
        <div class="hdr-title" style="font-size: <?= $headerTitlePt ?>pt;">
          <?php foreach ($publisherNameLines as $line): ?>
            <div><?= esc($line) ?></div>
          <?php endforeach; ?>
        </div>
        <div class="hdr-sub" style="font-size: <?= $headerSubPt ?>pt;">
          <?= esc($journalName) ?>
        </div>
        <div class="hdr-meta" style="font-size: <?= $headerMetaPt ?>pt;">
          <p><?= esc($headerMetaText) ?></p>
        </div>
      </td>
      <td width="15%">
        <div class="pdf-header-logo">
          <?php if (!empty($logoBase64)): ?>
            <img src="<?= esc((string) $logoBase64) ?>" style="height:<?= $journalLogoHeightCm ?>cm; width:auto;">
          <?php endif; ?>
        </div>
      </td>
    </tr>
  </table>

  <div class="hr"></div>

  <!-- TITLE -->
  <div class="center avoid loa-title-block" style="margin-top:<?= $titleMarginTopPx ?>px;">
    <div class="loa-title-main" style="font-weight:700; text-decoration: underline; font-size:<?= $loaTitlePt ?>pt;">Letter of Acceptance (LoA)</div>
    <div style="font-weight:700; font-size:<?= $loaNumberPt ?>pt;">No: <?= esc((string) ($loaNumber ?? '-')) ?></div>
  </div>

  <div class="justify" style="margin-top:0;">
    Dengan ini, redaksi <b><?= esc($journalName) ?></b> memberitahukan bahwa naskah Anda dengan identitas berikut:
  </div>

  <!-- META -->
  <table class="meta avoid">
    <tr>
      <td class="k">Judul</td>
      <td class="s">:</td>
      <td class="value"><?= esc((string) ($letter['title'] ?? '-')) ?></td>
    </tr>
    <tr>
      <td class="k">Penulis</td>
      <td class="s">:</td>
      <td class="value"><?= esc($authorsText) ?></td>
    </tr>
    <tr>
      <td class="k">Afiliasi</td>
      <td class="s">:</td>
      <td class="value"><?= esc($affText) ?></td>
    </tr>
    <tr>
      <td class="k">Email</td>
      <td class="s">:</td>
      <td class="value"><?= esc((string) ($letter['corresponding_email'] ?? '-')) ?></td>
    </tr>
  </table>

  <div class="justify section-gap-sm">
    Telah melalui proses seleksi dan penelaahan sesuai standar dan kebijakan editorial yang berlaku.
    Berdasarkan hasil evaluasi tersebut, naskah dinyatakan <b>diterima</b> dan layak untuk dipublikasikan pada edisi
    <b><?= esc($editionText) ?></b>.
  </div>

  <div class="justify section-gap-sm">
    Sehubungan dengan prinsip etika publikasi ilmiah dan untuk menghindari duplikasi terbitan, kami mengharapkan agar naskah/artikel tersebut tidak dikirimkan maupun dipublikasikan pada jurnal atau penerbit lain.
  </div>

  <div class="justify section-gap-sm">
    Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya. Atas kepercayaan, partisipasi, dan kerja sama yang baik, kami sampaikan terima kasih.
  </div>

  <!-- SIGNATURE SECTION - Proven Pattern -->
  <div class="avoid" style="margin-top:<?= $signatureMarginTopPx ?>px; padding-top:4px;">
    <table width="100%" style="border-collapse:collapse; margin:0; padding:0;">
      <tr>
        <!-- KOLOM KIRI: QR + KETERANGAN -->
        <td width="46%" valign="top" style="padding:0;">
          <div style="width:132px; text-align:center;">
            <?php if (!empty($qrcodeBase64)): ?>
              <img src="<?= esc((string) $qrcodeBase64) ?>" width="120px" style="border:1px solid #999; padding:4px; background:#fff;">
            <?php endif; ?>
            <p style="font-size:7pt; line-height:1.25; margin:4px 0 0 0;">Validasi LoA dapat dilakukan dengan memindai QR Code di atas.</p>
          </div>
        </td>

        <!-- KOLOM KANAN: TANDA TANGAN & NAMA -->
        <td width="54%" valign="top" align="left" style="padding:0; padding-left:<?= $sigLeft ?>px; font-family:'Times New Roman', serif;">
          <p style="font-size:11pt; margin:0; white-space:nowrap;">
            <?= esc($city) ?>, <?= esc((string) ($issuedDate ?? '')) ?>
          </p>
          <p style="font-size:11pt; margin:0; white-space:nowrap;">
            <?= esc($editorTitle) ?>
          </p>
          <div style="height:<?= $sigTop ?>px;"></div>
          
          <!-- CAP & TANDA TANGAN - Ukuran terpisah -->
          <div style="width:<?= $signatureWrapWidthPx ?>px; height:auto; margin:0; white-space:nowrap;">
            <?php if (!empty($stampBase64)): ?>
              <img
                src="<?= esc((string) $stampBase64) ?>"
                style="max-width:<?= $stampMaxWidthPx ?>px; max-height:<?= $stampMaxHeightPx ?>px; width:auto; height:auto; display:inline-block; vertical-align:bottom; object-fit:contain; margin-right:8px;"
              >
            <?php endif; ?>
            <?php if (!empty($sigBase64)): ?>
              <img
                src="<?= esc((string) $sigBase64) ?>"
                style="max-width:<?= $signatureMaxWidthPx ?>px; max-height:<?= $signatureMaxHeightPx ?>px; width:auto; height:auto; display:inline-block; vertical-align:bottom; object-fit:contain;"
              >
            <?php endif; ?>
          </div>
          
          <!-- NAMA -->
          <p style="font-size:11pt; margin:-2px 0 0 0; font-weight:700; white-space:nowrap;">
            <?= esc($editorName) ?>
          </p>
          <?php if (!empty($editorNidn)): ?>
            <p style="font-size:9pt; margin:0; white-space:nowrap;">
              NIDN. <?= esc($editorNidn) ?>
            </p>
          <?php endif; ?>
        </td>
      </tr>
    </table>
  </div>

  <!-- FOOTER FIXED - Garis dan Kontak Redaksi -->
  <div style="position: fixed; left: 0; right: 0; bottom: 0; width: 100%; text-align: left; padding-left: 0; padding-right: 16mm; margin: 0;">
    <!-- SEPARATOR -->
    <hr style="margin:4px 0; border:none; border-top:1px solid #9ca3af; padding:0;">
    
    <!-- FOOTER INFO PENERBIT -->
    <div style="text-align:left;">
      <p style="margin:2px 0; font-size:10pt; font-weight:700;">
        <b>Kontak Redaksi</b>
      </p>
      <p style="margin:0; font-size:9pt;">
        Email: <?= esc($publisherEmail) ?>
      </p>
      <p style="margin:0; font-size:9pt;">
        Whatsapp: <?= esc($publisherPhone) ?>
      </p>
      <p style="margin:0; font-size:9pt;">
        Alamat: <?= esc($publisherAddress) ?>
      </p>
    </div>
  </div>
</body>
</html>
