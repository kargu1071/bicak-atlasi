# Bıçak Atlası yayın kurulumu

Kaynak `index.html` dosyasıdır. Site bir derleme veya API gerektirmez.

## Firebase Hosting

Firebase Console’da proje oluşturup Hosting’i etkinleştir. Bu dalı yerel olarak aç, Firebase CLI ile oturum aç ve `firebase deploy --only hosting --project PROJE_ID` çalıştır. Ardından Hosting ekranında gösterilen URL’yi aç.

## Azure Static Web Apps

Azure portalında bir Static Web App oluştur. Kaynak olarak `kargu1071/bicak-atlasi` deposunu ve `hosting-setup` dalını seç. `Custom` derleme ön ayarıyla uygulama konumunu `/` olarak gir. API ve çıktı konumlarını boş bırak. Azure’un oluşturduğu iş akışında gerekirse `skip_app_build: true` ayarını kullan. Portalın verdiği URL’den yayını doğrula.

## DigitalOcean App Platform

Hesabında App Platform erişimiyle bu daldaki `.do/app.yaml` dosyasını kullan. App Spec, `index.html` dosyasını `dist/` dizinine kopyalayıp statik bileşen olarak yayınlar. App Platform üzerinden URL’yi doğrula.

## Codeberg Pages

Codeberg’de herkese açık bir `bicak-atlasi` deposu oluştur ve `pages` dalını hazırla. Depo ayarlarında git-pages webhook’u ekle ve dal filtresini `pages` olarak seç. Bu deponun `index.html` dosyasını o dala yükle. Webhook ekranının gösterdiği proje URL’sinden yayını doğrula.
