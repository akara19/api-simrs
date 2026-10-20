# SIMRS Poli — Aplikasi Praktikum React Native (Expo)

Frontend CRUD untuk SIMRS: **Poli, Dokter, Pasien, Pendaftaran, Rekam Medis, Resep Obat**.
Kode sama dengan modul praktikum "CRUD SIMRS dengan React Native" (Bagian A dan B).
Dibuat dengan Expo SDK 58 (React Native 0.88, React Navigation 7).

## 1. Prasyarat
- Node.js LTS dan npm
- XAMPP: Apache + MySQL aktif, folder API `api-simrs` ada di `htdocs`
  (uji: buka `http://localhost/api-simrs/poli/index.php` di browser, harus muncul JSON)
- Salah satu: browser (Chrome/Edge), aplikasi **Expo Go** di ponsel, atau emulator Android

## 2. Menjalankan
```bash
npm install
npx expo start
```
Lalu pilih cara menguji:
- **Browser:** tekan `w` (atau `npm run web`)
- **Ponsel:** pindai kode QR dengan Expo Go. Ponsel dan komputer harus **satu jaringan Wi-Fi**.
- **Emulator Android:** tekan `a`

## 3. Konfigurasi alamat API (`src/config/api.js`)
Alamat server dideteksi otomatis:

| Cara uji | Alamat yang dipakai |
|---|---|
| Browser | `localhost` |
| Expo Go / emulator | IP komputer yang menjalankan `expo start` (= komputer XAMPP) |

Ubah hanya bila perlu, di bagian atas `src/config/api.js`:
- `API_FOLDER`: nama folder API di `htdocs` (bawaan `api-simrs`)
- `API_PORT`: isi `'8080'` bila Apache bukan di port 80
- `MANUAL_HOST`: isi IP LAN komputer bila deteksi otomatis gagal (mis. `'192.168.1.10'`)

## 4. Endpoint yang dipakai

| Menu | Folder API | Keterangan |
|---|---|---|
| Data Poli | `/poli/` | Sudah diuji dengan file PHP `poli` asli |
| Dokter, Pasien, Pendaftaran, Rekam Medis, Resep Obat | `/dokter/`, `/pasien/`, `/pendaftaran/`, `/rekam_medis/`, `/resep_obat/` | **Diasumsikan** berpola sama dengan `poli` (`index`, `create`, `update`, `delete`) |

Pola yang diasumsikan: `index.php` mengembalikan `data` berupa array; `create.php` menerima body JSON;
`update.php` dan `delete.php` menerima parameter lewat query string. Respons berformat
`{ "status": "success|error", "message": "...", "data": ... }`.
Bila `update.php` pada API Anda membaca body JSON, ubah baris terkait di `src/services/apis.js`:
`createCrudService('nama_folder', { updateVia: 'json' })`.

Urutan mengisi data (karena kunci asing): `users` lalu `pasien`; `dokter` + `poli` lalu `jadwal_praktik`,
`pendaftaran`, `rekam_medis`, `resep_obat`.

## 5. Struktur proyek
```
App.js                      navigasi + daftar layar
src/config/api.js           alamat server
src/services/               poliService.js, crudService.js (generik), apis.js
src/components/             makeListScreen.js, makeFormScreen.js (pabrik layar)
src/screens/                HomeScreen, PoliListScreen, PoliFormScreen, entityScreens.js
src/utils/                  dialog.js (alert lintas platform), validators.js
```

## 6. Pemecahan masalah

| Gejala | Solusi |
|---|---|
| `Failed to fetch` / `Network request failed` | Apache mati, firewall memblokir port 80, atau alamat salah: isi `MANUAL_HOST` |
| Dialog "Respons server bukan JSON" | Ada error PHP; buka URL API langsung di browser untuk melihat pesannya |
| `blocked by CORS policy` (browser) | Pastikan tiap endpoint mengirim `Access-Control-Allow-Origin: *` dan tidak ada error PHP sebelum header |
| Daftar pilihan (pasien/dokter) kosong | Isi tabel sumbernya lebih dulu |
| Perubahan tidak tampil | Tutup Expo Go lalu jalankan `npx expo start --clear` |
