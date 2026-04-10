{{-- After DOM ready: italic only for "Apnacrowdfunding" (any casing); skip text inside a[href] --}}
<script>
(function () {
    var SKIP_TAGS = { SCRIPT: 1, STYLE: 1, NOSCRIPT: 1, TEXTAREA: 1, IFRAME: 1 };

    function insideHrefLink(textNode) {
        var el = textNode.parentElement;
        return el && typeof el.closest === 'function' && el.closest('a[href]');
    }

    function shouldSkipElement(el) {
        if (!el || el.nodeType !== Node.ELEMENT_NODE) return true;
        if (SKIP_TAGS[el.tagName]) return true;
        if (el.tagName === 'A' && el.hasAttribute('href')) return true;
        return false;
    }

    function wrapBrandInTextNode(textNode) {
        if (!textNode || textNode.nodeType !== Node.TEXT_NODE) return;
        if (insideHrefLink(textNode)) return;
        var text = textNode.textContent;
        if (!text) return;
        var probe = /Apnacrowdfunding/gi;
        if (!probe.test(text)) return;
        probe.lastIndex = 0;
        var parent = textNode.parentNode;
        if (!parent) return;
        var frag = document.createDocumentFragment();
        var last = 0;
        var m;
        var re = /Apnacrowdfunding/gi;
        while ((m = re.exec(text)) !== null) {
            if (m.index > last) {
                frag.appendChild(document.createTextNode(text.slice(last, m.index)));
            }
            var em = document.createElement('em');
            em.textContent = m[0];
            frag.appendChild(em);
            last = m.index + m[0].length;
        }
        if (last < text.length) {
            frag.appendChild(document.createTextNode(text.slice(last)));
        }
        parent.replaceChild(frag, textNode);
    }

    function walk(node) {
        if (!node) return;
        if (node.nodeType === Node.TEXT_NODE) {
            wrapBrandInTextNode(node);
            return;
        }
        if (node.nodeType !== Node.ELEMENT_NODE) return;
        if (shouldSkipElement(node)) return;
        var kids = Array.prototype.slice.call(node.childNodes);
        for (var i = 0; i < kids.length; i++) {
            walk(kids[i]);
        }
    }

    function run() {
        if (!document.body) return;
        walk(document.body);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();
</script>
