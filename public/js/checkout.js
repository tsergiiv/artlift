// Create a Checkout Session with the selected amount
var createCheckoutSession = function (stripe) {
    var amoutInput = document.getElementById("amount-input");
    var amount = parseFloat(amoutInput.value) * 100;

    var subInput = document.getElementById("sub-input");
    var sub_id = parseInt(subInput.value);

    var userInput = document.getElementById("user-input");
    var user_id = parseInt(userInput.value);

    return fetch("../api/sub_stripe_create_session", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            amount:  amount,
            sub_id:  sub_id,
            user_id: user_id
        }),
    }).then(function (result) {
        return result.json();
    });
};

var createCheckoutSessionShot = function (stripe) {
    var amoutInput = document.getElementById("amount-input");
    var amount = parseFloat(amoutInput.value) * 100;

    var subLikes = document.getElementById("sub-likes");
    var likes = parseInt(subLikes.value);

    var subComments = document.getElementById("sub-comments");
    var comments = parseInt(subComments.value);

    var elShot = document.getElementById("shot-id");
    var shot = parseInt(elShot.value);

    var elUser = document.getElementById("user-id");
    var user_id = parseInt(elUser.value);

    return fetch("../api/shot_stripe_create_session", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            amount:  amount,
            likes:  likes,
            comments: comments,
            shot: shot,
            user_id: user_id,
        }),
    }).then(function (result) {
        return result.json();
    });
};


// Handle any errors returned from Checkout
var handleResult = function (result) {
    if (result.error) {
        var displayError = document.getElementById("error-message");
        displayError.textContent = result.error.message;
    }
};

/* Method for getting query string parameter by name */
var getParameterByName = function (name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}

/* Method for getting the "amt" variable from the query string in case it is provided */
var getAmountFromQueryString = function() {
    var amt = getParameterByName("amt");
    if (amt) {
        var inputEl = document.getElementById("amount-input");
        inputEl.value = amt;
    }
}

/* Get your Stripe publishable key to initialize Stripe.js */
fetch("../api/config")
    .then(function (result) {
        return result.json();
    })
    .then(function (json) {
        window.config = json;
        var stripe = Stripe(config.publicKey);
        getAmountFromQueryString();
        // Setup event handler to create a Checkout Session on submit
        if (document.querySelector("#submit")) document.querySelector("#submit").addEventListener("click", function (evt) {
            createCheckoutSession().then(function (data) {
                stripe
                    .redirectToCheckout({
                        sessionId: data.sessionId,
                    })
                    .then(handleResult);
            });
        });
        if (document.querySelector("#shot-submit")) document.querySelector("#shot-submit").addEventListener("click", function (evt) {
            createCheckoutSessionShot().then(function (data) {
                stripe
                    .redirectToCheckout({
                        sessionId: data.sessionId,
                    })
                    .then(handleResult);
            });
        });


    });
