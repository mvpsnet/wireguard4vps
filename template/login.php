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

<body class="h-full antialiased">

<div class="min-h-full flex flex-col">
    <div class="bg-gray-800 pb-28">

        <?php require "template/parts/navbar.php" ?>

        <header class="max-w-2xl mx-auto px-4 md:px-8 py-10 flex flex-wrap items-center">
            <div class="mr-auto mb-2">
                <h1 class="text-3xl italic text-white">
                    Log in
                </h1>
            </div>
        </header>

    </div>

    <main class="-mt-28">
        <div class="max-w-2xl mx-auto pb-12 px-4">
            <div class="bg-white p-4 shadow rounded-lg flex justify-center flex-wrap px-4 md:px-16">
                <form action="login.php" method="post" class="w-full sm:w-5/7">
                    <div>
                        <div>
                            <h3 class="text-lg text-center leading-6 font-medium text-gray-900 items-baseline" id="modal-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-flex text-gray-500" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span>Authenticate</span>
                            </h3>
                            <hr class="mt-4 mb-6">
                            <div class="mt-2">
                                <div class="md:flex md:items-center mb-6">
                                    <div class="md:w-1/3">
                                        <label class="block text-gray-600 md:text-right mb-1 md:mb-0 pr-4" for="username">
                                            Username
                                        </label>
                                    </div>
                                    <div class="md:w-2/3">
                                        <input id="username" name="user" type="text" value="" autocomplete="username" placeholder="Username" autofocus
                                               class="bg-gray-50 appearance-none border border-gray-300 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                                    </div>
                                </div>

                                <div class="md:flex md:items-center mb-6">
                                    <div class="md:w-1/3">
                                        <label class="block text-gray-600 md:text-right mb-1 md:mb-0 pr-4" for="password">
                                            Password
                                        </label>
                                    </div>
                                    <div class="md:w-2/3">
                                        <input id="password" name="pass" type="password" value="" autocomplete="current-password" placeholder="Password"
                                               class="bg-gray-50 appearance-none border border-gray-300 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                                    </div>
                                </div>

                                <div class="md:flex md:items-center mb-6">
                                    <div class="md:w-1/3">
                                        <label class="block text-gray-600 md:text-right mb-1 md:mb-0 pr-4" for="tfa">
                                            <span class="whitespace-nowrap">One-time password</span> <small class="block">(2FA) - Optional</small>
                                        </label>
                                    </div>
                                    <div class="md:w-2/3">
                                        <input id="tfa" name="tfa" type="text" value="" autocomplete="off" placeholder="Ex: OTP from Google Authenticator"
                                               class="bg-gray-50 appearance-none border border-gray-300 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Log in
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </main>

    <?php require "template/parts/footer.php" ?>
</div>

<script type="text/javascript" src="assets/js/main.js" defer></script>
</body>
</html>
