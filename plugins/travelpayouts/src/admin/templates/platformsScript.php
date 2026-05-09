<?php
/**
 * @var string $scriptLink
 */
?>
<script data-noptimize="1" data-cfasync="false" data-wpfc-render="false">
    (function () {
        var script = document.createElement("script");
        script.async = 1;
        script.src = '<?= $scriptLink ?>';
        document.head.appendChild(script);
    })();
</script>
