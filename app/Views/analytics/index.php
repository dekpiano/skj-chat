<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<div class="container-fluid p-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-chart-line text-primary me-2"></i>รายงานและสถิติการสนทนา (Live Chat Analytics)</h4>
        <p class="text-muted small mb-0">ภาพรวมปริมาณการติดต่อสอบถาม ประสิทธิภาพของ AI บอท และการทำงานของทีมงานตอบแชท</p>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">เซสชันทั้งหมด</div>
                        <h3 class="fw-bold mb-0 text-dark"><?= number_format($totalChats) ?></h3>
                    </div>
                    <div class="rounded-circle p-3 bg-primary-subtle text-primary"><i class="fa-regular fa-comments fs-4"></i></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">แชทวันนี้</div>
                        <h3 class="fw-bold mb-0 text-success"><?= number_format($todayChats) ?></h3>
                    </div>
                    <div class="rounded-circle p-3 bg-success-subtle text-success"><i class="fa-solid fa-calendar-day fs-4"></i></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">ข้อความทั้งหมด</div>
                        <h3 class="fw-bold mb-0 text-info"><?= number_format($totalMessages) ?></h3>
                    </div>
                    <div class="rounded-circle p-3 bg-info-subtle text-info"><i class="fa-solid fa-message fs-4"></i></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">AI ตอบคำถาม</div>
                        <h3 class="fw-bold mb-0 text-warning"><?= number_format($botReplies) ?></h3>
                    </div>
                    <div class="rounded-circle p-3 bg-warning-subtle text-warning"><i class="fa-solid fa-robot fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h6 class="fw-bold mb-3">ปริมาณการสนทนาย้อนหลัง 7 วัน</h6>
                <canvas id="dailyChatsChart" height="120"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h6 class="fw-bold mb-3">สัดส่วนการตอบ (Bot vs Agent)</h6>
                <canvas id="ratioChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Agent Leaderboard -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="p-3 border-bottom">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-trophy text-warning me-2"></i>ผลงานเจ้าหน้าที่ตอบแชท</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>เจ้าหน้าที่</th>
                        <th>จำนวนข้อความที่ตอบ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($agentStats)): ?>
                        <?php foreach ($agentStats as $idx => $st): ?>
                            <tr>
                                <td class="text-muted small"><?= $idx + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 min-w-0">
                                        <img src="<?= esc($st->avatar ?: ('https://ui-avatars.com/api/?name=' . urlencode($st->fullname) . '&background=fce4ec&color=c2185b')) ?>" class="rounded-circle avatar-img flex-shrink-0" width="32" height="32" style="object-fit: cover;" alt="avatar" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=<?= urlencode(esc($st->fullname)) ?>&background=fce4ec&color=c2185b';">
                                        <span class="fw-semibold text-dark text-truncate" style="max-width: 250px;" title="<?= esc($st->fullname) ?>"><?= esc($st->fullname) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill px-3"><?= number_format($st->total_replies) ?> ข้อความ</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center py-4 text-muted">ยังไม่มีข้อมูลสถิติเจ้าหน้าที่</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Daily Chart
        const dailyData = <?= json_encode($dailyChats) ?>;
        const labels = dailyData.map(d => d.date);
        const counts = dailyData.map(d => parseInt(d.count));

        new Chart(document.getElementById('dailyChatsChart'), {
            type: 'line',
            data: {
                labels: labels.length > 0 ? labels : ['วันนี้'],
                datasets: [{
                    label: 'จำนวนเซสชันแชท',
                    data: counts.length > 0 ? counts : [<?= $todayChats ?>],
                    borderColor: '#e91e63',
                    backgroundColor: 'rgba(233, 30, 99, 0.1)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#e91e63'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });

        // Ratio Chart
        new Chart(document.getElementById('ratioChart'), {
            type: 'doughnut',
            data: {
                labels: ['AI Bot ตอบ', 'เจ้าหน้าที่ตอบ'],
                datasets: [{
                    data: [<?= $botReplies ?>, <?= $adminReplies ?>],
                    backgroundColor: ['#0284c7', '#e91e63'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
