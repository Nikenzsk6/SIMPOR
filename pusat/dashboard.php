<?php include_once '../includes/sidebar_pusat.php'; ?>
<?php
/* ============================================================
   QUERY DASHBOARD — hanya READ, tidak mengubah alur bisnis
   ============================================================ */

/* --- KPI --- */
$jml_dikirim   = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pengesahan WHERE status='dikirim'"))['jml'];
$jml_disetujui = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pengesahan WHERE status='disetujui'"))['jml'];
$jml_ditolak   = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pengesahan WHERE status='ditolak'"))['jml'];
$jml_total     = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pengesahan"))['jml'];

/* --- Tren pengajuan 6 bulan terakhir --- */
$bln_label = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$trend_label = [];
$trend_data  = [];
for ($i = 5; $i >= 0; $i--) {
    $ts = strtotime("-$i month");
    $trend_label[] = $bln_label[(int)date('n', $ts)] . ' ' . date('y', $ts);
    $trend_data[]  = 0;
}
$qt = mysqli_query($koneksi, "
    SELECT DATE_FORMAT(tgl_kirim,'%Y-%m') AS bln, COUNT(*) AS jml
    FROM pengesahan
    WHERE tgl_kirim IS NOT NULL
      AND tgl_kirim >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY bln
");
while ($r = mysqli_fetch_assoc($qt)) {
    $parts = explode('-', $r['bln']);
    $cari  = $bln_label[(int)$parts[1]] . ' ' . substr($parts[0], 2);
    $idx   = array_search($cari, $trend_label);
    if ($idx !== false) $trend_data[$idx] = (int)$r['jml'];
}
$trend_total = array_sum($trend_data);

/* --- Distribusi status --- */
$st_data = [$jml_dikirim, $jml_disetujui, $jml_ditolak];
$st_label = ['Dikirim', 'Disetujui', 'Ditolak'];
$st_warna = ['#0EA5E9', '#16A34A', '#DC2626'];

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

/* --- Tingkat kelulusan --- */
$lulus_data  = [0, 0];
$lulus_label = ['Lulus', 'Tidak Lulus'];
$ql = mysqli_query($koneksi, "
    SELECT status_kelulusan, COUNT(*) AS jml
    FROM hasil_ujian
    GROUP BY status_kelulusan
");
while ($r = mysqli_fetch_assoc($ql)) {
    if (($r['status_kelulusan'] ?? '') === 'LULUS') $lulus_data[0] = (int)$r['jml'];
    else $lulus_data[1] = (int)$r['jml'];
}
$lulus_total = array_sum($lulus_data);

$cabang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil WHERE tipe='cabang'"));
$nama_cabang = htmlspecialchars($cabang['nama_organisasi'] ?? 'Cabang');
?>

<h4 class="page-title">Dashboard Pusat</h4>

<!-- ===== KPI STRIP ===== -->
<div class="kpi-strip">
    <div class="kpi-item kpi-cyan">
        <div class="kpi-ico"><i class="bi bi-envelope"></i></div>
        <div class="kpi-body">
            <div class="kpi-value"><?= number_format($jml_dikirim, 0, ',', '.') ?></div>
            <div class="kpi-label">Baru Dikirim</div>
        </div>
    </div>
    <div class="kpi-item kpi-green">
        <div class="kpi-ico"><i class="bi bi-check-circle"></i></div>
        <div class="kpi-body">
            <div class="kpi-value"><?= number_format($jml_disetujui, 0, ',', '.') ?></div>
            <div class="kpi-label">Disetujui</div>
        </div>
    </div>
    <div class="kpi-item kpi-red">
        <div class="kpi-ico"><i class="bi bi-x-circle"></i></div>
        <div class="kpi-body">
            <div class="kpi-value"><?= number_format($jml_ditolak, 0, ',', '.') ?></div>
            <div class="kpi-label">Ditolak</div>
        </div>
    </div>
    <div class="kpi-item kpi-slate">
        <div class="kpi-ico"><i class="bi bi-inboxes"></i></div>
        <div class="kpi-body">
            <div class="kpi-value"><?= number_format($jml_total, 0, ',', '.') ?></div>
            <div class="kpi-label">Total Pengesahan</div>
        </div>
    </div>
</div>

<!-- ===== BARIS GRAFIK 1 ===== -->
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">Tren Pengajuan</h5>
                <p class="chart-subtitle">6 bulan terakhir</p>
                <?php if ($trend_total > 0): ?>
                    <div id="chartTren" class="chart-box"></div>
                <?php else: ?>
                    <div class="chart-empty">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Belum ada pengajuan dalam 6 bulan terakhir</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">Status Pengesahan</h5>
                <p class="chart-subtitle"><?= $jml_total ?> pengajuan</p>
                <?php if ($jml_total > 0): ?>
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
                <h5 class="chart-title">Rata-rata Nilai per Tingkat</h5>
                <p class="chart-subtitle">Seluruh cabang</p>
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
    <div class="col-lg-6">
        <div class="card chart-card">
            <div class="card-body">
                <h5 class="chart-title">Kelulusan</h5>
                <p class="chart-subtitle">Seluruh peserta ujian</p>
                <?php if ($lulus_total > 0): ?>
                    <div id="chartLulus" class="chart-box"></div>
                <?php else: ?>
                    <div class="chart-empty">
                        <i class="bi bi-mortarboard"></i>
                        <span>Belum ada peserta ujian yang dinyatakan</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ===== TABEL AKTIVITAS ===== -->
<div class="content-wrapper">
    <h5 class="fw-bold mb-3" style="font-size:.95rem;">
        <i class="bi bi-clock-history me-1" style="color:var(--brand)"></i> Pengajuan Terbaru
    </h5>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Cabang</th>
                <th>Perihal</th>
                <th>Status</th>
                <th>Tanggal Kirim</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($koneksi, "
                SELECT p.*, h.status_kelulusan, dp.id_pendaftaran, pu.tingkat_ujian
                FROM pengesahan p
                LEFT JOIN hasil_ujian h ON p.id_hasil = h.id_hasil
                LEFT JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
                LEFT JOIN pendaftaran_ujian pu ON dp.id_pendaftaran = pu.id_pendaftaran
                ORDER BY p.tgl_kirim DESC LIMIT 5
            ");
            if ($q && mysqli_num_rows($q) > 0):
                while ($d = mysqli_fetch_assoc($q)):
            ?>
                    <tr>
                        <td><?= $nama_cabang ?></td>
                        <td>Ujian <?= htmlspecialchars($d['tingkat_ujian'] ?? '-') ?></td>
                        <td><?= status_label($d['status']) ?></td>
                        <td><?= tgl_indonesia_detik($d['tgl_kirim']) ?></td>
                        <td class="text-end">
                            <a href="detail.php?id=<?= $d['id_pengesahan'] ?>" class="btn btn-sm btn-info">
                                Detail <i class="bi bi-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-inbox d-block mb-2" style="font-size:1.4rem;opacity:.5"></i>
                        Belum ada pengajuan masuk
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($trend_total > 0 || $jml_total > 0 || $nilai_total > 0 || $lulus_total > 0): ?>
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
        series: [{ name: 'Pengajuan', data: <?= json_encode($trend_data) ?> }],
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
            y: { formatter: function (v) { return v + ' pengajuan'; } }
        },
        markers: { size: 0, hover: { size: 5 } }
    }).render();
    <?php endif; ?>

    <?php if ($jml_total > 0): ?>
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
                            color: '#1F2937'
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
            xaxis: { lines: { show: false } }
        },
        tooltip: { y: { formatter: function (v) { return v.toFixed(1); } } }
    }).render();
    <?php endif; ?>

    <?php if ($lulus_total > 0): ?>
    new ApexCharts(document.querySelector('#chartLulus'), {
        chart: {
            type: 'donut',
            height: 280,
            fontFamily: CHART_FONT
        },
        series: <?= json_encode($lulus_data) ?>,
        labels: <?= json_encode($lulus_label) ?>,
        colors: ['#16A34A', '#DC2626'],
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
                            color: '#1F2937'
                        },
                        total: {
                            show: true,
                            label: 'Peserta',
                            fontSize: '12px',
                            color: CHART_TEXT,
                            formatter: function (w) { return w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0); }
                        }
                    }
                }
            }
        },
        tooltip: { y: { formatter: function (v) { return v + ' peserta'; } } }
    }).render();
    <?php endif; ?>
    </script>
<?php endif; ?>

<?php include_once '../includes/footer.php'; ?>