<?php

/**
 * DashboardController
 * 
 * Controller ini menangani tampilan dashboard utama aplikasi.
 * Dashboard menampilkan:
 * - Statistik sistem (total users, active sessions, roles, permissions)
 * - Last login users
 * - Failed login attempts
 * - Security alerts
 * - System health indicator
 * 
 * @package App\Http\Controllers
 * @author Tracking App Team
 */

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AuthSession;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama
     * 
     * Method ini mengumpulkan semua data yang diperlukan untuk dashboard:
     * - Statistik sistem
     * - User yang baru login
     * - Failed login attempts dalam 24 jam terakhir
     * - Security alerts
     * - System health score
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // ========== CORE STATISTICS ==========
        // Mengumpulkan statistik dasar sistem
        $stats = [
            'totalUsers' => User::count(), // Total jumlah user di sistem
            'totalActiveSessions' => AuthSession::where('expires_at', '>', now())->count(), // Sesi aktif saat ini
            'rolesCount' => Role::count(), // Total jumlah roles
            'permissionsCount' => Permission::count(), // Total jumlah permissions
        ];

        // ========== LAST LOGIN USERS ==========
        // Mengambil 10 session terbaru, lalu ambil unique user_id (satu user bisa punya multiple session)
        // Lalu ambil 5 user pertama yang unique
        $lastLoginUsers = AuthSession::with('user')
            ->where('expires_at', '>', now()) // Hanya session yang masih aktif
            ->orderBy('issued_at', 'desc') // Urutkan dari yang terbaru
            ->take(10)
            ->get()
            ->unique('user_id') // Ambil unique berdasarkan user_id
            ->take(5); // Ambil 5 user pertama

        // ========== FAILED LOGIN ATTEMPTS ==========
        // Menghitung jumlah failed login attempts dalam 24 jam terakhir
        $failedLoginAttempts = AuditLog::where('action', 'login_failed')
            ->where('created_at', '>=', now()->subDay()) // 24 jam terakhir
            ->count();

        // ========== SECURITY ALERTS ==========
        // Mendeteksi aktivitas mencurigakan berdasarkan failed login attempts
        $securityAlerts = $this->getSecurityAlerts();

        // ========== SYSTEM HEALTH INDICATOR ==========
        // Menghitung skor kesehatan sistem berdasarkan berbagai faktor
        $systemHealth = $this->calculateSystemHealth($stats, $failedLoginAttempts, $securityAlerts);

        // Pass semua data ke view
        return view('dashboard', compact(
            'stats',
            'lastLoginUsers',
            'failedLoginAttempts',
            'securityAlerts',
            'systemHealth'
        ));
    }

    /**
     * Mendapatkan security alerts berdasarkan failed login attempts
     * 
     * Method ini menganalisis failed login attempts dalam 1 jam terakhir untuk
     * mendeteksi aktivitas mencurigakan seperti:
     * - Multiple failed attempts dari IP yang sama (>= 5 attempts)
     * - Total failed attempts yang tinggi dalam waktu singkat (>= 10 attempts/hour)
     * 
     * Alert types:
     * - 'warning': Multiple attempts dari IP tertentu
     * - 'danger': Total attempts tinggi (potensi brute force attack)
     * 
     * @return array Array of alerts dengan struktur:
     *   [
     *     'type' => 'warning'|'danger',
     *     'message' => string,
     *     'ip_address' => string (optional),
     *     'attempts' => int,
     *     'timestamp' => Carbon instance
     *   ]
     */
    private function getSecurityAlerts()
    {
        $alerts = [];

        // Mengambil semua failed login attempts dalam 1 jam terakhir
        $recentFailedLogins = AuditLog::where('action', 'login_failed')
            ->where('created_at', '>=', now()->subHour())
            ->get();

        // Mengelompokkan berdasarkan IP address untuk deteksi brute force dari IP tertentu
        $ipAttempts = [];
        foreach ($recentFailedLogins as $login) {
            if (isset($login->details['ip_address'])) {
                $ipAddress = $login->details['ip_address'];
                if (!isset($ipAttempts[$ipAddress])) {
                    $ipAttempts[$ipAddress] = 0;
                }
                $ipAttempts[$ipAddress]++;
            }
        }

        // Cek apakah ada IP yang melakukan >= 5 failed attempts (potensi brute force)
        foreach ($ipAttempts as $ipAddress => $attempts) {
            if ($attempts >= 5) {
                $alerts[] = [
                    'type' => 'warning',
                    'message' => "Multiple failed login attempts from IP: {$ipAddress} ({$attempts} attempts in last hour)",
                    'ip_address' => $ipAddress,
                    'attempts' => $attempts,
                    'timestamp' => now(),
                ];
            }
        }

        // Cek total failed attempts dalam 1 jam (>= 10 berarti ada aktivitas mencurigakan)
        $totalFailedLastHour = $recentFailedLogins->count();
        if ($totalFailedLastHour >= 10) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "High number of failed login attempts detected: {$totalFailedLastHour} in the last hour",
                'attempts' => $totalFailedLastHour,
                'timestamp' => now(),
            ];
        }

        return $alerts;
    }

    /**
     * Menghitung system health indicator
     * 
     * Method ini menghitung skor kesehatan sistem berdasarkan berbagai faktor:
     * - Rasio user aktif vs tidak aktif
     * - Jumlah user pending
     * - Jumlah failed login attempts
     * - Security alerts (critical dan warning)
     * 
     * Health Score dimulai dari 100 dan dikurangi berdasarkan issues yang ditemukan:
     * - High inactive users (>50%): -10 points
     * - Many pending users (>10): -5 points
     * - High failed login attempts (>20): -15 points
     * - Critical security alerts: -20 points
     * - Multiple warnings (>3): -10 points
     * 
     * Health Status:
     * - Healthy (score >= 80): Sistem berjalan baik
     * - Warning (score 60-79): Ada beberapa issues yang perlu diperhatikan
     * - Critical (score < 60): Ada masalah serius yang perlu ditangani segera
     * 
     * @param array $stats Statistik sistem
     * @param int $failedLoginAttempts Jumlah failed login attempts dalam 24 jam
     * @param array $securityAlerts Array of security alerts
     * @return array Array dengan struktur:
     *   [
     *     'score' => int (0-100),
     *     'status' => 'healthy'|'warning'|'critical',
     *     'statusColor' => 'success'|'warning'|'danger',
     *     'statusText' => string,
     *     'issues' => array
     *   ]
     */
    private function calculateSystemHealth($stats, $failedLoginAttempts, $securityAlerts)
    {
        // Mulai dengan score 100 (perfect health)
        $healthScore = 100;
        $issues = [];

        // ========== CEK RASIO USER AKTIF ==========
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();
        $pendingUsers = User::where('status', 'pending')->count();
        
        // Jika >50% user tidak aktif, ini bisa menjadi masalah
        if ($activeUsers > 0) {
            $inactiveRatio = ($inactiveUsers / $stats['totalUsers']) * 100;
            if ($inactiveRatio > 50) {
                $healthScore -= 10;
                $issues[] = 'High number of inactive users';
            }
        }

        // ========== CEK USER PENDING ==========
        // Banyak user pending (>10) bisa berarti ada backlog aktivasi
        if ($pendingUsers > 10) {
            $healthScore -= 5;
            $issues[] = 'Many pending user accounts';
        }

        // ========== CEK FAILED LOGIN ATTEMPTS ==========
        // Banyak failed login attempts (>20 dalam 24 jam) bisa berarti ada serangan
        if ($failedLoginAttempts > 20) {
            $healthScore -= 15;
            $issues[] = 'High number of failed login attempts';
        }

        // ========== CEK SECURITY ALERTS ==========
        // Hitung jumlah critical dan warning alerts
        $criticalAlerts = collect($securityAlerts)->where('type', 'danger')->count();
        $warningAlerts = collect($securityAlerts)->where('type', 'warning')->count();
        
        // Critical alerts sangat serius
        if ($criticalAlerts > 0) {
            $healthScore -= 20;
            $issues[] = 'Critical security alerts detected';
        }
        
        // Banyak warning alerts juga perlu diperhatikan
        if ($warningAlerts > 3) {
            $healthScore -= 10;
            $issues[] = 'Multiple security warnings';
        }

        // ========== TENTUKAN HEALTH STATUS ==========
        // Berdasarkan score final, tentukan status kesehatan sistem
        if ($healthScore >= 80) {
            $status = 'healthy';
            $statusColor = 'success';
            $statusText = 'Healthy';
        } elseif ($healthScore >= 60) {
            $status = 'warning';
            $statusColor = 'warning';
            $statusText = 'Warning';
        } else {
            $status = 'critical';
            $statusColor = 'danger';
            $statusText = 'Critical';
        }

        return [
            'score' => $healthScore,
            'status' => $status,
            'statusColor' => $statusColor,
            'statusText' => $statusText,
            'issues' => $issues,
        ];
    }
}