<?php
if (count(get_included_files()) == 1) {
    die("Direct access not allowed");
}
?>
<div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-show="twoFactorAuthModal" style="display: none">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="twoFactorAuthModal = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>


        <form x-show="twoFactorAuthModal" x-transition action="index.php?q=2fa" method="post"
              class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl md:max-w-2xl sm:w-full sm:p-6">
            <div>
                <div>
                    <h3 class="text-lg text-center leading-6 font-medium text-gray-900 items-baseline" id="modal-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-flex text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Two factor authentication</span>
                    </h3>
                    <hr class="mt-4 mb-6">

                    <div class="mt-2 flex gap-4">
                        <div class="w-[250px]">
                            <canvas id="2fa_qr" class="qrcodes" data-value="otpauth://totp/wireguard?secret=<?=$_config['tfa_key']; ?>"></canvas>

                        </div>
                        <div class="text-sm flex-shrink">
                            <h5 class="font-semibold text-xl">How to activate 2FA</h5>
                            <p>
                                <strong>Copy and write down the Auth Key</strong>, as it will be the
                                only way to access your
                                account if you lose your phone!</p>
                            <ul class="list-disc list-inside mt-4">
                                <li>Download
                                    <strong>Authenticator</strong> app for
                                    <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&amp;hl=en" target="_blank"
                                       rel="noopener nofollow" class="text-indigo-500 hover:text-indigo-700">Android</a> or
                                    <a href="https://itunes.apple.com/gb/app/google-authenticator/id388497605?mt=8" target="_blank" rel="noopener nofollow"
                                       class="text-indigo-500 hover:text-indigo-700">iOS</a>
                                </li>
                                <li>Scan the QR Code</li>
                                <li>Enter the generated one-time code</li>
                            </ul>
                        </div>
                    </div>

                    <hr class="mt-4 mb-6">

                    <div class="mt-2">
                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block text-gray-600 md:text-right mb-1 md:mb-0 pr-4" for="tfa_key">
                                    Your Auth Key:
                                </label>
                            </div>
                            <div class="md:w-2/3">
                                <input id="tfa_key" name="tfa_key" type="text" value="<?=$_config['tfa_key']; ?>" readonly
                                       class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:border-purple-500">
                            </div>
                        </div>

                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block text-gray-600 md:text-right mb-1 md:mb-0 pr-4" for="tfa">
                                    One-time 2FA Code
                                </label>
                            </div>
                            <div class="md:w-2/3">
                                <input id="tfa" name="tfa" type="text" value="" autocomplete="off"
                                       class="bg-gray-50 appearance-none border border-gray-300 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <button type="submit"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                    Activate 2FA
                </button>
                <button type="button"
                        @click="twoFactorAuthModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
