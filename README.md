# Müşteri Destek & SEO Raporlama Paneli

Framework kullanılmadan, düz PHP ile yazılmış küçük bir yönetim paneli. Panelde üç bölüm var: giriş yapan kullanıcıların destek talebi açıp durumunu güncelleyebildiği bir talep modülü, Lighthouse JSON raporlarını içe aktaran ve URL'ler için SEO analizi yapan bir raporlama modülü, ve bu verileri Chart.js grafiğiyle özetleyen bir dashboard. Veriler MySQL'de, rapor dosyaları `storage/reports/` altında JSON olarak tutuluyor. Arayüz Türkçe.

## Özellikler

- **Kimlik doğrulama**: E-posta ve şifreyle kayıt / giriş (`password_hash` ile BCRYPT). Şifre en az 6 karakter olmalı. Bütün POST formlarında oturuma bağlı bir CSRF token'ı kontrol ediliyor (`_csrf` alanı veya `X-CSRF` başlığı).
- **Destek talepleri** (`/tickets`): Konu ve açıklamayla yeni talep açma, tüm talepleri listeleme, durum güncelleme (`new`, `in_progress`, `resolved`).
- **SEO raporları** (`/reports`):
  - **Manuel yükleme**: Lighthouse JSON dosyası yükleniyor; `categories.seo`, `performance` ve `accessibility` skorları okunuyor.
  - **URL analizi**: Tek bir URL için Lighthouse (PageSpeed Insights v5) ve sayfa HTML'i üzerinden kendi SEO denetimi birlikte çalıştırılıyor.
  - **Toplu analiz**: En fazla 10 URL, her biri arasında 2 saniye bekleme.
  - **Demo raporlar** (`/reports/demo`): Üç örnek rapor oluşturuyor.
  - **Rapor detayı**: Modal pencerede kayıtlı JSON içeriği gösteriliyor.
- **SEO denetimi** (`app/Services/SeoAnalyzer.php`): `title` (var mı, uzunluğu), meta description, H1/başlık yapısı, görsellerde `alt` metni, boş bağlantılar, `viewport`, `charset` ve `<html lang>` kontrolleri. Skor 100'den başlıyor; her sorun için puan düşülüyor (critical −20, error −15, warning −10, info −5).
- **Dashboard** (`/`): Toplam ve açık talep sayısı, bugün çözülen talepler, son 7 günde açılan talepler, rapor sayısı, son 30 gündeki rapor sayısı, ortalama SEO skoru, son raporun hız skoru ve son 10 raporun Hız / SEO / Erişilebilirlik grafiği.
- Açık/koyu tema geçişi ve mobil menü (`public/js/app.js`, `public/css/styles.css`).

## Teknoloji

- PHP (typed property ve arrow function kullanıldığı için 7.4 veya üzeri), PDO
- MySQL (`utf8mb4`)
- Chart.js (jsDelivr CDN üzerinden)
- Vanilla JavaScript ve CSS
- Google PageSpeed Insights API v5 (opsiyonel; aşağıya bakın)

## Proje yapısı

```
app/
├── Auth.php                    # Oturum, CSRF, giriş/kayıt
├── Db.php                      # PDO sarmalayıcı
├── Controllers/
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── TicketController.php
│   └── ReportController.php
├── Services/
│   ├── LighthouseService.php   # PageSpeed API çağrısı / demo rapor üretimi, JSON kaydetme
│   └── SeoAnalyzer.php         # HTML tabanlı SEO denetimi
└── Views/                      # layout, giriş, talepler, raporlar şablonları
public/
├── index.php                   # Front controller ve router
├── css/styles.css
└── js/app.js
storage/reports/                # Yüklenen / üretilen JSON raporlar
schema.sql                      # users, tickets, seo_reports tabloları
```

## Gereksinimler

