<?php
if (count(get_included_files()) == 1) {
    die("Direct access not allowed");
}
?>
<div x-data="{hideQrCode: true}" class="connection-card">

    <div class="flex-grow">
        <div class="flex mb-2 gap-4">
            <div class="bg-gray-100 shadow-md border-2 border-white rounded-md p-1 text-slate-400">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 16 12" fill="none">
                        <path d="M13.5 1C13.6326 1 13.7598 1.05268 13.8536 1.14645C13.9473 1.24021 14 1.36739 14 1.5V9H2V1.5C2 1.36739 2.05268 1.24021 2.14645 1.14645C2.24021 1.05268 2.36739 1 2.5 1H13.5ZM2.5 0C2.10218 0 1.72064 0.158035 1.43934 0.43934C1.15804 0.720644 1 1.10218 1 1.5V10H15V1.5C15 1.10218 14.842 0.720644 14.5607 0.43934C14.2794 0.158035 13.8978 0 13.5 0H2.5ZM0 10.5H16C16 10.8978 15.842 11.2794 15.5607 11.5607C15.2794 11.842 14.8978 12 14.5 12H1.5C1.10218 12 0.720644 11.842 0.43934 11.5607C0.158035 11.2794 0 10.8978 0 10.5H0Z"
                              fill="currentColor"/>
                    </svg>

            </div>

            <div class="flex-grow">
                <form action="/index.php?q=edit&ip=<?=$profile['ip'] ?>" class="flex gap-1 flex-wrap" x-data="{isEditing: false}" method="post">
                    <?php csrf_token(); ?>
                    <input name="name" class="font-medium text-gray-800 px-1 py-1 rounded-md" :class="{'border border-gray-400': isEditing}" value="<?= $profile['name'] ?>"
                           x-bind:readonly="!isEditing"
                    >

                    <div class="connection-btns">
                        <button type="button" class="btn btn-primary btn-xs rounded-full" x-show="!isEditing" @click="isEditing = true">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit name
                        </button>

                        <button value="1" type="submit" name="edit" class="btn btn-primary btn-xs rounded-full" x-show="isEditing">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save
                        </button>

                        <button type="submit" name="delete" x-show="!isEditing" value="1" class="btn btn-xs rounded-full mr-2 ml-auto" onclick="return confirm('Are you sure?');">Delete</button>
                        <button type="button" x-show="isEditing" class="btn btn-xs rounded-full mr-2 ml-auto" @click="isEditing = false">Cancel</button>
                    </div>
                </form>


                <label class="w-full mt-2 text-gray-400 flex gap-1 px-1"><span class="flex-shrink-0">IP:</span>
                    <input type="text"
                           class="text-green-600 break-all flex-grow"
                           readonly
                           value="<?= $profile['ip'] ?> "/></label>
            </div>
        </div>

        <div class="pb-6 items-baseline sm:pb-7">
            <label class="w-full text-sm mt-4 text-gray-400 flex gap-1"><span class="flex-shrink-0">Public key: </span>
                <input type="text"
                       class="text-gray-600 flex-grow mr-2"
                       readonly
                       value="<?= $profile['public_key'] ?> "/></label>
        </div>

        <div class="flex flex-wrap items-center justify-between">
            <a href="index.php?q=download&ip=<?=$profile['ip']; ?>"
               class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download config
            </a>

            <button type="button" @click="hideQrCode = !hideQrCode"
                    class="btn btn-xs sm:hidden my-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <template x-if="hideQrCode"><span>Show QR Code</span></template>
                <template x-if="!hideQrCode"><span>Hide QR Code</span></template>
            </button>
        </div>
    </div>

    <div class="my-1" x-data="{zoom: false}">

        <canvas data-value="[Interface]
PrivateKey = <?=$profile['private_key']."\n"; ?>
Address = <?=$profile['ip']; ?>/32,<?=$profile['ipv6']; ?>/128
DNS = 8.8.8.8,8.8.4.4

[Peer]
PublicKey = <?=$_config['public_key']."\n"; ?>
PresharedKey = <?=$profile['preshared']."\n"; ?>
Endpoint = <?=$_SERVER['SERVER_ADDR']; ?>:<?=$_config['port']."\n"; ?>
AllowedIPs = 0.0.0.0/0,::/0
"
             x-transition
             @click="zoom = !zoom"
             :style="{width: zoom ? '220px' : '150px'}"
             class="sm:block transition-all qrcodes"
             :class="{'hidden': hideQrCode, 'cursor-zoom-in': !zoom, 'cursor-zoom-out': zoom}"
        ></canvas>
    </div>

</div>
