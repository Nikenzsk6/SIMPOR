<?php include_once '../includes/sidebar_admin.php'; ?>
<?php
/* ============================================================
   QUERY DASHBOARD — hanya READ, tidak mengubah alur bisnis
   ============================================================ */

/* --- KPI --- */
$q1 = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pelatih");
$q2 = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM anggota");
$q3 = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pendaftaran_ujian");
$q4 = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pengesahan WHERE status IN ('dikirim','diterima_pusat')");
$jml_pelatih = $q1 ? (int)mysqli_fetch_assoc($q1)['jml'] : 0;
$jml_anggota = $q2 ? (int)mysqli_fetch_assoc($q2)['jml'] : 0;
$jml_ujian   = $q3 ? (int)mysqli_fetch_assoc($q3)['jml'] : 0;
$jml_pending = $q4 ? (int)mysqli_fetch_assoc($q4)['jml'] : 0;

/* --- Tren pendaftaran ujian 6 bulan terakhir --- */
$bln_label = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$trend_label = [];
$trend_data  = [];
for ($i = 5; $i >= 0; $i--) {
    $ts = strtotime("-$i month");
    $trend_label[] = $bln_label[(int)date('n', $ts)] . ' ' . date('y', $ts);
    $trend_data[]  = 0;
}
$qt = mysqli_query($koneksi, "
    SELECT DATE_FORMAT(tgl_ujian,'%Y-%m') AS bln, COUNT(*) AS jml
    FROM pendaftaran_ujian
    WHERE tgl_ujian IS NOT NULL
      AND tgl_ujian >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY bln
");
while ($r = mysqli_fetch_assoc($qt)) {
    $parts = explode('-', $r['bln']);
    $cari  = $bln_label[(int)$parts[1]] . ' ' . substr($parts[0], 2);
    $idx   = array_search($cari, $trend_label);
    if ($idx !== false) $trend_data[$idx] = (int)$r['jml'];
}
$trend_total = array_sum($trend_data);

/* --- Distribusi status pengesahan --- */
$st_label = ['Dikirim', 'Diterima', 'Disetujui', 'Ditolak'];
$st_warna  = ['#0EA5E9', '#C9A227', '#16A34A', '#DC2626'];
$st_data   = [0, 0, 0, 0];
$qs = mysqli_query($koneksi, "
    SELECT status, COUNT(*) AS jml FROM pengesahan GROUP BY status
");
while ($r = mysqli_fetch_assoc($qs)) {
    $peta = [
        'dikirim'        => 0,
        'diterima_pusat' => 1,
        'disetujui'      => 2,
        'ditolak'        => 3,
    ];
    if (isset($peta[$r['status']])) $st_data[$peta[$r['status']]] = (int)$r['jml'];
}
$st_total = array_sum($st_data);

/* --- Anggota per tingkatan sabuk --- */
$sabuk_label = [];
$sabuk_data  = [];
$qsb = mysqli_query($koneksi, "
    SELECT tingkatan_sabuk AS s, COUNT(*) AS jml
    FROM anggota
    GROUP BY s
    ORDER BY jml DESC
");
while ($r = mysqli_fetch_assoc($qsb)) {
    $sabuk_label[] = $r['s'] ?: 'Belum ada';
    $sabuk_data[]  = (int)$r['jml'];
}
$sabuk_total = array_sum($sabuk_data);

/* --- Rata-rata nilai per tingkat ujian --- */
$nilai_label = [];
$nilai_data  = [];
$qn = mysqli_query($koneksi, "
    SELECT pu.tingkat_ujian AS t, ROUND(AVG(h.rata_rata_nilai), 1) AS avg_nilai
    FROM hasil_ujian h
    JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
    JOIN pendaftaran_ujian pu   ON dp.id_pendaftaran = pu.id_pendaftaran
    GROUP BY pu.tingkat_ujian
    ORDER BY avg_nilai DESC
");
while ($r = mysqli_fetch_assoc($qn)) {
    $nilai_label[] = $r['t'];
    $nilai_data[]  = (float)$r['avg_nilai'];
}
$nilai_total = count($nilai_data);
?>

<h4 class="page-title">Dashboard Admin</h4>

<!-- ===== KPI STRIP ===== -->
<div class="kpi-strip">
    <div class="kpi-item kpi-brand">
        <div class="kpi-ico"><i class="bi bi-person-badge"></i></div>
        <div class="kpi-body">
            <div class="kpi-value"><?= number_format($jml_anggota, 0, ',', '.') ?></div>
            <div class="kpi-label">Total Anggota</div>
        </div>
    </div>
    <div class="kpi-item kpi-cyan">
        <div class="kpi-ico"><i class="bi bi-people"></i></div>
        <div class="kpi-body">
            <div class="kpi-value"><?= number_format($jml_pelatih, 0, ',', '.') ?></div>
            <div class="kpi-label">Total Pelatih</div>
        </div>
    </div>
    <div class="kpi-item kpi-green">
        <div class="kpi-ico"><i class="bi bi-journal-check"></i></div>
        <div class="kpi-body">
            <div class="kpi-value"><?= number_format($jml_ujian, 0, ',', '.') ?></div>
            <div class="kpi-label">Total Ujian</div>
        </div>
    </div>
    <div class="kpi-item kpi-amber">
        <div class="kpi-ico"><i class="bi bi-send-check"></i></div>
        <div class="kpi-body">
            <div class="kpi-value"><?= number_format($jml_pending, 0, ',', '.') ?></div>
            <div class="kpi-label">Menunggu Pengesahan</div>
        </div>
    </div>
</div>

<!-- ===== BARIS GRAFIK 1 ===== -->
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">Tren Pendaftaran Ujian</h5>
                <p class="chart-subtitle">6 bulan terakhir</p>
                <?php if ($trend_total > 0): ?>
                    <div id="chartTren" class="chart-box"></div>
                <?php else: ?>
                    <div class="chart-empty">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Belum ada pendaftaran ujian dalam 6 bulan terakhir</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">Status Pengesahan</h5>
                <p class="chart-subtitle"><?= $st_total ?> pengajuan</p>
                <?php if ($st_total > 0): ?>
                    <div id="chartStatus" class="chart-box"></div>
                <?php else: ?>
                    <div class="chart-empty">
                        <i class="bi bi-inboxes"></i>
                        <span>Belum ada pengajuan pengesahan</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ===== BARIS GRAFIK 2 ===== -->
<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">Anggota per Tingkatan Sabuk</h5>
                <p class="chart-subtitle"><?= $sabuk_total ?> anggota terdaftar</p>
                <?php if ($sabuk_total > 0): ?>
                    <div id="chartSabuk" class="chart-box"></div>
                <?php else: ?>
                    <div class="chart-empty">
                        <i class="bi bi-people"></i>
                        <span>Belum ada anggota terdaftar</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">Rata-rata Nilai per Tingkat</h5>
                <p class="chart-subtitle">Dari hasil ujian</p>
                <?php if ($nilai_total > 0): ?>
                    <div id="chartNilai" class="chart-box"></div>
                <?php else: ?>
                    <div class="chart-empty">
                        <i class="bi bi-award"></i>
                        <span>Belum ada hasil ujian yang dinilai</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ===== TABEL AKTIVITAS ===== -->
<div class="content-wrapper">
    <h5 class="fw-bold mb-3" style="font-size:.95rem;">
        <i class="bi bi-clock-history me-1" style="color:var(--brand)"></i> Ujian Terbaru
    </h5>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Ujian</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian WHERE tgl_ujian >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) ORDER BY tgl_ujian DESC LIMIT 5");
            if ($q && mysqli_num_rows($q) > 0):
                while ($d = mysqli_fetch_assoc($q)):
            ?>
                    <tr>
                        <td><?= htmlspecialchars($d['tingkat_ujian']) ?></td>
                        <td><?= tgl_indonesia($d['tgl_ujian']) ?></td>
                        <td><?= status_label($d['status']) ?></td>
                        <td class="text-end">
                            <a href="ujian/detail.php?id=<?= $d['id_pendaftaran'] ?>" class="btn btn-sm btn-info">
                                Detail <i class="bi bi-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="bi bi-inbox d-block mb-2" style="font-size:1.4rem;opacity:.5"></i>
                        Belum ada ujian dalam 30 hari terakhir
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($trend_total > 0 || $st_total > 0 || $sabuk_total > 0 || $nilai_total > 0): ?>
    <script>
    var CHART_FONT = 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
    var CHART_TEXT = '#6B7280';

    <?php if ($trend_total > 0): ?>
    new ApexCharts(document.querySelector('#chartTren'), {
        chart: {
            type: 'area',
            height: 280,
            toolbar: { show: false },
            fontFamily: CHART_FONT,
            animations: { enabled: true, speed: 500 }
        },
        series: [{ name: 'Pendaftaran Ujian', data: <?= json_encode($trend_data) ?> }],
        xaxis: {
            categories: <?= json_encode($trend_label) ?>,
            labels: { style: { colors: CHART_TEXT, fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { style: { colors: CHART_TEXT, fontSize: '11px' } },
            min: 0,
            forceNiceScale: true
        },
        colors: ['#8C1F2F'],
        dataLabels: { enabled: false },
        stroke: { width: 3, curve: 'smooth', cap: 'round' },
        fill: {
            type: 'gradient',
            gradient: { opacityFrom: 0.32, opacityTo: 0.03 }
        },
        legend: { show: false },
        grid: {
            borderColor: '#E9ECEF',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } }
        },
        tooltip: {
            theme: 'light',
            y: { formatter: function (v) { return v + ' pendaftaran'; } }
        },
        markers: { size: 0, hover: { size: 5 } }
    }).render();
    <?php endif; ?>

    <?php if ($st_total > 0): ?>
    new ApexCharts(document.querySelector('#chartStatus'), {
        chart: {
            type: 'donut',
            height: 280,
            fontFamily: CHART_FONT
        },
        series: <?= json_encode($st_data) ?>,
        labels: <?= json_encode($st_label) ?>,
        colors: <?= json_encode($st_warna) ?>,
        stroke: { width: 2, colors: ['#FFFFFF'] },
        legend: {
            position: 'bottom',
            fontSize: '12px',
            labels: { colors: CHART_TEXT },
            markers: { size: 6 }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '64%',
                    labels: {
                        show: true,
                        name: { fontSize: '12px', color: CHART_TEXT },
                        value: {
                            fontSize: '22px',
                            fontWeight: 700,
                            color: '#1F2937',
                            formatter: function (v) { return v + ''; }
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '12px',
                            color: CHART_TEXT,
                            formatter: function (w) { return w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0); }
                        }
                    }
                }
            }
        },
        tooltip: { y: { formatter: function (v) { return v + ' pengajuan'; } } }
    }).render();
    <?php endif; ?>

    <?php if ($sabuk_total > 0): ?>
    new ApexCharts(document.querySelector('#chartSabuk'), {
        chart: {
            type: 'bar',
            height: 280,
            toolbar: { show: false },
            fontFamily: CHART_FONT
        },
        series: [{ name: 'Anggota', data: <?= json_encode($sabuk_data) ?> }],
        xaxis: {
            categories: <?= json_encode($sabuk_label) ?>,
            labels: { style: { colors: CHART_TEXT, fontSize: '11px' }, rotate: -25, hideOverlappingLabels: true }
        },
        yaxis: {
            labels: { style: { colors: CHART_TEXT, fontSize: '11px' } },
            min: 0,
            forceNiceScale: true
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '48%',
                borderRadius: 6,
                borderRadiusApplication: 'end'
            }
        },
        colors: ['#8C1F2F'],
        dataLabels: {
            enabled: true,
            offsetY: -4,
            style: { colors: ['#6B7280'], fontSize: '11px', fontWeight: 600 },
            formatter: function (v) { return v > 0 ? v : ''; }
        },
        legend: { show: false },
        grid: {
            borderColor: '#E9ECEF',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } }
        },
        tooltip: { y: { formatter: function (v) { return v + ' anggota'; } } }
    }).render();
    <?php endif; ?>

    <?php if ($nilai_total > 0): ?>
    new ApexCharts(document.querySelector('#chartNilai'), {
        chart: {
            type: 'bar',
            height: 280,
            toolbar: { show: false },
            fontFamily: CHART_FONT
        },
        series: [{ name: 'Rata-rata', data: <?= json_encode($nilai_data) ?> }],
        xaxis: {
            categories: <?= json_encode($nilai_label) ?>,
            labels: { style: { colors: CHART_TEXT, fontSize: '11px' } }
        },
        yaxis: {
            labels: { style: { colors: CHART_TEXT, fontSize: '11px' } },
            min: 0,
            max: 100,
            forceNiceScale: false
        },
        plotOptions: {
            bar: {
                columnWidth: '52%',
                borderRadius: 6,
                borderRadiusApplication: 'end'
            }
        },
        colors: ['#0891B2'],
        dataLabels: {
            enabled: true,
            offsetY: -4,
            style: { colors: ['#6B7280'], fontSize: '11px', fontWeight: 600 },
            formatter: function (v) { return v > 0 ? v.toFixed(1) : ''; }
        },
        legend: { show: false },
        grid: {
            borderColor: '#E9ECEF',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } }
        },
        tooltip: { y: { formatter: function (v) { return v.toFixed(1); } } }
    }).render();
    <?php endif; ?>
    </script>
<?php endif; ?>

<?php include_once '../includes/footer.php'; ?>