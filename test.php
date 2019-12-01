<?php

//botumuzu çalıştırmak için gerekli parametereleri giriyoruz

header('Content-type: application/json');

$user_name = $_GET["user_name"]; //kullanıcı adı

$tweet_count = $_GET["tweet_count"]; //alınacak tweet sayısı

$tweet_at = $_GET["tweet_at"];  //tavsiyeler oluştuktan sonra tweer atıp atılmayacağı

$only_negative_tweet = $_GET["only_negative_tweet"]; //olumlu geri dönüşlerin tweet atılıp atılmayacağı



//Türkçe stemmer ve sözlük veri tabanımzın olduğu mysql sunucuna bağlanıyoruz

$con=mysqli_connect("localhost",YOUR_HOST_INFO,YOUR_HOST_INFO,YOUR_HOST_INFO);

// Bağlantı kontrol

if (mysqli_connect_errno()) {

  //echo "MySQL bağlantısı başarısız oldu: " . mysqli_connect_error();

}



//Twitter api kütüphanemizi içeri aktarıyoruz

require_once('twitter-api-php-master/TwitterAPIExchange.php');

//twitter api ayarlarını yapıyoruz

$settings = array(

    'oauth_access_token' => YOUR_TOKENS,

    'oauth_access_token_secret' => YOUR_TOKENS,

    'consumer_key' => YOUR_TOKENS,

    'consumer_secret' => YOUR_TOKENS

);



//Tweetleri almak için apimizi kullanıyoruz

$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

$getfield = '?tweet_mode=extended&trim_user=false&count='.$tweet_count.'&screen_name=' . $user_name;

$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);

$tweetler_json = $twitter->setGetfield($getfield)

             ->buildOauth($url, $requestMethod)

             ->performRequest();     



//json formatında gelen büyük veriyi diziye parçalıyoruz

$tweetler = json_decode($tweetler_json, true);



//aldığımız metinler için bir array yaratıyoruz

$metinler = [];

//ileride kullanacağımız bir değişkeni tanımlıyoruz

$negative_res = false;

$json_array = [];

//metinler arayına jsondan tweet metinlerini ekliyoruz

foreach ($tweetler as $key => $value) {

    array_push($metinler, $value[full_text]);

}

//ilk boş veriyi dışarı çıkarıyoruz

array_shift($metinler);



//her metni tek tek inceliyoruz

