<style>

#first_login h2 {
    font-size:2.5em;
    padding:0 50px;
    margin-bottom:10px;
    text-align:center;
}

#first_login h3 {
    text-align:center;
    color:gray;
    margin-bottom:10px;
}

#first_login div.first_login_container {
    background: #ececec;
    padding: 15px;
    text-align:center;
    margin:0 15px;
    font-size:1.5em;
    margin-bottom:10px;
}

#first_login p.info {
    width:450px;
    margin: 0 auto;
    text-align:center;
    margin-bottom:20px;
    font-size:.9em;
    color:gray;
}

#first_login input[type=submit] {
    margin: 0 auto;
    display:block;
    font-size:1.4em;
    margin-bottom:10px;
}

</style>
<div id="first_login">
    <h2>Thanks for signing up!</h2>
    <h3>Your account has been set up and you are now signed in.</h3>
    <div class="first_login_container">

        <p>You can always access your account at:</p>
    <a href="http://app.thepapertiger.com/login">app.thepapertiger.com</a>

    </div>
    <p class="info">We've sent you an email with this information.</p>

    <input type="submit" value="OK, let's get started!" onClick="jQuery(document).trigger('close.facebox')" >
</div>
