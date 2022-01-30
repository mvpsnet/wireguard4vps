<?php
if (count(get_included_files()) == 1) {
    die("Direct access not allowed");
}
?>
<div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-show="changePasswordModal" style="display: none">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="changePasswordModal = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>


        <form x-show="changePasswordModal" x-transition action="index.php?q=password" method="post"
              class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full sm:p-6">
            <div>
                <div class="text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 items-baseline" id="modal-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-flex text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Change password</span>
                    </h3>
                    <hr class="mt-4 mb-6">
                    <div class="mt-2">
                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block text-gray-600 md:text-right mb-1 md:mb-0 pr-4" for="old_password">
                                    Old password
                                </label>
                            </div>
                            <div class="md:w-2/3">
                                <input id="old_password" name="old_password" type="password" value="" autocomplete="current-password" placeholder="Old password"
                                       class="bg-gray-50 appearance-none border border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                            </div>
                        </div>

                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block text-gray-600 md:text-right mb-1 md:mb-0 pr-4" for="new_password">
                                    New password
                                </label>
                            </div>
                            <div class="md:w-2/3">
                                <input id="new_password" name="new_password" type="password" value="" autocomplete="new-password" placeholder="Min 8 characters"
                                       class="bg-gray-50 appearance-none border border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                            </div>
                        </div>

                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block text-gray-600 md:text-right mb-1 md:mb-0 pr-4" for="repeat_password">
                                    Repeat password
                                </label>
                            </div>
                            <div class="md:w-2/3">
                                <input id="repeat_password" name="repeat_password" type="password" value="" autocomplete="new-password" placeholder="Same as above"
                                       class="bg-gray-50 appearance-none border border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <button type="submit"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                    Submit
                </button>
                <button type="button"
                        @click="changePasswordModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
