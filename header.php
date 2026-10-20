<?php
require_once __DIR__ . '/functions.php';
require_login();
$page_title = $page_title ?? 'Dashboard';
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title) ?> | Library Management</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
    <li class="nav-item d-none d-sm-inline-block"><a href="<?= e(app_url('index.php')) ?>" class="nav-link">Home</a></li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item"><span class="nav-link"><i class="fas fa-user"></i> <?= e($_SESSION['full_name'] ?? 'User') ?></span></li>
    <li class="nav-item"><a class="nav-link" href="<?= e(app_url('auth/logout.php')) ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
  </ul>
</nav>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="<?= e(app_url('index.php')) ?>" class="brand-link">
    <i class="fas fa-book-reader brand-image ml-3 mt-1"></i>
    <span class="brand-text font-weight-light">Library System</span>
  </a>
  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image"><i class="fas fa-user-circle fa-2x text-light"></i></div>
      <div class="info"><a href="#" class="d-block"><?= e($_SESSION['full_name'] ?? 'Administrator') ?></a></div>
    </div>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <li class="nav-item"><a href="<?= e(app_url('index.php')) ?>" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <li class="nav-item"><a href="<?= e(app_url('modules/books/index.php')) ?>" class="nav-link"><i class="nav-icon fas fa-book"></i><p>Books</p></a></li>
        <li class="nav-item"><a href="<?= e(app_url('modules/categories/index.php')) ?>" class="nav-link"><i class="nav-icon fas fa-list"></i><p>Categories</p></a></li>
        <li class="nav-item"><a href="<?= e(app_url('modules/members/index.php')) ?>" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Members</p></a></li>
        <li class="nav-item"><a href="<?= e(app_url('modules/loans/index.php')) ?>" class="nav-link"><i class="nav-icon fas fa-exchange-alt"></i><p>Borrowing & Returns</p></a></li>
      </ul>
    </nav>
  </div>
</aside>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1 class="m-0"><?= e($page_title) ?></h1></div><div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="<?= e(app_url('index.php')) ?>">Home</a></li><li class="breadcrumb-item active"><?= e($page_title) ?></li></ol></div></div></div>
  </div>
  <section class="content"><div class="container-fluid">
    <?php show_flash(); ?>
