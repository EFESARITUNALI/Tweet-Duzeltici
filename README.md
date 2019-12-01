# Tweet Düzeltici

## Tweet Düzeltici Nedir?

Tweet Düzeltici, sosyal medyada sık sık kullanılan yabancı kökenli kelimelere karşı Türkçe kelimeler kullanmaları için  kullanıcıları uyaran bir bottur. Girdi olarak kontrol edilmek istenen hesabın adını ve  kaç tweetin kontrol edileceğini alır. Son paylaşılan tweetlerden istenilen sayı kadarını inceler ve kontrol edilen hesabı etiketleyerek çıktısını tweetler. Cümlede Türkçe karşılığı olan yabancı kökenli bir sözcük varsa *"@... göderinde, ... kelimesi yerine ... kullanımını tercih ederek yazımındaki Türkçeyi zenginleştirebilirsin."*, formatında bir paylaşım yapar. Aksi takdirde *"@... göderinde gözüme çarpan bir Türkçe yazım tavsiyesi görünmüyor, oldukça güzel."*, şeklinde bir teşekkür mesajı paylaşır.

## Kaynaklar
Stemmer olarak [Zargan Türkçe Kelime Veritabanı](http://st2.zargan.com/duyuru/Zargan_Turkce_Dilbilimsel_Veritabani.html)'nı kullandık. Yabancı kelimelerin Türkçe karşılıklarını bulmada Türk Dil Kurmu'nun Yabancı Sözlere Karşılıklar Kılavuzu'ndan faydalandık. Twitter'dan tweetleri almak için [Twitter'ın api](https://github.com/J7mbo/twitter-api-php)'ını  kullandık.

## Nasıl Çalışıyor?

Tweet Düzeltici, Twitter'dan aldığı tweetleri kelimelerden oluşan dizilere çevirip her kelimenin kökünü bulur. Eğer kelime yerine Türkçe'si konulabilecek yabancı kökenli bir kelimeyse o kelimeyi düzeltilecek kelimelerin arasına ekler ve düzelti mesajında paylaşır.

## Nasıl Kullanılır?
[Bu adrese] girip test etmek istediğiniz hesabın kullanıcı adını girerek başlayın. Kaç tweet görmek istediğinizi seçtikten sonra Tweetleri İncelet butonuna basın. Kullanıcının tweetleri karşınıza gelecek. Onu yalnızca hatalı kullanımları için uyarabilir veya tüm tweetlerine cevap vererek hatasız tweetler için de tebrik edebilirsiniz.

## Biz Kimiz?

Tweet Düzeltici, **Galatasaray Üniversitesi IEEE Klübü Takımı** tarafından, Türkçe dil işleme temalı AçıkHack Hackathonu kapsamında geliştirilmiştir.
