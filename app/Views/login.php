<html lang="id">

<head>

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Masuk - NusaShare</title>

    <!-- Fonts: Inter -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0"
        rel="stylesheet">
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">

    <!-- PWA -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="theme-color" content="#4F46E5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="NusaShare">
    <link rel="apple-touch-icon" href="<?= base_url('assets/icon/logonus.png') ?>">

    <style>
        :root {
            --brand-primary: #4F46E5;
            --brand-secondary: #22D3EE;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #FFFFFF;
        }

        /* Gradient Button */
        .btn-gradient {
            background: linear-gradient(135deg, #4F46E5, #22D3EE);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            opacity: 0.95;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
            transform: translateY(-1px);
        }

        /* Input Styling */
        .input-field {
            transition: all 0.2s ease;
        }

        .input-field:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        /* Image Animation */
        .ken-burns {
            animation: kenBurns 20s infinite alternate;
        }

        @keyframes kenBurns {
            from {
                transform: scale(1);
            }

            to {
                transform: scale(1.1);
            }
        }

        /* Glass Overlay for Artist Credit */
        .glass-credit {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        *,
        ::before,
        ::after {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-gradient-from-position: ;
            --tw-gradient-via-position: ;
            --tw-gradient-to-position: ;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia: ;
            --tw-contain-size: ;
            --tw-contain-layout: ;
            --tw-contain-paint: ;
            --tw-contain-style:
        }

        ::backdrop {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-gradient-from-position: ;
            --tw-gradient-via-position: ;
            --tw-gradient-to-position: ;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia: ;
            --tw-contain-size: ;
            --tw-contain-layout: ;
            --tw-contain-paint: ;
            --tw-contain-style:
        }

        /* ! tailwindcss v3.4.17 | MIT License | https://tailwindcss.com */
        *,
        ::after,
        ::before {
            box-sizing: border-box;
            border-width: 0;
            border-style: solid;
            border-color: #e5e7eb
        }

        ::after,
        ::before {
            --tw-content: ''
        }

        :host,
        html {
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
            -moz-tab-size: 4;
            tab-size: 4;
            font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-feature-settings: normal;
            font-variation-settings: normal;
            -webkit-tap-highlight-color: transparent
        }

        body {
            margin: 0;
            line-height: inherit
        }

        hr {
            height: 0;
            color: inherit;
            border-top-width: 1px
        }

        abbr:where([title]) {
            -webkit-text-decoration: underline dotted;
            text-decoration: underline dotted
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: inherit;
            font-weight: inherit
        }

        a {
            color: inherit;
            text-decoration: inherit
        }

        b,
        strong {
            font-weight: bolder
        }

        code,
        kbd,
        pre,
        samp {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-feature-settings: normal;
            font-variation-settings: normal;
            font-size: 1em
        }

        small {
            font-size: 80%
        }

        sub,
        sup {
            font-size: 75%;
            line-height: 0;
            position: relative;
            vertical-align: baseline
        }

        sub {
            bottom: -.25em
        }

        sup {
            top: -.5em
        }

        table {
            text-indent: 0;
            border-color: inherit;
            border-collapse: collapse
        }

        button,
        input,
        optgroup,
        select,
        textarea {
            font-family: inherit;
            font-feature-settings: inherit;
            font-variation-settings: inherit;
            font-size: 100%;
            font-weight: inherit;
            line-height: inherit;
            letter-spacing: inherit;
            color: inherit;
            margin: 0;
            padding: 0
        }

        button,
        select {
            text-transform: none
        }

        button,
        input:where([type=button]),
        input:where([type=reset]),
        input:where([type=submit]) {
            -webkit-appearance: button;
            background-color: transparent;
            background-image: none
        }

        :-moz-focusring {
            outline: auto
        }

        :-moz-ui-invalid {
            box-shadow: none
        }

        progress {
            vertical-align: baseline
        }

        ::-webkit-inner-spin-button,
        ::-webkit-outer-spin-button {
            height: auto
        }

        [type=search] {
            -webkit-appearance: textfield;
            outline-offset: -2px
        }

        ::-webkit-search-decoration {
            -webkit-appearance: none
        }

        ::-webkit-file-upload-button {
            -webkit-appearance: button;
            font: inherit
        }

        summary {
            display: list-item
        }

        blockquote,
        dd,
        dl,
        figure,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        hr,
        p,
        pre {
            margin: 0
        }

        fieldset {
            margin: 0;
            padding: 0
        }

        legend {
            padding: 0
        }

        menu,
        ol,
        ul {
            list-style: none;
            margin: 0;
            padding: 0
        }

        dialog {
            padding: 0
        }

        textarea {
            resize: vertical
        }

        input::placeholder,
        textarea::placeholder {
            opacity: 1;
            color: #9ca3af
        }

        [role=button],
        button {
            cursor: pointer
        }

        :disabled {
            cursor: default
        }

        audio,
        canvas,
        embed,
        iframe,
        img,
        object,
        svg,
        video {
            display: block;
            vertical-align: middle
        }

        img,
        video {
            max-width: 100%;
            height: auto
        }

        [hidden]:where(:not([hidden=until-found])) {
            display: none
        }

        .absolute {
            position: absolute
        }

        .relative {
            position: relative
        }

        .inset-0 {
            inset: 0px
        }

        .bottom-12 {
            bottom: 3rem
        }

        .bottom-6 {
            bottom: 1.5rem
        }

        .left-12 {
            left: 3rem
        }

        .left-6 {
            left: 1.5rem
        }

        .left-8 {
            left: 2rem
        }

        .right-12 {
            right: 3rem
        }

        .right-4 {
            right: 1rem
        }

        .right-8 {
            right: 2rem
        }

        .top-1\/2 {
            top: 50%
        }

        .top-8 {
            top: 2rem
        }

        .mb-1\.5 {
            margin-bottom: 0.375rem
        }

        .mb-10 {
            margin-bottom: 2.5rem
        }

        .mb-2 {
            margin-bottom: 0.5rem
        }

        .mb-8 {
            margin-bottom: 2rem
        }

        .ml-auto {
            margin-left: auto
        }

        .mt-2 {
            margin-top: 0.5rem
        }

        .mt-6 {
            margin-top: 1.5rem
        }

        .mt-8 {
            margin-top: 2rem
        }

        .block {
            display: block
        }

        .flex {
            display: flex
        }

        .inline-flex {
            display: inline-flex
        }

        .grid {
            display: grid
        }

        .hidden {
            display: none
        }

        .h-12 {
            height: 3rem
        }

        .h-5 {
            height: 1.25rem
        }

        .h-8 {
            height: 2rem
        }

        .h-full {
            height: 100%
        }

        .h-screen {
            height: 100vh
        }

        .w-1\/2 {
            width: 50%
        }

        .w-12 {
            width: 3rem
        }

        .w-5 {
            width: 1.25rem
        }

        .w-8 {
            width: 2rem
        }

        .w-full {
            width: 100%
        }

        .max-w-md {
            max-width: 28rem
        }

        .-translate-y-1\/2 {
            --tw-translate-y: -50%;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
        }

        .animate-\[fadeInUp_0\.6s_ease-out\] {
            animation: fadeInUp 0.6s ease-out
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr))
        }

        .flex-col {
            flex-direction: column
        }

        .items-center {
            align-items: center
        }

        .justify-center {
            justify-content: center
        }

        .justify-between {
            justify-content: space-between
        }

        .gap-1 {
            gap: 0.25rem
        }

        .gap-2 {
            gap: 0.5rem
        }

        .gap-3 {
            gap: 0.75rem
        }

        .gap-4 {
            gap: 1rem
        }

        .space-y-5> :not([hidden])~ :not([hidden]) {
            --tw-space-y-reverse: 0;
            margin-top: calc(1.25rem * calc(1 - var(--tw-space-y-reverse)));
            margin-bottom: calc(1.25rem * var(--tw-space-y-reverse))
        }

        .overflow-hidden {
            overflow: hidden
        }

        .overflow-y-auto {
            overflow-y: auto
        }

        .rounded-2xl {
            border-radius: 1rem
        }

        .rounded-full {
            border-radius: 9999px
        }

        .rounded-lg {
            border-radius: 0.5rem
        }

        .rounded-xl {
            border-radius: 0.75rem
        }

        .border {
            border-width: 1px
        }

        .border-2 {
            border-width: 2px
        }

        .border-l {
            border-left-width: 1px
        }

        .border-t {
            border-top-width: 1px
        }

        .border-slate-200 {
            --tw-border-opacity: 1;
            border-color: rgb(226 232 240 / var(--tw-border-opacity, 1))
        }

        .border-slate-300 {
            --tw-border-opacity: 1;
            border-color: rgb(203 213 225 / var(--tw-border-opacity, 1))
        }

        .border-white\/10 {
            border-color: rgb(255 255 255 / 0.1)
        }

        .border-white\/20 {
            border-color: rgb(255 255 255 / 0.2)
        }

        .border-white\/50 {
            border-color: rgb(255 255 255 / 0.5)
        }

        .bg-slate-900 {
            --tw-bg-opacity: 1;
            background-color: rgb(15 23 42 / var(--tw-bg-opacity, 1))
        }

        .bg-white {
            --tw-bg-opacity: 1;
            background-color: rgb(255 255 255 / var(--tw-bg-opacity, 1))
        }

        .bg-white\/20 {
            background-color: rgb(255 255 255 / 0.2)
        }

        .bg-gradient-to-br {
            background-image: linear-gradient(to bottom right, var(--tw-gradient-stops))
        }

        .bg-gradient-to-t {
            background-image: linear-gradient(to top, var(--tw-gradient-stops))
        }

        .from-\[\#0F172A\]\/90 {
            --tw-gradient-from: rgb(15 23 42 / 0.9) var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(15 23 42 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
        }

        .from-\[\#4F46E5\] {
            --tw-gradient-from: #4F46E5 var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(79 70 229 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
        }

        .via-transparent {
            --tw-gradient-to: rgb(0 0 0 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), transparent var(--tw-gradient-via-position), var(--tw-gradient-to)
        }

        .to-\[\#22D3EE\] {
            --tw-gradient-to: #22D3EE var(--tw-gradient-to-position)
        }

        .to-transparent {
            --tw-gradient-to: transparent var(--tw-gradient-to-position)
        }

        .object-cover {
            object-fit: cover
        }

        .p-4 {
            padding: 1rem
        }

        .px-2 {
            padding-left: 0.5rem;
            padding-right: 0.5rem
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem
        }

        .px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem
        }

        .py-2\.5 {
            padding-top: 0.625rem;
            padding-bottom: 0.625rem
        }

        .py-3 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem
        }

        .py-3\.5 {
            padding-top: 0.875rem;
            padding-bottom: 0.875rem
        }

        .pl-4 {
            padding-left: 1rem
        }

        .pr-6 {
            padding-right: 1.5rem
        }

        .text-center {
            text-align: center
        }

        .text-3xl {
            font-size: 1.875rem;
            line-height: 2.25rem
        }

        .text-lg {
            font-size: 1.125rem;
            line-height: 1.75rem
        }

        .text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem
        }

        .text-xl {
            font-size: 1.25rem;
            line-height: 1.75rem
        }

        .text-xs {
            font-size: 0.75rem;
            line-height: 1rem
        }

        .font-bold {
            font-weight: 700
        }

        .font-medium {
            font-weight: 500
        }

        .font-semibold {
            font-weight: 600
        }

        .leading-relaxed {
            line-height: 1.625
        }

        .tracking-tight {
            letter-spacing: -0.025em
        }

        .text-\[\#4F46E5\] {
            --tw-text-opacity: 1;
            color: rgb(79 70 229 / var(--tw-text-opacity, 1))
        }

        .text-cyan-300 {
            --tw-text-opacity: 1;
            color: rgb(103 232 249 / var(--tw-text-opacity, 1))
        }

        .text-slate-400 {
            --tw-text-opacity: 1;
            color: rgb(148 163 184 / var(--tw-text-opacity, 1))
        }

        .text-slate-500 {
            --tw-text-opacity: 1;
            color: rgb(100 116 139 / var(--tw-text-opacity, 1))
        }

        .text-slate-600 {
            --tw-text-opacity: 1;
            color: rgb(71 85 105 / var(--tw-text-opacity, 1))
        }

        .text-slate-700 {
            --tw-text-opacity: 1;
            color: rgb(51 65 85 / var(--tw-text-opacity, 1))
        }

        .text-slate-900 {
            --tw-text-opacity: 1;
            color: rgb(15 23 42 / var(--tw-text-opacity, 1))
        }

        .text-white {
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity, 1))
        }

        .text-white\/80 {
            color: rgb(255 255 255 / 0.8)
        }

        .placeholder-slate-400::placeholder {
            --tw-placeholder-opacity: 1;
            color: rgb(148 163 184 / var(--tw-placeholder-opacity, 1))
        }

        .opacity-90 {
            opacity: 0.9
        }

        .shadow-lg {
            --tw-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)
        }

        .shadow-indigo-500\/30 {
            --tw-shadow-color: rgb(99 102 241 / 0.3);
            --tw-shadow: var(--tw-shadow-colored)
        }

        .drop-shadow-md {
            --tw-drop-shadow: drop-shadow(0 4px 3px rgb(0 0 0 / 0.07)) drop-shadow(0 2px 2px rgb(0 0 0 / 0.06));
            filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)
        }

        .backdrop-blur {
            --tw-backdrop-blur: blur(8px);
            -webkit-backdrop-filter: var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia);
            backdrop-filter: var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia)
        }

        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms
        }

        .transition-colors {
            transition-property: color, background-color, border-color, fill, stroke, -webkit-text-decoration-color;
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, -webkit-text-decoration-color;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms
        }

        .transition-transform {
            transition-property: transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms
        }

        .hover\:border-slate-300:hover {
            --tw-border-opacity: 1;
            border-color: rgb(203 213 225 / var(--tw-border-opacity, 1))
        }

        .hover\:bg-slate-50:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(248 250 252 / var(--tw-bg-opacity, 1))
        }

        .hover\:text-\[\#4338CA\]:hover {
            --tw-text-opacity: 1;
            color: rgb(67 56 202 / var(--tw-text-opacity, 1))
        }

        .hover\:text-\[\#4F46E5\]:hover {
            --tw-text-opacity: 1;
            color: rgb(79 70 229 / var(--tw-text-opacity, 1))
        }

        .hover\:text-slate-600:hover {
            --tw-text-opacity: 1;
            color: rgb(71 85 105 / var(--tw-text-opacity, 1))
        }

        .hover\:underline:hover {
            -webkit-text-decoration-line: underline;
            text-decoration-line: underline
        }

        .focus\:outline-none:focus {
            outline: 2px solid transparent;
            outline-offset: 2px
        }

        .group:hover .group-hover\:-translate-x-1 {
            --tw-translate-x: -0.25rem;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
        }

        @media (min-width: 640px) {
            .sm\:px-12 {
                padding-left: 3rem;
                padding-right: 3rem
            }
        }

        @media (min-width: 768px) {
            .md\:px-20 {
                padding-left: 5rem;
                padding-right: 5rem
            }
        }

        @media (min-width: 1024px) {
            .lg\:block {
                display: block
            }

            .lg\:hidden {
                display: none
            }

            .lg\:w-1\/2 {
                width: 50%
            }

            .lg\:justify-start {
                justify-content: flex-start
            }

            .lg\:text-left {
                text-align: left
            }
        }
    </style>
    <link type="text/css"
        href="https://fonts.googleapis.com/css2?family=Google+Symbols:opsz,wght,FILL,GRAD,ROND@24,400,0,0,50&amp;icon_names=link"
        rel="stylesheet">
    <style id="dyn-img-link-styles-di-script">
        .dyn-img-link-wrapper-di-script {
            anchor-scope: all;
            display: contents;
            position: relative;
            z-index: 1;
        }

        .dyn-img-link-wrapper-di-script img {
            anchor-name: --photo;
            display: block;
            max-width: 100%;
        }

        .dyn-img-link-di-script {
            z-index: 999999;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 9999px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            color: white;
            height: clamp(13px, calc(max(anchor-size(width), anchor-size(height)) * 0.3), 21px);
            width: clamp(13px, calc(max(anchor-size(width), anchor-size(height)) * 0.3), 21px);
            padding: 4px;
            position: absolute;

            bottom: anchor(bottom);
            left: anchor(left);
            margin-left: 10px;
            margin-bottom: 10px;

            transition: background-color 0.2s ease-in-out;
        }

        .dyn-img-link-di-script:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .dyn-img-link-di-script span {
            font-size: 14px;
            vertical-align: top;
        }

        .dyn-img-attribution-button-di-script {
            z-index: 999999;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 9999px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            color: white;
            width: clamp(13px, calc(max(anchor-size(width), anchor-size(height)) * 0.3), 21px);
            height: clamp(13px, calc(max(anchor-size(width), anchor-size(height)) * 0.3), 21px);
            padding: 4px;

            position: absolute;
            position-anchor: --photo;
            top: anchor(top);
            right: anchor(right);
            margin-top: 10px;
            margin-right: 10px;

            transition: background-color 0.2s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dyn-img-attribution-button-di-script:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .dyn-img-attribution-button-di-script span {
            font-size: 14px;
            vertical-align: top;
        }

        .dyn-img-inline-attribution-di-script {
            z-index: 999999;
            font-size: 12px;
            padding: 4px;
            display: none;

            position: absolute;
            position-anchor: --photo;
            bottom: anchor(bottom);
            right: anchor(right);
            margin-bottom: 8px;
            margin-right: 8px;

            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: right;

            white-space: nowrap;
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 9999px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            color: white;
        }
    </style>
</head>

<body class="h-screen w-full overflow-hidden flex">

    <!-- LEFT SIDE: Visual / Artwork Spotlight (Hidden on Mobile) -->
    <div class="hidden lg:block w-1/2 h-full relative overflow-hidden bg-slate-900">
        <!-- Background Image -->
        <?php
        $coverUrl = base_url('image/cover/' . ($featuredWork['id'] ?? 0));
        if (empty($featuredWork['cover_url'])) {
            $coverUrl = 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=1964&auto=format&fit=crop';
        }
        ?>
        <img src="<?= $coverUrl ?>" alt="Featured Artwork"
            class="absolute inset-0 w-full h-full object-cover ken-burns opacity-90">

        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#0F172A]/90 via-transparent to-transparent"></div>

        <!-- Logo Overlay Top Left -->
        <div class="absolute top-8 left-8 flex items-center gap-3">
            <div
                class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center text-white font-bold border border-white/10">
                <img src="<?= base_url('assets/icon/login.png') ?>" alt="Logo N" class="w-8 h-8" /></div>
            <span class="text-white font-bold text-lg tracking-tight drop-shadow-md">NusaShare</span>
        </div>

        <!-- Artist Credit (Bottom) -->
        <div class="absolute bottom-12 left-12 right-12">
            <div class="glass-credit inline-flex items-center gap-4 p-4 pr-6 rounded-2xl">
                <div
                    class="w-12 h-12 rounded-full border-2 border-white/50 bg-indigo-500/30 backdrop-blur flex items-center justify-center text-white font-bold text-xl overflow-hidden shrink-0">
                    <?= strtoupper(substr($featuredWork['creator_name'] ?? 'N', 0, 1)) ?>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm"><?= esc($featuredWork['title'] ?? 'Nusa Dreamscape') ?>
                    </p>
                    <p class="text-cyan-300 text-xs">Karya oleh
                        <?= esc($featuredWork['creator_name'] ?? 'Kreator Nusa') ?></p>
                </div>
                <div class="ml-auto pl-4 border-l border-white/20">
                    <span class="text-white/80 text-xs">Featured Artist</span>
                </div>
            </div>
            <p class="mt-6 text-slate-400 text-sm max-w-md leading-relaxed">
                "NusaShare memberi saya ruang untuk tidak hanya memajang karya, tetapi menemukan kolektor yang
                benar-benar menghargai detail."
            </p>
        </div>
    </div>

    <!-- RIGHT SIDE: Login Form -->
    <div
        class="w-full lg:w-1/2 h-full flex flex-col justify-center items-center bg-white px-6 sm:px-12 md:px-20 relative overflow-y-auto">

        <!-- Mobile Logo (Visible only on small screens) -->
        <div class="lg:hidden absolute top-8 left-6 flex items-center gap-2">
            <img src="<?= base_url('assets/icon/logonus.png') ?>" alt="Logo N" class="w-8 h-8" />
            <span class="font-bold text-lg text-slate-900">NusaShare</span>
        </div>

        <!-- Back Link -->
        <a href="#"
            class="absolute top-8 right-8 text-sm text-slate-500 hover:text-[#4F46E5] flex items-center gap-1 transition-colors group">
            <span
                class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Kembali ke Explore
        </a>

        <div class="w-full max-w-md animate-[fadeInUp_0.6s_ease-out]">
            <!-- Header -->
            <div class="mb-10 text-center lg:text-left">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Selamat Datang Kembali.</h1>
                <p class="text-slate-500">Masuk untuk mengelola karya atau koleksi Anda.</p>
            </div>

            <!-- Social Login -->
            <div class="mb-8">
                <a href="<?= base_url('auth/login') ?>"
                    class="flex items-center justify-center gap-2 py-2.5 w-full border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
                    <span class="text-sm font-medium text-slate-700">Masuk dengan Google</span>
                </a>
            </div>

            <!-- Divider -->
            <div class="relative mb-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-slate-400">atau masuk dengan email</span>
                </div>
            </div>

            <!-- Notifications -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-xl">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-sm rounded-r-xl">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="<?= base_url('login') ?>" method="POST" class="space-y-5">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" id="email" name="email"
                        class="input-field w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none"
                        placeholder="nama@email.com" required="">
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium text-slate-700">Kata Sandi</label>
                        <a href="<?= base_url('forgot-password') ?>"
                            class="text-sm text-[#4F46E5] hover:text-[#4338CA] font-medium">Lupa sandi?</a>
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="input-field w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none"
                            placeholder="••••••••" required="">
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <span class="material-symbols-outlined text-xl" id="eye-icon">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="btn-gradient w-full py-3.5 rounded-xl text-white font-bold text-lg shadow-lg shadow-indigo-500/30 mt-2">
                    Masuk
                </button>
            </form>

            <!-- Footer Link -->
            <p class="mt-8 text-center text-sm text-slate-600">
                Belum punya akun?
                <a href="<?= base_url('register') ?>" class="font-bold text-[#4F46E5] hover:underline">Daftar
                    sekarang</a>
            </p>
        </div>

        <!-- Trust Footer -->
        <div class="absolute bottom-6 w-full text-center lg:text-left px-6 sm:px-12 md:px-20">
            <div class="flex items-center justify-center lg:justify-start gap-4 text-slate-400 text-xs">
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">lock</span>
                    Dilindungi dengan enkripsi end-to-end.
                </div>
                <div class="flex gap-3">
                    <a href="<?= base_url('terms') ?>" class="hover:text-slate-600">Syarat</a>
                    <a href="<?= base_url('privacy') ?>" class="hover:text-slate-600">Privasi</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = 'visibility';
            }
        }
    </script>

    <!--
    Image Urls:
    "/gen?prompt=surreal+digital+art+indonesian+mythology+garuda+wisnu+kencana+dreamscape+vibrant+indigo+cyan+gold+colors+vertical+composition&aspect=9:16"
    "/gen?prompt=portrait+of+young+cool+indonesian+digital+artist+with+headphones&aspect=1:1"
    -->


    <script src="<?= base_url('assets/js/pwa.js') ?>"></script>
</body>

</html>