<?php
/**
 * @var string $marker
 */
?>
<!-- Travelpayouts analytics (travelpayouts.com) -->
<script id="__tpam_snippet_node" type="text/javascript">
    (function (t, p, a, m) {
        p.TPAM_CONFIG = { params: t, scriptInitializationDate: new Date() };
        var entrypoint = document.createElement("script");
        var snippet = document.getElementById(a);
        var sp = new URLSearchParams(t);
        sp.append("page_url", p.location.href);
        entrypoint.src = "https://tpo.gg/entrypoint.js?" + sp.toString();
        if (snippet) snippet.parentNode.insertBefore(entrypoint, snippet);
    })(
        { marker: <?= $marker ?>, page_opened_id: crypto.randomUUID() },
        window,
        "__tpam_snippet_node"
    );
</script>