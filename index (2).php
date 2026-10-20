<?php
require_once __DIR__ . '/../../config/database.php'; require_once __DIR__ . '/../../includes/functions.php'; require_login();
$page_title='Books';
if (isset($_GET['delete'])) {
    $id=(int)$_GET['delete'];
    $q=$pdo->prepare("SELECT COUNT(*) FROM loans WHERE book_id=? AND status='Borrowed'"); $q->execute([$id]);
    if ((int)$q->fetchColumn()>0) { flash('This book has an active loan and cannot be deleted.','warning'); }
    else { try {$q=$pdo->prepare("DELETE FROM books WHERE id=?");$q->execute([$id]);flash('Book deleted.');} catch(PDOException $e){flash('Unable to delete this book.','danger');} }
    header('Location: '.app_url('modules/books/index.php')); exit;
}
$search=trim($_GET['q']??'');
$sql="SELECT b.*, c.category_name FROM books b LEFT JOIN categories c ON c.id=b.category_id";
$params=[];
if($search!==''){ $sql.=" WHERE b.title LIKE ? OR b.isbn LIKE ? OR b.shelf_location LIKE ?"; $like="%$search%"; $params=[$like,$like,$like];}
$sql.=" ORDER BY b.id DESC";
$stmt=$pdo->prepare($sql);$stmt->execute($params);$books=$stmt->fetchAll();
require __DIR__ . '/../../includes/header.php';
?>
<div class="card"><div class="card-header"><h3 class="card-title">Book Inventory</h3><div class="card-tools"><a href="<?= e(app_url('modules/books/form.php')) ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Book</a></div></div>
<div class="card-body"><form class="form-inline mb-3" method="get"><input name="q" class="form-control mr-2" placeholder="Search title, ISBN, shelf..." value="<?= e($search) ?>"><button class="btn btn-secondary">Search</button><a class="btn btn-default ml-2" href="index.php">Reset</a></form>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>ID</th><th>ISBN</th><th>Title</th><th>Category</th><th>Copies</th><th>Shelf</th><th>Actions</th></tr></thead><tbody>
<?php foreach($books as $b): ?><tr><td><?= (int)$b['id'] ?></td><td><?= e($b['isbn']) ?></td><td><?= e($b['title']) ?></td><td><?= e($b['category_name']??'—') ?></td><td><span class="badge badge-info"><?= (int)$b['available_copies'] ?> / <?= (int)$b['total_copies'] ?></span></td><td><?= e($b['shelf_location']) ?></td><td><a class="btn btn-sm btn-info" href="<?= e(app_url('modules/books/form.php?id='.$b['id'])) ?>">Edit</a> <a class="btn btn-sm btn-danger" onclick="return confirm('Delete this book?')" href="?delete=<?= (int)$b['id'] ?>">Delete</a></td></tr><?php endforeach; ?>
<?php if(!$books): ?><tr><td colspan="7" class="text-center">No books found.</td></tr><?php endif; ?>
</tbody></table></div></div></div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
