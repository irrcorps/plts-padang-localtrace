<?php $pageTitle = 'Beranda'; require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/navbar.php'; ?>

<main>
    <!-- HERO -->
    <section class="hero-section">
        <div class="container py-5">
            <div class="row align-items-center g-5 py-4">
                <div class="col-lg-6">
                    <span class="badge-pill mb-3"><i class="bi bi-mortarboard-fill me-1"></i> Penelitian PDP 2026</span>
                    <h1 class="hero-title mb-3">
                        Traceability &amp; Sertifikasi Digital Produk Lokal Padang,
                        <span class="text-gradient">Berbasis Blockchain</span>
                    </h1>
                    <p class="hero-lead mb-4">
                        Padang LocalTrace System (PLTS) adalah prototipe platform yang mencatat
                        perjalanan produk ritel lokal secara transparan &mdash; dari produsen hingga
                        konsumen &mdash; serta menerbitkan sertifikat digital melalui simulasi
                        <em>smart contract</em>, untuk memperkuat hilirisasi UMKM Kota Padang.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="<?= BASE_URL ?>/register.php" class="btn btn-plts-primary btn-lg px-4">
                            <i class="bi bi-person-plus me-2"></i>Register
                        </a>
                        <a href="<?= BASE_URL ?>/login.php" class="btn btn-plts-outline-light btn-lg px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                        <a href="<?= BASE_URL ?>/verify.php" class="btn btn-plts-ghost btn-lg px-4">
                            <i class="bi bi-qr-code-scan me-2"></i>Verify Product
                        </a>
                    </div>
                    <div class="row mt-5 g-3 hero-stats">
                        <div class="col-4">
                            <div class="stat-box">
                                <div class="stat-number">TKT 3</div>
                                <div class="stat-label">Tingkat Kesiapan Teknologi</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-box">
                                <div class="stat-number">4</div>
                                <div class="stat-label">Peran Rantai Pasok</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-box">
                                <div class="stat-number">100%</div>
                                <div class="stat-label">Riset &amp; Prototipe</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-visual">
                        <div class="chain-card chain-card-1">
                            <i class="bi bi-box-seam"></i>
                            <div>
                                <div class="chain-card-title">Producer</div>
                                <div class="chain-card-sub">Rendang Kemasan &bull; Block #001</div>
                            </div>
                        </div>
                        <div class="chain-card chain-card-2">
                            <i class="bi bi-truck"></i>
                            <div>
                                <div class="chain-card-title">Distributor</div>
                                <div class="chain-card-sub">Hash: 8f3a...9c2e</div>
                            </div>
                        </div>
                        <div class="chain-card chain-card-3">
                            <i class="bi bi-shop"></i>
                            <div>
                                <div class="chain-card-title">Retailer</div>
                                <div class="chain-card-sub">Verified &bull; Block #003</div>
                            </div>
                        </div>
                        <div class="chain-card chain-card-4">
                            <i class="bi bi-patch-check-fill"></i>
                            <div>
                                <div class="chain-card-title">Consumer</div>
                                <div class="chain-card-sub">Certificate Valid ✓</div>
                            </div>
                        </div>
                        <svg class="chain-lines" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                            <path d="M80,70 L320,140" stroke="url(#g1)" stroke-width="2" stroke-dasharray="6 6" fill="none"/>
                            <path d="M320,140 L80,230" stroke="url(#g1)" stroke-width="2" stroke-dasharray="6 6" fill="none"/>
                            <path d="M80,230 L320,320" stroke="url(#g1)" stroke-width="2" stroke-dasharray="6 6" fill="none"/>
                            <defs>
                                <linearGradient id="g1" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#4fd1c5"/>
                                    <stop offset="100%" stop-color="#f6ad55"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MASALAH -->
    <section class="section-problem py-5">
        <div class="container py-4">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <span class="section-eyebrow">Latar Belakang Masalah</span>
                    <h2 class="section-title mb-3">Minimnya Transparansi Rantai Pasok Produk Lokal</h2>
                    <p class="text-muted-plts">
                        Produk ritel lokal Kota Padang &mdash; seperti rendang kemasan, keripik balado,
                        dan kopi Minang &mdash; kerap sulit ditelusuri asal-usul, proses distribusi, dan
                        keasliannya. Konsumen tidak memiliki cara mudah untuk memverifikasi klaim
                        "produk asli daerah", sementara produsen kesulitan membuktikan kualitas dan
                        legalitas produknya secara digital.
                    </p>
                </div>
                <div class="col-lg-7">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="problem-card">
                                <i class="bi bi-eye-slash"></i>
                                <h6>Traceability Rendah</h6>
                                <p>Perjalanan produk dari produsen ke konsumen tidak tercatat secara sistematis dan dapat diaudit.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="problem-card">
                                <i class="bi bi-file-earmark-x"></i>
                                <h6>Sertifikasi Manual</h6>
                                <p>Proses sertifikasi produk lokal masih berbasis dokumen fisik, rentan dipalsukan dan lambat.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="problem-card">
                                <i class="bi bi-shield-exclamation"></i>
                                <h6>Kepercayaan Konsumen</h6>
                                <p>Konsumen tidak memiliki alat verifikasi independen atas keaslian produk lokal yang dibeli.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="problem-card">
                                <i class="bi bi-graph-down"></i>
                                <h6>Hambatan Hilirisasi</h6>
                                <p>UMKM lokal kesulitan naik kelas ke pasar lebih luas tanpa bukti mutu yang kredibel.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KONSEP BLOCKCHAIN -->
    <section class="section-concept py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="section-eyebrow">Konsep Solusi</span>
                <h2 class="section-title">Blockchain-Inspired Traceability &amp; Smart Contract</h2>
                <p class="text-muted-plts mx-auto" style="max-width:700px">
                    PLTS mengadopsi prinsip inti blockchain &mdash; pencatatan berantai yang tidak
                    mudah diubah (hash-linked ledger) &mdash; dan logika <em>smart contract</em> sederhana
                    untuk otomatisasi penerbitan sertifikat, sebagai model konseptual dalam skala prototipe penelitian.
                </p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="concept-card">
                        <div class="concept-icon"><i class="bi bi-link-45deg"></i></div>
                        <h5>Hash-Linked Ledger</h5>
                        <p>Setiap aktivitas rantai pasok dicatat sebagai "block" dengan data hash unik yang saling terhubung, mensimulasikan sifat immutable blockchain.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="concept-card">
                        <div class="concept-icon"><i class="bi bi-filetype-json"></i></div>
                        <h5>Smart Contract Sederhana</h5>
                        <p>Logika IF-THEN otomatis memvalidasi kelengkapan produk dan dokumen sebelum sertifikat digital diterbitkan oleh admin.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="concept-card">
                        <div class="concept-icon"><i class="bi bi-qr-code"></i></div>
                        <h5>Verifikasi Publik via QR</h5>
                        <p>Konsumen memindai QR code pada kemasan untuk melihat riwayat lengkap dan status sertifikasi produk secara real-time.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ALUR SISTEM -->
    <section class="section-flow py-5" id="alur">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="section-eyebrow">Alur Sistem</span>
                <h2 class="section-title">Producer &rarr; Distributor &rarr; Retail &rarr; Consumer</h2>
            </div>
            <div class="flow-track">
                <div class="flow-step">
                    <div class="flow-icon"><i class="bi bi-box-seam-fill"></i></div>
                    <h6>1. Producer</h6>
                    <p>UMKM mendaftarkan produk, melengkapi data produksi, dan mengajukan sertifikasi.</p>
                </div>
                <div class="flow-arrow"><i class="bi bi-arrow-right"></i></div>
                <div class="flow-step">
                    <div class="flow-icon"><i class="bi bi-truck-front-fill"></i></div>
                    <h6>2. Distributor</h6>
                    <p>Mencatat aktivitas distribusi produk beserta lokasi dan waktu pengiriman.</p>
                </div>
                <div class="flow-arrow"><i class="bi bi-arrow-right"></i></div>
                <div class="flow-step">
                    <div class="flow-icon"><i class="bi bi-shop-window"></i></div>
                    <h6>3. Retail</h6>
                    <p>Retailer mencatat penerimaan produk sebelum dijual ke konsumen akhir.</p>
                </div>
                <div class="flow-arrow"><i class="bi bi-arrow-right"></i></div>
                <div class="flow-step">
                    <div class="flow-icon"><i class="bi bi-patch-check-fill"></i></div>
                    <h6>4. Consumer</h6>
                    <p>Konsumen memindai QR code untuk memverifikasi keaslian dan riwayat produk.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- PENELITIAN -->
    <section class="section-research py-5" id="penelitian">
        <div class="container py-4">
            <div class="row g-5 align-items-center">
                <div class="col-lg-7">
                    <span class="section-eyebrow">Informasi Penelitian</span>
                    <h2 class="section-title mb-3">Penelitian Dosen Pemula (PDP) 2026</h2>
                    <p class="text-muted-plts">
                        Sistem ini dikembangkan sebagai luaran prototipe dari penelitian berjudul
                        <strong>&ldquo;Perancangan Sistem Traceability dan Sertifikasi Produk Ritel Lokal
                        Berbasis Blockchain dan Smart Contract untuk Penguatan Hilirisasi di Kota
                        Padang&rdquo;</strong>. Prototipe ini disusun untuk membuktikan konsep (proof of
                        concept) pada Tingkat Kesiapterapan Teknologi (TKT) 3, bukan sebagai sistem
                        ERP atau marketplace produksi penuh.
                    </p>
                    <ul class="research-list">
                        <li><i class="bi bi-check-circle-fill"></i> Model Traceability berbasis konsep blockchain</li>
                        <li><i class="bi bi-check-circle-fill"></i> Model Sertifikasi berbasis simulasi smart contract</li>
                        <li><i class="bi bi-check-circle-fill"></i> Arsitektur sistem modular &amp; dashboard analitik</li>
                    </ul>
                </div>
                <div class="col-lg-5">
                    <div class="research-card">
                        <i class="bi bi-journal-bookmark-fill research-card-icon"></i>
                        <div class="research-card-row">
                            <span>Skema</span><strong>PDP 2026</strong>
                        </div>
                        <div class="research-card-row">
                            <span>Status Prototipe</span><strong>TKT 3 &mdash; Proof of Concept</strong>
                        </div>
                        <div class="research-card-row">
                            <span>Lokus Studi</span><strong>Kota Padang</strong>
                        </div>
                        <div class="research-card-row">
                            <span>Fokus</span><strong>Hilirisasi Produk Ritel Lokal</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="section-cta py-5">
        <div class="container py-4 text-center">
            <h2 class="mb-3">Mulai Telusuri &amp; Verifikasi Produk Lokal Padang</h2>
            <p class="mb-4 text-secondary-light">Bergabung sebagai Producer, Distributor, atau Retailer &mdash; atau verifikasi produk tanpa perlu login.</p>
            <div class="d-flex justify-content-center flex-wrap gap-3">
                <a href="<?= BASE_URL ?>/register.php" class="btn btn-plts-primary btn-lg px-4">Register Sekarang</a>
                <a href="<?= BASE_URL ?>/verify.php" class="btn btn-plts-outline-light btn-lg px-4">Verify Product</a>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
