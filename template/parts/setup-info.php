<?php
if (count(get_included_files()) == 1) {
    die("Direct access not allowed");
}
?>
<header class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-wrap items-center">
    <div class="mr-auto mb-2">
        <h1 class="text-3xl italic text-white mb-4">
            VPN clients
        </h1>
        <form action="/index.php?q=add" method="post" x-data="{showForm: false}">
        <?php csrf_token(); ?>
            <div class="mb-4" x-show="showForm" x-transition>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <label for="profile_name"
                           class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-100 text-gray-800 sm:text-sm">
                        Profile name:
                    </label>
                    <input type="text" name="profile_name" id="profile_name" autocomplete="off" class="input" placeholder="Ex: My desktop device" required>
                </div>
            </div>

            <div class="flex justify-between">
                <button type="submit" x-show="showForm" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Submit
                </button>

                <button type="button" @click="showForm = !showForm" class="btn" :class="{'bg-transparent': showForm, 'btn-primary': !showForm}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="!showForm">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="showForm ? 'Cancel' : 'New profile'"></span>
                </button>
            </div>
        </form>
    </div>
    <div class="text-gray-200 mb-2">
        <h4 class="text-gray-400 font-bold text-xl">Setup:</h4>
        <ol class="list-decimal list-inside">
            <li>Install <a href="https://www.wireguard.com/install/" target="_blank" rel="noopener" class="text-blue-300 hover:text-blue-400 underline">WireGuard</a>
            </li>
            <li>Download your WireGuard config</li>
            <li>Connect to the VPN</li>
        </ol>
    </div>
</header>
