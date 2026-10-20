<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$page_title = 'Dashboard';
$totalBooks = (int)$pdo->query("SELECT COALESCE(SUM(total_copies),0) FROM books")->fetchColumn();
$availableBooks = (int)$pdo->query("SELECT COALESCE(SUM(available_copies),0) FROM books")->fetchColumn();
$totalMembers = (int)$pdo->query("SELECT COUNT(*) FROM members WHERE status='Active'")->fetchColumn();
$borrowedBooks = (int)$pdo->query("SELECT COUNT(*) FROM loans WHERE status='Borrowed'")->fetchColumn();
$overdueBooks = (int)$pdo->query("SELECT COUNT(*) FROM loans WHERE status='Borrowed' AND due_date < CURDATE()")->fetchColumn();
$recent = $pdo->query("SELECT l.id, b.title, m.full_name, l.borrowed_date, l.due_date, l.status FROM loans l JOIN books b ON b.id=l.book_id JOIN members m ON m.id=l.member_id ORDER BY l.id DESC LIMIT 6")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="row">
  <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3><?= $totalBooks ?></h3><p>Total Book Copies</p></div><div class="icon"><i class="fas fa-book"></i></div><a href="<?= e(app_url('modules/books/index.php')) ?>" class="small-box-footer">Manage books <i class="fas fa-arrow-circle-right"></i></a></div></div>
  <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3><?= $availableBooks ?></h3><p>Available Copies</p></div><div class="icon"><i class="fas fa-book-open"></i></div><a href="<?= e(app_url('modules/books/index.php')) ?>" class="small-box-footer">View inventory <i class="fas fa-arrow-circle-right"></i></a></div></div>
  <div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?= $totalMembers ?></h3><p>Active Members</p></div><div class="icon"><i class="fas fa-users"></i></div><a href="<?= e(app_url('modules/members/index.php')) ?>" class="small-box-footer">Manage members <i class="fas fa-arrow-circle-right"></i></a></div></div>
  <div class="col-lg-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3><?= $overdueBooks ?></h3><p>Overdue Loans</p></div><div class="icon"><i class="fas fa-exclamation-triangle"></i></div><a href="<?= e(app_url('modules/loans/index.php')) ?>" class="small-box-footer">Review loans <i class="fas fa-arrow-circle-right"></i></a></div></div>
</div>
<div class="row">
  <div class="col-md-4"><div class="info-box"><span class="info-box-icon bg-primary"><i class="fas fa-exchange-alt"></i></span><div class="info-box-content"><span class="info-box-text">Currently Borrowed</span><span class="info-box-number"><?= $borrowedBooks ?></span></div></div></div>
  <div class="col-md-4"><div class="info-box"><span class="info-box-icon bg-secondary"><i class="fas fa-book-reader"></i></span><div class="info-box-content"><span class="info-box-text">Book Titles</span><span class="info-box-number"><?= (int)$pdo->query("SELECT COUNT(*) FROM books")->fetchColumn() ?></span></div></div></div>
  <div class="col-md-4"><div class="info-box"><span class="info-box-icon bg-teal"><i class="fas fa-tags"></i></span><div class="info-box-content"><span class="info-box-text">Categories</span><span class="info-box-number"><?= (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn() ?></span></div></div></div>
</div>
<div class="card"><div class="card-header"><h3 class="card-title">Recent Borrowing Activity</h3><div class="card-tools"><a class="btn btn-primary btn-sm" href="<?= e(app_url('modules/loans/index.php')) ?>"><i class="fas fa-plus"></i> Issue Book</a></div></div><div class="card-body table-responsive p-0"><table class="table table-hover text-nowrap"><thead><tr><th>ID</th><th>Book</th><th>Member</th><th>Borrowed</th><th>Due</th><th>Status</th></tr></thead><tbody>
<?php if (!$recent): ?><tr><td colspan="6" class="text-center p-4">No borrowing records yet.</td></tr><?php else: foreach ($recent as $r): $late = $r['status']==='Borrowed' && $r['due_date'] < date('Y-m-d'); ?>
<tr><td><?= (int)$r['id'] ?></td><td><?= e($r['title']) ?></td><td><?= e($r['full_name']) ?></td><td><?= e($r['borrowed_date']) ?></td><td><?= e($r['due_date']) ?></td><td><span class="badge badge-<?= $r['status']==='Returned'?'success':($late?'danger':'warning') ?>"><?= $late?'Overdue':e($r['status']) ?></span></td></tr>
<?php endforeach; endif; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
