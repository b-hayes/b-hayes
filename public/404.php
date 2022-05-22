<?php
http_response_code(404);
?>
<div style="width: 100%;text-align: center;">
    <p style="font-size: xxx-large"><?=http_response_code()?></p>
    <p style="font-size: xxx-large;"> <?= $_SERVER['REQUEST_URI'] ?>❔ 🤔</p>
    <h1>Yeah nah, cant find that here bud.</h1>
</div>
<style>
    body {
        display: flex;
        align-items: center; /* Vertical center alignment */
        justify-content: center; /* Horizontal center alignment */
    }
</style>
