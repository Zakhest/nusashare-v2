<?php

/**
 * article_helper.php
 *
 * Parser untuk body artikel NusaShare.
 * Mendukung blok khusus: infobox, table, image.
 * Semua baris lainnya dirender sebagai paragraf HTML.
 *
 * Syntax blok:
 *
 * infobox: [
 *   { "key": "Nama", "value": "Hanami Wickecklov" },
 *   { "key": "Kekuatan", "value": "Cahaya putih + angin" }
 * ]
 *
 * table: {
 *   "headers": ["Karakter", "Kekuatan"],
 *   "rows": [["Hanami", "Angin"], ["Riku", "Api"]]
 * }
 *
 * image: {
 *   "url": "https://...",
 *   "layout": "center",
 *   "caption": "Keterangan gambar"
 * }
 * Layout values: left | center | right | full
 */

if (!function_exists('render_article_body')) {

    /**
     * Parse dan render body artikel menjadi HTML.
     *
     * @param  string $rawBody  Teks mentah dari database
     * @return string           HTML yang aman ditampilkan
     */
    function render_article_body(string $rawBody): string
    {
        if (trim($rawBody) === '') {
            return '';
        }

        $html   = '';
        $blocks = _split_article_blocks($rawBody);

        foreach ($blocks as $block) {
            $trimmed = trim($block);
            if ($trimmed === '') {
                continue;
            }

            if (_starts_with($trimmed, 'infobox:')) {
                $html .= _render_infobox($trimmed);
            } elseif (_starts_with($trimmed, 'table:')) {
                $html .= _render_table($trimmed);
            } elseif (_starts_with($trimmed, 'image:')) {
                $html .= _render_image($trimmed);
            } else {
                $html .= _render_paragraph($trimmed);
            }
        }

        return $html;
    }

    /**
     * Split body menjadi blok-blok.
     * Blok dipisahkan oleh dua newline berturut-turut.
     * Tapi kita harus hati-hati agar JSON multi-baris tidak terpotong.
     */
    function _split_article_blocks(string $body): array
    {
        // Normalize line endings
        $body = str_replace(["\r\n", "\r"], "\n", $body);

        $blocks    = [];
        $lines     = explode("\n", $body);
        $buffer    = '';
        $depth     = 0; // track JSON bracket depth
        $inSpecial = false;

        foreach ($lines as $line) {
            $trimmed       = ltrim($line);
            $startsSpecial = _starts_with($trimmed, 'infobox:')
                || _starts_with($trimmed, 'table:')
                || _starts_with($trimmed, 'image:');

            // Special blocks may appear right after normal text without an empty line.
            if (!$inSpecial && $startsSpecial) {
                if (trim($buffer) !== '') {
                    $blocks[] = $buffer;
                }
                $buffer    = '';
                $depth     = 0;
                $inSpecial = true;
            }

            $buffer .= $line . "\n";
            $depth += substr_count($line, '[') + substr_count($line, '{');
            $depth -= substr_count($line, ']') + substr_count($line, '}');

            if ($inSpecial && $depth <= 0) {
                if (trim($buffer) !== '') {
                    $blocks[] = $buffer;
                }
                $buffer    = '';
                $depth     = 0;
                $inSpecial = false;
                continue;
            }

            // Blok teks biasa selesai jika: baris kosong DAN tidak di dalam JSON
            if (!$inSpecial && trim($line) === '' && $depth <= 0) {
                if (trim($buffer) !== '') {
                    $blocks[] = $buffer;
                }
                $buffer = '';
                $depth  = 0;
            }
        }

        if (trim($buffer) !== '') {
            $blocks[] = $buffer;
        }

        return $blocks;
    }

    /**
     * Render blok infobox.
     */
    function _render_infobox(string $block): string
    {
        $jsonStr = trim(substr($block, strlen('infobox:')));
        $data    = json_decode($jsonStr, true);

        if (!is_array($data) || empty($data)) {
            return '<p class="article-parse-error">⚠️ Infobox tidak valid.</p>';
        }

        $rows = '';
        foreach ($data as $row) {
            if (!isset($row['key']) || !isset($row['value'])) {
                continue;
            }
            $key   = htmlspecialchars($row['key'], ENT_QUOTES, 'UTF-8');
            $value = htmlspecialchars($row['value'], ENT_QUOTES, 'UTF-8');
            $rows .= "
                <tr>
                    <th class=\"article-infobox-key\">{$key}</th>
                    <td class=\"article-infobox-value\">{$value}</td>
                </tr>";
        }

        return "
        <div class=\"article-infobox\">
            <div class=\"article-infobox-header\">
                <span class=\"article-infobox-icon\">📋</span>
                <span>Info</span>
            </div>
            <table class=\"article-infobox-table\">
                <tbody>{$rows}</tbody>
            </table>
        </div>";
    }

    /**
     * Render blok tabel.
     */
    function _render_table(string $block): string
    {
        $jsonStr = trim(substr($block, strlen('table:')));
        $data    = json_decode($jsonStr, true);

        if (!is_array($data) || empty($data['rows'])) {
            return '<p class="article-parse-error">⚠️ Tabel tidak valid.</p>';
        }

        $theadHtml = '';
        if (!empty($data['headers'])) {
            $ths = '';
            foreach ($data['headers'] as $h) {
                $ths .= '<th>' . htmlspecialchars($h, ENT_QUOTES, 'UTF-8') . '</th>';
            }
            $theadHtml = "<thead><tr>{$ths}</tr></thead>";
        }

        $tbodyHtml = '<tbody>';
        foreach ($data['rows'] as $row) {
            $tds = '';
            foreach ((array) $row as $cell) {
                $tds .= '<td>' . htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') . '</td>';
            }
            $tbodyHtml .= "<tr>{$tds}</tr>";
        }
        $tbodyHtml .= '</tbody>';

        return "<div class=\"article-table-wrap\"><table class=\"article-table\">{$theadHtml}{$tbodyHtml}</table></div>";
    }

    /**
     * Render blok gambar.
     */
    function _render_image(string $block): string
    {
        $jsonStr = trim(substr($block, strlen('image:')));
        $data    = json_decode($jsonStr, true);

        if (!is_array($data) || empty($data['url'])) {
            return '<p class="article-parse-error">⚠️ Gambar tidak valid.</p>';
        }

        $url     = htmlspecialchars($data['url'], ENT_QUOTES, 'UTF-8');
        $caption = htmlspecialchars($data['caption'] ?? '', ENT_QUOTES, 'UTF-8');
        $layout  = $data['layout'] ?? 'center';

        // Sanitize layout
        $allowedLayouts = ['left', 'center', 'right', 'full'];
        if (!in_array($layout, $allowedLayouts)) {
            $layout = 'center';
        }

        $figCaption = $caption !== ''
            ? "<figcaption class=\"article-img-caption\">{$caption}</figcaption>"
            : '';

        return "
        <figure class=\"article-img article-img--{$layout}\">
            <img src=\"{$url}\" alt=\"" . ($caption ?: 'Gambar artikel') . "\" loading=\"lazy\">
            {$figCaption}
        </figure>";
    }

    /**
     * Render teks biasa menjadi paragraf HTML.
     * Mendukung **bold** dan _italic_ dasar.
     */
    function _render_paragraph(string $block): string
    {
        $escaped = htmlspecialchars($block, ENT_QUOTES, 'UTF-8');

        // Inline markdown: **bold**
        $escaped = preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $escaped);
        // Inline markdown: _italic_
        $escaped = preg_replace('/_(.+?)_/u', '<em>$1</em>', $escaped);

        // Split menjadi paragraf per double-newline di dalam blok
        $paras = preg_split('/\n\s*\n/', $escaped);
        $html  = '';
        foreach ($paras as $para) {
            $para = trim($para);
            if ($para === '') {
                continue;
            }
            // Konversi single newline jadi <br>
            $para = nl2br($para);
            $html .= "<p class=\"article-para\">{$para}</p>\n";
        }

        return $html ?: '';
    }

    /**
     * Utility: cek apakah string dimulai dengan prefix (case-sensitive).
     */
    function _starts_with(string $str, string $prefix): bool
    {
        return strncmp($str, $prefix, strlen($prefix)) === 0;
    }
}
