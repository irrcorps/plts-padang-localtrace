<?php

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function flash(string $type, string $message): void
{
    Auth::start();
    $_SESSION['flash'][$type] = $message;
}

function getFlash(string $type): ?string
{
    Auth::start();
    if (!empty($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

function roleLabel(string $role): string
{
    switch ($role) {
        case 'admin':       return 'Administrator';
        case 'producer':    return 'Producer / UMKM';
        case 'distributor': return 'Distributor';
        case 'retailer':    return 'Retailer';
        default:            return ucfirst($role);
    }
}

function generateHash(string $data): string
{
    return hash('sha256', $data . microtime());
}

function statusLabel(string $status): string
{
    switch ($status) {
        case 'draft':     return 'Draft';
        case 'submitted': return 'Submitted';
        case 'verified':  return 'Verified';
        case 'certified': return 'Certified';
        case 'rejected':  return 'Rejected';
        default:          return ucfirst($status);
    }
}

function statusBadgeClass(string $status): string
{
    switch ($status) {
        case 'draft':     return 'badge-status badge-status-draft';
        case 'submitted': return 'badge-status badge-status-submitted';
        case 'verified':  return 'badge-status badge-status-verified';
        case 'certified': return 'badge-status badge-status-certified';
        case 'rejected':  return 'badge-status badge-status-rejected';
        default:          return 'badge-status';
    }
}

function formatDateID(?string $date): string
{
    if (!$date) {
        return '-';
    }
    $timestamp = strtotime($date);
    return $timestamp ? date('d M Y', $timestamp) : '-';
}

function absoluteUrl(string $path): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . BASE_URL . '/' . ltrim($path, '/');
}

function shortHash(?string $hash, int $length = 16): string
{
    if (!$hash) {
        return '-';
    }
    return substr($hash, 0, $length) . '…';
}
