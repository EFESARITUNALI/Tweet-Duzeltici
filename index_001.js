function sendTweetAll () {
    let loading = document.querySelector(".loading");
    let user_name = document.querySelector("input.user-name").value;
    let tweet_count = document.querySelector("select.tweet-count").value;
    let send_tweet = true;
    let only_negative_tweet = true;
    loading.classList.add("is-active");
    let sorgu = "user_name=" + user_name + "&tweet_count=" + tweet_count + "&tweet_at=" + send_tweet + "&only_negative_tweet=" + only_negative_tweet;
    fetch('test.php?' + sorgu)
    .then(response => response.json())
    .then(data => {
        console.log(data);
        loading.classList.remove("is-active");
    })
    .catch(err => {
        console.error('An error ocurred', err);
        loading.classList.remove("is-active");
    });
}

function sendTweetNegative () {
    let user_name = document.querySelector("input.user-name").value;
    let tweet_count = document.querySelector("select.tweet-count").value;
    let send_tweet = true;
    let only_negative_tweet = true;

    let sorgu = "user_name=" + user_name + "&tweet_count=" + tweet_count + "&tweet_at=" + send_tweet + "&only_negative_tweet=" + only_negative_tweet;
    fetch('test.php?' + sorgu)
    .then(response => response.json())
    .then(data => {
        console.log(data);
    })
    .catch(err => {
        console.error('An error ocurred', err);
    });
}


function search_user () {
    let loading = document.querySelector(".loading");
    let user_name = document.querySelector("input.user-name").value;
    let tweet_count = document.querySelector("select.tweet-count").value;
    let send_tweet = false;
    let only_negative_tweet = true;
    loading.classList.add("is-active");
    let sorgu = "user_name=" + user_name + "&tweet_count=" + tweet_count + "&tweet_at=" + send_tweet + "&only_negative_tweet=" + only_negative_tweet;
    fetch('test.php?' + sorgu)
    .then(response => response.json())
    .then(data => {
        loading.classList.remove("is-active");
        console.log(data);
        data.forEach(element => {
            putCard(element);
        });
    })
    .catch(err => {
        console.error('An error ocurred', err);
        loading.classList.remove("is-active");
    });
}

function putCard (data) {
    let main_div = document.querySelector("div.tweet-cards");

    let card = document.createElement("div");
    card.classList.add("card");
    main_div.append(card);

    let card_content = document.createElement("div");
    card_content.classList.add("card-content");
    card.append(card_content);

    let tweet_p = document.createElement("p");
    tweet_p.classList.add("subtitle");
    tweet_p.innerHTML = data.tweet;
    card_content.append(tweet_p);

    let respond_p = document.createElement("p");
    respond_p.classList.add("subtitle");
    respond_p.innerHTML = data.respond;
    card_content.append(respond_p);

    let card_footer = document.createElement("div");
    card_footer.classList.add("card-footer");
    card.append(card_footer);

    let card_footer_item = document.createElement("p");
    card_footer_item.classList.add("card-footer-item");
    
    let card_footer_item_span = document.createElement("span");
    card_footer_item_span.innerHTML = "Tweet ile onu uyar";
    card_footer_item.append(card_footer_item_span);
    card_footer.append(card_footer_item);
    let br = document.createElement("br");
    main_div.append(br);
}