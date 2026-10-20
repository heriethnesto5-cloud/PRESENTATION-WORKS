<?php
require_once __DIR__ . '/../../config/database.php'; require_once __DIR__ . '/../../includes/functions.php'; require_login();
$id=(int)($_GET['id']??0);$editing=$id>0;$page_title=$editing?'Edit Book':'Add Book';$error='';
$book=['isbn'=>'','title'=>'','category_id'=>'','publication_year'=>'','total_copies'=>1,'available_copies'=>1,'shelf_location'=>'','description'=>''];
if($editing){$s=$pdo->prepare("SELECT * FROM books WHERE id=?");$s->execute([$id]);$found=$s->fetch();if(!$found){flash('Book not found.','danger');header('Location: '.app_url('modules/books/index.php'));exit;}$book=$found;}
$categories=$pdo->query("SELECT id,category_name FROM categories ORDER BY category_name")->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
  $book['isbn']=trim($_POST['isbn']??'')?:null;$book['title']=trim($_POST['title']??'');$book['category_id']=(int)($_POST['category_id']??0)?:null;$book['publication_year']=(int)($_POST['publication_year']??0)?:null;$book['total_copies']=max(1,(int)($_POST['total_copies']??1));$book['shelf_location']=trim($_POST['shelf_location']??'');$book['description']=trim($_POST['description']??'');
  if($book['title']===''){$error='Book title is required.';}
  else{
    try{
      if($editing){
        $q=$pdo->prepare("SELECT COUNT(*) FROM loans WHERE book_id=? AND status='Borrowed'");$q->execute([$id]);$active=(int)$q->fetchColumn();
        $available=max(0,$book['total_copies']-$active);
        if($book['total_copies']<$active){$error='Total copies cannot be less than the number currently borrowed.';}
        else{$q=$pdo->prepare("UPDATE books SET isbn=?,title=?,category_id=?,publication_year=?,total_copies=?,available_copies=?,shelf_location=?,description=? WHERE id=?");$q->execute([$book['isbn'],$book['title'],$book['category_id'],$book['publication_year'],$book['total_copies'],$available,$book['shelf_location']?:null,$book['description']?:null,$id]);flash('Book updated successfully.');header('Location: '.app_url('modules/books/index.php'));exit;}
      }else{
        $q=$pdo->prepare("INSERT INTO books(isbn,title,category_id,publication_year,total_copies,available_copies,shelf_location,description) VALUES(?,?,?,?,?,?,?,?)");$q->execute([$book['isbn'],$book['title'],$book['category_id'],$book['publication_year'],$book['total_copies'],$book['total_copies'],$book['shelf_location']?:null,$book['description']?:null]);flash('Book added successfully.');header('Location: '.app_url('modules/books/index.php'));exit;
      }
    }catch(PDOException $e){$error='Could not save the book. Check that the ISBN is unique.';}
  }
}
require __DIR__ . '/../../includes/header.php';
?>
<div class="card card-primary"><div class="card-header"><h3 class="card-title"><?= e($page_title) ?></h3></div><form method="post"><div class="card-body">
<?php if($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="row"><div class="form-group col-md-6"><label>Book Title *</label><input class="form-control" name="title" required value="<?= e($book['title']) ?>"></div><div class="form-group col-md-6"><label>ISBN</label><input class="form-control" name="isbn" value="<?= e($book['isbn']) ?>"></div></div>
<div class="row"><div class="form-group col-md-6"><label>Category</label><select class="form-control" name="category_id"><option value="">Select category</option><?php foreach($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (string)$book['category_id']===(string)$c['id']?'selected':'' ?>><?= e($c['category_name']) ?></option><?php endforeach; ?></select></div><div class="form-group col-md-6"><label>Publication Year</label><input type="number" min="1000" max="<?= date('Y')+1 ?>" class="form-control" name="publication_year" value="<?= e($book['publication_year']) ?>"></div></div>
<div class="row"><div class="form-group col-md-4"><label>Total Copies *</label><input type="number" min="1" class="form-control" name="total_copies" required value="<?= e($book['total_copies']) ?>"></div><div class="form-group col-md-4"><label>Shelf Location</label><input class="form-control" name="shelf_location" value="<?= e($book['shelf_location']) ?>"></div></div>
<div class="form-group"><label>Description</label><textarea class="form-control" name="description" rows="3"><?= e($book['description']) ?></textarea></div>
</div><div class="card-footer"><button class="btn btn-primary">Save Book</button> <a class="btn btn-default" href="index.php">Cancel</a></div></form></div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
