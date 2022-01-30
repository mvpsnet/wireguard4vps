<?php
if (count(get_included_files()) == 1) {
    die("Direct access not allowed");
}

if(!empty($success_msg)){ ?>
<div class="alert alert-success">
    <p><?=$success_msg; ?></p>
</div>
<?php } elseif(!empty($warn_msg)){  ?>
<div class="alert">
    <p><?=$warn_msg; ?></p>
</div>
<?php } elseif(!empty($info_msg)){  ?>
<div class="alert alert-info">
    <p><?=$info_msg; ?>/p>
</div>
<?php } elseif(!empty($error_msg)){  ?>
<div class="alert alert-danger">
    <p><?=$error_msg; ?></p>
</div>
<?php }