foreach ($metinler as $key => $value) {

    //metnimizin ilk halini bir değişkende tutuyoruz

    $first_text = $value;

    

    //Metnimizde ayrıştıracağımız kelimeler için diziler oluşturuyoruz

    $karsilikli_kelimeler = []; 

    $hatali_kelimeler = [];

    $dogru_kelimeler = [];

    

    //metnimizi kelimelere ayırıyoruz

    $dizi = explode(" ", $value);

    

    //RT ile başlayan tweetler kişinin gönderileri olmadığından bunları atlıyoruz

    if($dizi[0] == "RT") {

        continue;

    }

    

    //Her kelime için kontroller gerçekleştiriyoruz

    foreach ($dizi as $key => $value) {

        //kelimenin değişimlerden önceki halini bir değişkende saklıyoruz

        $first_word = $value;



        //Hashtag varsa kelimeyi atlıyoruz

        if($value[0] == '@') {

            continue;

        }



        //Link ise kelimeyi atlıyoruz

        $reg_exUrl = "/(http|https|ftp|ftps)/";

        if(preg_match($reg_exUrl, $value)) {

            continue;

        }

        

        //Özel işaretleri kaldırıyoruz

        $value = str_replace(" ", "", $value);

        $value = str_replace(":", "", $value);

        $value = str_replace(".", "", $value);

        $value = str_replace("*", "", $value);

        $value = str_replace("…", "", $value);

        $value = str_replace(",", "", $value);

        $value = str_replace("!", "", $value);

        $value = str_replace("?", "", $value);

        $value = str_replace(")", "", $value);

        $value = str_replace("(", "", $value);

        $value = str_replace("\"", "", $value);

        $value = str_replace("|", "", $value);

        $value = str_replace("'", "*", $value);

        $value = str_replace("”", "", $value);

        $value = str_replace("\"", "", $value);



        //Tüm karakterleri küçük harfe dönüştürüp türkçe karkterleri büyük harfe dönüştürüyoruz

        $value = mb_strtolower($value, 'utf-8');

        $value = str_replace("ç", "C", $value);

        $value = str_replace("ş", "S", $value);

        $value = str_replace("ü", "U", $value);

        $value = str_replace("ı", "I", $value);

        $value = str_replace("ğ", "G", $value);

        $value = str_replace("ö", "O", $value);



        $value = preg_replace('/[0-9]+/', "", $value);



        

        

        $sql = "SELECT root_of_word FROM roots WHERE word = '$value'";

        $result = $con->query($sql);

        if ($result->num_rows > 0) {

            while($row = $result->fetch_assoc()) {

                

                $root = $row["root_of_word"];



                $root = str_replace("C", "ç", $root);

                $root = str_replace("S", "ş", $root);

                $root = str_replace("U", "ü", $root);

                $root = str_replace("I", "i", $root);

                $root = str_replace("G", "ğ", $root);

                $root = str_replace("O", "ö", $root);

                $root = str_replace("*", "'", $root);

                

                $ch = curl_init();



                curl_setopt($ch, CURLOPT_URL, 'https://sozluk.gov.tr/kilavuz?prm=ysk&ara=' . $root);



                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

                $resul = curl_exec($ch);



                curl_close($ch);



                $dec = json_decode($resul);



                if($dec->error) {

                    array_push($dogru_kelimeler, $root);

                } else {

                    $karsilik_k = $dec[0]->kkarsilik;

                    $karsilik_k = str_replace(".", "", $karsilik_k);

                    $karsilik_k = preg_replace('/[0-9]+/', "", $karsilik_k);

                    $karsilik_k_arr = explode(", ", $karsilik_k);

                    if(count($karsilik_k_arr) > 1) {

                        $karsilik_k = $karsilik_k_arr[1];

                        $arr = [];

                        foreach ($karsilik_k_arr as $key => $value) {

                            array_push($arr, $value);

                        }

                        array_push($karsilikli_kelimeler, [$root, $arr]);

                    } else {

                        array_push($karsilikli_kelimeler, [$root, [$karsilik_k]]);

                    }

                }



            }

        } else {

            array_push($hatali_kelimeler, $first_word);

        }



        

    }

    

    $tweet_text = "@" . $user_name . " göderinde";



    foreach ($karsilikli_kelimeler as $key => $text) {



        $tweet_text = $tweet_text . ", " . $text[0] . " kelimesi yerine";



        foreach ($text[1] as $key2 => $text2) {

            if($key2 != 0) {

                $tweet_text = $tweet_text . " veya";

            }

            $tweet_text = $tweet_text . " " . $text2;

        }

        

    }

    //print_r($first_text);

    //print_r("</br>");



    if(count($karsilikli_kelimeler) > 0) {

        $tweet_text = $tweet_text . " kullanımını tercih ederek yazımındaki Türkçeyi zenginleştirebilirsin.";

        $negative_res = true;

    } else {

        $tweet_text = $tweet_text . " gözüme çarpan bir Türkçe yazım tavsiyesi görünmüyor, oldukça güzel.";

        $negative_res = false;

    }

        $tweet_text = strip_tags($tweet_text);

        //print_r($tweet_text);

        //print_r("</br>");

        //print_r(count($karsilikli_kelimeler));

        //print_r("</br></br>");



        array_push($json_array, ["tweet" =>  $first_text, "respond" => $tweet_text]);

    

    if($tweet_at == true) {

        if($negative_res == true || ($only_negative_tweet == false && $negative_res == false )) {

            /** Note: Set the GET field BEFORE calling buildOauth(); **/

            $url = 'https://api.twitter.com/1.1/statuses/update.json';

            $postData = array('status' => $tweet_text);

            $requestMethod = 'POST';

            $twitter = new TwitterAPIExchange($settings);

            $send_respond_result = $twitter->buildOauth($url, $requestMethod)->setPostfields($postData)->performRequest();

        }

    }

}



echo json_encode($json_array);



$con->close();









?>
