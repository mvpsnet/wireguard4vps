<?php
if (count(get_included_files()) == 1) {
    die("Direct access not allowed");
}
?>
<nav class="bg-gray-800 mb-4">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="border-b border-gray-700">
            <div class="flex items-center justify-between flex-wrap px-4 sm:px-0 py-2">
                <a class="flex items-center my-2 text-blue-200 hover:text-blue-500 italic" href="index.php">
                    <?php
                    $logoDotColor = "#BCBAC0";
                    if ($_SESSION['logged']===1) {
                        $logoDotColor = "#31c48d";
                    }
                    ?>
                    <svg width="31" height="34" viewBox="0 0 31 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M26 12.5V14.93C26 25.93 16 30.54 14 31.36C12 30.54 2 25.92 2 14.93V8.14C4.10947 7.53356 6.17441 6.78177 8.18 5.89C10.1857 5.02299 12.1302 4.02068 14 2.89C15.4575 3.7794 16.9631 4.58731 18.51 5.31V5.02C18.511 4.40285 18.5882 3.7882 18.74 3.19C17.2997 2.49451 15.9007 1.71655 14.55 0.86L14 0.5L13.46 0.85C11.5182 2.07348 9.48556 3.14661 7.38 4.06C5.23629 5.01644 3.01915 5.79896 0.75 6.4L0 6.59V14.93C0 28.32 13.53 33.33 13.66 33.38L14 33.5L14.34 33.38C14.48 33.38 28 28.33 28 14.93V12.22C27.349 12.4032 26.6762 12.4974 26 12.5V12.5Z"
                              fill="url(#paint0_linear_2_6)"/>


                        <!-- #31c48d - logged in -->
                        <!-- #BCBAC0 - not logged in -->
                        <path d="M26 10C28.7614 10 31 7.76142 31 5C31 2.23858 28.7614 0 26 0C23.2386 0 21 2.23858 21 5C21 7.76142 23.2386 10 26 10Z"
                              fill="<?= $logoDotColor ?>"
                        />
                        <defs>
                            <linearGradient id="paint0_linear_2_6" x1="14" y1="0.5" x2="14" y2="33.5" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#6366F1"/>
                                <stop offset="1" stop-color="#2C2FCA"/>
                            </linearGradient>
                        </defs>
                    </svg>

                    <span class="ml-2">WireGuard4VPS</span>
                </a>

                <div class="flex my-2">
                    <?php if ($_SESSION['logged']===1): ?>
                        <div x-data="{dropdownMenu: false}" @click.outside="dropdownMenu = false" @keydown.escape="dropdownMenu = false" class="relative">
                            <button type="button"
                                    @click="dropdownMenu = ! dropdownMenu"
                                    class="max-w-xs bg-gray-800 rounded-full flex items-center text-sm focus:outline-none focus:ring-1 focus:ring-offset-8 focus:ring-offset-gray-800 focus:ring-gray-500"
                                    aria-expanded="false" aria-haspopup="true">
                                    <span class="text-sm font-medium leading-none text-gray-400">
                                        Logged in as <strong class="text-gray-300">admin</strong>
                                    </span>
                            </button>

                            <div x-show="dropdownMenu" x-transition x-cloak
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                 role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" :tabindex="dropdownMenu ? 0 : -1">

                                <button type="button" @click.prevent="changePasswordModal = true"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:text-blue-600" role="menuitem" :tabindex="dropdownMenu ? 0 : -1">
                                    Change
                                    password
                                </button>

                                <button type="button" @click.prevent="twoFactorAuthModal = true"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:text-blue-600" role="menuitem" :tabindex="dropdownMenu ? 0 : -1">Two
                                    factor auth.
                                </button>

                                <form action="login.php" method="post">
                                    <input type="hidden" name="logout" value="123">
                                    <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:text-blue-600" role="menuitem"
                                            :tabindex="dropdownMenu ? 0 : -1">
                                        Log out
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</nav>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <?php

        require "template/parts/alert.php";

    ?>
</div>
