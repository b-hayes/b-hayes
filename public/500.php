<?php
http_response_code(500);
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || $_SERVER['HTTP_ACCEPT'] === 'application/json') {
    echo json_encode(['error' => 'internal server error']);
    return;
}
?>
<div class="middle" style="width: 100%;text-align: center;">
    <p style="font-size: xxx-large"><?=http_response_code()?></p>
    <p style="font-size: xxx-large;">🧪💥</p>
    <h1>Wow that was unexpected!</h1>
</div>
<style>
    body {
        display: flex;
        align-items: center; /* Vertical center alignment */
        justify-content: center; /* Horizontal center alignment */
    }
</style>
