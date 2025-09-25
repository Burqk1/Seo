# Müşteri Destek & SEO Raporlama Paneli
- Auth (password_hash, CSRF), Destek Talepleri, SEO Rapor (Lighthouse JSON), Dashboard (Chart.js)
## Kurulum
1) MySQL'de `mds` DB oluştur, `schema.sql` içe aktar.
2) Web root'u `public/` yap. `app/Db.php` DSN'i ihtiyacına göre güncelle.
3) /login üzerinden hesap oluştur.
4) /reports ekranından örnek JSON yükle.
