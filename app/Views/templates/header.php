<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'RailInfo — Indian Railways Information Portal') ?></title>
    <meta name="description" content="RailInfo — Check train schedules and live PNR status. An informational railway portal.">

    <!-- Bootstrap 5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- App CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<header class="ri-topbar d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="ri-topbar__gov">
            <i class="bi bi-shield-check"></i> Government of India &middot; Ministry of Railways &middot; Informational Portal
        </div>
        <div class="ri-topbar__links">
            <a href="#"><i class="bi bi-translate"></i> हिंदी</a>
            <span class="mx-2">|</span>
            <a href="<?= site_url('about') ?>">Help &amp; Support</a>
        </div>
    </div>
</header>

<nav class="navbar navbar-expand-lg ri-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand ri-brand" href="<?= site_url('/') ?>">
            <span class="ri-brand__icon"><i class="bi bi-train-front-fill"></i></span>
            <span class="ri-brand__text">
                RailInfo
                <small>Train Schedules &amp; PNR Status</small>
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#riNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="riNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('/') ?>"><i class="bi bi-house-door me-1"></i>Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('trains') ?>"><i class="bi bi-search me-1"></i>Train Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('pnr') ?>"><i class="bi bi-ticket-perforated me-1"></i>PNR Status</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('about') ?>"><i class="bi bi-info-circle me-1"></i>About</a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="btn ri-btn-outline btn-sm" href="<?= site_url('pnr') ?>">
                        <i class="bi bi-lightning-charge-fill me-1"></i>Quick PNR Check
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php if (session()->getFlashdata('message')): ?>
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show ri-alert" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('message')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<main>
