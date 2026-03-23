# Data Visualization Dashboard

Project ini sekarang hanya memiliki **2 sumber data non-dummy**:
- Weather API (Open-Meteo)
- Student Dataset CSV (file upload/manual di storage)

Seluruh modul data dummy (dashboard penjualan, CRUD product/customer/order/category, dan DummyJSON API) sudah dihapus.

## Fitur Aktif

### 1) Weather API Dashboard
- Dashboard cuaca kota-kota Pulau Jawa
- Halaman cities, 7-day forecast, dan comparison
- Refresh cache cuaca

Routes:
- `/weather`
- `/weather/cities`
- `/weather/forecast`
- `/weather/comparison`
- `/weather/refresh`

### 2) Student Dataset Dashboard
- Visualisasi dari CSV `storage/app/datasets/ultimate_student_productivity_dataset_5000.csv`
- Filter berdasarkan academic level, gender, internet quality
- Upload CSV baru langsung dari UI
- Refresh cache dataset
- Endpoint JSON untuk konsumsi seperti API

Routes:
- `/student-productivity`
- `/student-productivity/upload` (POST)
- `/student-productivity/refresh` (POST)
- `/student-productivity/api`

## Instalasi

```bash
git clone https://github.com/IsmetMaulanaAzhari/Data_Visualization.git
cd Data_Visualization
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Akses aplikasi:
- `http://localhost:8000` (redirect ke weather)

## Struktur Inti

```text
app/
  Http/Controllers/
    StudentProductivityController.php
    WeatherController.php
  Services/
    StudentProductivityService.php
    WeatherService.php

resources/views/
  layouts/app.blade.php
  student-productivity/dashboard.blade.php
  weather/
    dashboard.blade.php
    cities.blade.php
    forecast.blade.php
    comparison.blade.php
```

## Catatan

- Dataset CSV disimpan di disk `local` (`storage/app/...`) sehingga tidak bisa diakses langsung dari URL publik.
- Bila ingin mengganti dataset, gunakan form upload di halaman Student Dataset.

## License

MIT
