<?php
if (count(get_included_files()) == 1) {
    die("Direct access not allowed");
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="theme-color" content="#000000"/>

    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon-16x16.png">
    <link rel="manifest" href="/assets/img/site.webmanifest">
    <link rel="mask-icon" href="/assets/img/safari-pinned-tab.svg" color="#272860">
    <link rel="shortcut icon" href="/assets/img/favicon.ico">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="msapplication-config" content="/assets/img/browserconfig.xml">
    <meta name="theme-color" content="#272860">

    <title>WireGuard 4 VPS</title>
    <link type="text/css" href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/inter/inter.css">
</head>

<body class="h-full antialiased" x-data="{changePasswordModal: false, twoFactorAuthModal: false}"
      @keydown.escape="changePasswordModal= false; twoFactorAuthModal= false">

<div class="min-h-full flex flex-col">
    <div class="bg-gray-800 pb-28">

        <?php include "parts/navbar.php" ?>

        <?php include "parts/setup-info.php" ?>

    </div>

    <main class="-mt-28">
        <dl class="max-w-4xl mx-auto pb-12 px-4 sm:px-6 lg:px-8">
            <?php
            foreach ($profiles as $profile) {
                include "parts/connection.php";
            }
            ?>
        </dl>
    </main>

    <?php include "parts/footer.php" ?>
</div>

<?php include "parts/modal-change-password.php"; ?>
<?php
if($_config['twofa']=="0"){
    include "parts/two-factor-auth-modal.php";
}else {
    include "parts/disable-two-factor-auth-modal.php";
}

?>

<script type="text/javascript" src="assets/js/main.js" defer></script>
<script type="text/javascript" src="assets/js/qrious.js" defer></script>

<script>
    document.addEventListener('readystatechange', event => {
        if (event.target.readyState === "complete") {
            var cusid_ele = document.getElementsByClassName('qrcodes');
            for (var i = 0; i < cusid_ele.length; ++i) {
                var item = cusid_ele[i];

                var qr = new QRious({
                    element: item,
                    value: item.dataset.value,
                    size: 158,
                });
            }
        }
    });
</script>


</body>
</html>
