<?php

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\User;
use App\Models\SuratKeluar;

class DisposisiMail
{
    protected $disposisi;
    protected $userEmail;
    protected $userName;

    public function __construct($disposisi, $userEmail, $userName)
    {
        $this->disposisi = $disposisi;
        $this->userEmail = $userEmail;
        $this->userName = $userName;
    }

    public function send()
    {
        $mail = new PHPMailer(true);

        try {
            \Log::info('Memulai pengiriman email disposisi');
            
            // Konfigurasi Server
            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host');
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption');
            $mail->Port = config('mail.mailers.smtp.port');
            $mail->setFrom(config('mail.from.address'), config('mail.from.name'));

            // Embed gambar
            $logoPath = public_path('images/logo.png');
            $mail->addEmbeddedImage($logoPath, 'logo', 'logo.png');

            // Ambil data tambahan dengan eager loading untuk jabatan dan surat keluar
            $suratKeluar = SuratKeluar::find($this->disposisi->kd_surat_keluar);
            $pengirim = User::with('jabatan')->find($this->disposisi->asal_surat);

            // Tambahkan file surat sebagai lampiran jika ada
            if ($suratKeluar && $suratKeluar->file_path) {
                $filePath = storage_path('app/public/' . $suratKeluar->file_path);
                if (file_exists($filePath)) {
                    $mail->addAttachment($filePath);
                }
            }

            if (!$pengirim) {
                \Log::error('Pengirim tidak ditemukan untuk ID: ' . $this->disposisi->asal_surat);
                return false;
            }

            // Dapatkan nama jabatan dengan accessor
            $jabatanPengirim = $pengirim->jabatanName;
            
            \Log::info('Data pengirim:', [
                'nama' => $pengirim->name,
                'jabatan' => $jabatanPengirim
            ]);

            // Penerima
            $mail->addAddress($this->userEmail, $this->userName);

            // Konten
            $mail->isHTML(true);
            $mail->Subject = 'Disposisi Baru - ' . $this->disposisi->jenis_disposisi;
            
            // Template email yang lebih menarik
            $body = "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { 
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        color: #333;
                    }
                    .email-container {
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                    }
                    .header {
                        background-color: #ffffff;
                        padding: 20px;
                        text-align: center;
                        border-radius: 10px 10px 0 0;
                        border: 1px solid #e5e7eb;
                        border-bottom: none;
                    }
                    .header img {
                        max-height: 60px;
                        margin-bottom: 15px;
                    }
                    .header h2 {
                        color: #374151;
                        margin: 10px 0;
                        font-size: 24px;
                    }
                    .content {
                        background-color: #ffffff;
                        padding: 30px;
                        border: 1px solid #e5e7eb;
                        border-radius: 0 0 10px 10px;
                    }
                    .footer {
                        text-align: center;
                        margin-top: 20px;
                        padding: 20px;
                        font-size: 12px;
                        color: #666;
                    }
                    .button {
                        display: inline-block;
                        padding: 12px 24px;
                        background-color: #10B981;
                        color: #ffffff !important;
                        text-decoration: none;
                        border-radius: 5px;
                        margin-top: 20px;
                        font-weight: 600;
                        transition: background-color 0.3s ease;
                    }
                    .button:hover {
                        background-color: #059669;
                    }
                    .info-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 20px 0;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                    }
                    .info-table td {
                        padding: 12px 16px;
                        border: 1px solid #e5e7eb;
                    }
                    .info-table td:first-child {
                        font-weight: 600;
                        width: 35%;
                        background-color: #f9fafb;
                    }
                    .priority {
                        display: inline-block;
                        padding: 6px 12px;
                        border-radius: 15px;
                        font-size: 12px;
                        font-weight: bold;
                        background-color: " . ($this->disposisi->tingkat_kepentingan == 'penting' ? '#FF4E4E' : ($this->disposisi->tingkat_kepentingan == 'rahasia' ? '#000000' : '#10B981')) . ";
                        color: white;
                    }
                    .divider {
                        height: 2px;
                        background-color: #e5e7eb;
                        margin: 15px 0;
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='header'>
                        <img src='cid:logo' alt='SISM Azra Logo'>
                        <div class='divider'></div>
                        <h2>Notifikasi Disposisi Baru</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Halo <strong>{$this->userName}</strong>,</p>
                        <p>Anda telah menerima disposisi baru dengan detail sebagai berikut:</p>
                        
                        <table class='info-table'>
                            <tr>
                                <td>Nomor Surat</td>
                                <td>{$suratKeluar->nomor_surat}</td>
                            </tr>
                            <tr>
                                <td>Perihal</td>
                                <td>{$suratKeluar->perihal}</td>
                            </tr>
                            <tr>
                                <td>Pengirim Disposisi</td>
                                <td>{$pengirim->name} - {$jabatanPengirim}</td>
                            </tr>
                            <tr>
                                <td>Jenis Disposisi</td>
                                <td>{$this->disposisi->jenis_disposisi}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>{$this->disposisi->status_penyelesaian}</td>
                            </tr>
                            <tr>
                                <td>Tingkat Kepentingan</td>
                                <td><span class='priority'>{$this->disposisi->tingkat_kepentingan}</span></td>
                            </tr>
                            <tr>
                                <td>Instruksi</td>
                                <td>{$this->disposisi->instruksi}</td>
                            </tr>
                            <tr>
                                <td>Catatan</td>
                                <td>{$this->disposisi->catatan}</td>
                            </tr>
                        </table>

                        <div style='text-align: center;'>
                            <a href='http://127.0.0.1:8000/disposisi/{$this->disposisi->id}' class='button'>
                                Lihat Detail Disposisi
                            </a>
                        </div>
                    </div>

                    <div class='footer'>
                        <p>Email ini dikirim secara otomatis oleh Sistem Informasi Surat Menyurat Azra.</p>
                        <p>© " . date('Y') . " SISM Azra. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            $mail->Body = $body;

            $mail->send();
            
            \Log::info('Email berhasil dikirim ke: ' . $this->userEmail);
            return true;
        } catch (\Exception $e) {
            \Log::error('DisposisiMail Error: ' . $e->getMessage());
            \Log::error('File Path: ' . ($suratKeluar->file_path ?? 'not found'));
            return false;
        }
    }
}