- PHP 7.4+ ve `pdo_mysql`, `dom` eklentileri
- URL analizi için `allow_url_fopen` açık olmalı (sayfalar `file_get_contents` ile çekiliyor)
- MySQL veya MariaDB
- `storage/reports/` klasörüne web sunucusunun yazabilmesi

## Kurulum

1. MySQL'de `mds` adında bir veritabanı oluşturun ve `schema.sql` dosyasını içe aktarın:
   ```bash
   mysql -u root -e "CREATE DATABASE mds CHARACTER SET utf8mb4"
   mysql -u root mds < schema.sql
   ```
2. Veritabanı bağlantı bilgilerini `public/index.php` içindeki `$config` dizisinden düzenleyin. Varsayılan: `mysql:host=localhost;dbname=mds;charset=utf8mb4`, kullanıcı `root`, şifre boş.
3. Web sunucusunun kök dizinini `public/` olarak ayarlayın. Tüm istekler `public/index.php` üzerinden yönlendiriliyor, bu yüzden var olmayan yollar bu dosyaya düşmeli. Depoda `.htaccess` yok. Geliştirme için PHP'nin dahili sunucusu yeterli:
   ```bash
   php -S localhost:8000 -t public
   ```
4. `/login` sayfasından bir hesap oluşturup giriş yapın.
5. `/reports` ekranından örnek bir JSON yükleyin (örneğin `storage/reports/sample_lighthouse.json`) ya da "Demo Raporlar Oluştur" butonunu kullanın.

## Rotalar

| Yol | Metot | Açıklama |
|---|---|---|
| `/login`, `/register` | GET / POST | Giriş ve kayıt |
| `/logout` | POST | Çıkış |
| `/` | GET | Dashboard |
| `/tickets` | GET | Talep listesi ve formu |
| `/tickets/store`, `/tickets/update` | POST | Talep ekleme / durum güncelleme |
| `/reports` | GET | Rapor ekranı |
| `/reports/upload` | POST | Lighthouse JSON yükleme (`json` dosya alanı, `label`) |
| `/reports/analyze-url` | POST | Tek URL analizi (JSON yanıt) |
| `/reports/analyze-bulk` | POST | Toplu analiz, `urls` alanında satır başına bir URL (JSON yanıt) |
| `/reports/demo` | GET | Demo rapor üretimi |
| `/reports/api/detail?id=` | GET | Rapor kaydı + JSON içeriği |
| `/api/reports/last10` | GET | Grafik için son 10 rapor |

## Önemli: Lighthouse skorları şu an simüle ediliyor

`LighthouseService` bir API anahtarı alabiliyor, ama `ReportController` servisi anahtarsız oluşturuyor (`new LighthouseService()`). Anahtar boş kalınca yer tutucu bir değer (`AIzaSyDemoKeyForProject123`) atanıyor ve servis gerçek API'yi çağırmak yerine URL'in `crc32` hash'inden türetilen sahte bir rapor döndürüyor. Bu yüzden URL ve toplu analizdeki performans, SEO ve erişilebilirlik skorları gerçek ölçüm değil. Gerçek PageSpeed sonuçları için `ReportController` yapıcısında geçerli bir Google API anahtarı verilmeli:

```php
$this->lighthouse = new LighthouseService('GERCEK_API_ANAHTARI');
```

Benzer şekilde `SeoAnalyzer` adresinde `demo` veya `test` geçen URL'lerde ve sayfa çekilemediğinde, hata vermek yerine üretilmiş örnek bir HTML'i analiz ediyor.

## Bilinen eksikler

- Menüdeki `/profile` ve `/settings` bağlantıları için rota tanımlı değil; bu sayfalar 404 döndürüyor.
- `/reports/api/detail` ve `/api/reports/last10` uçları oturum kontrolü yapmıyor.
- Yüklenen JSON dosyalarının içeriği doğrulanmıyor; dosya doğrudan `storage/reports/` altına kaydediliyor.
- Depoda lisans dosyası yok; lisans belirtilmemiş.
