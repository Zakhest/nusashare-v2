<html lang="id"><head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NusaShare - Gerbang Masa Depan Kreator Indonesia</title>
    
    <!-- Fonts: Inter for that clean, Apple-like aesthetic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="icon" href="<?= base_url('assets/icon/logonus.png') ?>" type="image/x-icon">

    <!-- PWA -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="theme-color" content="#4F46E5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="NusaShare">
    <link rel="apple-touch-icon" href="/assets/pwa/icon-192.png">
    <meta name="msapplication-TileImage" content="/assets/pwa/icon-144.png">
    <meta name="msapplication-TileColor" content="#4F46E5">

    <style>
        :root {
            --lp-bg: #EEF2FF;
            --lp-primary: #4F46E5;
            --lp-accent: #22D3EE;
            --lp-surface: #FFFFFF;
            --lp-border: rgba(79, 70, 229, 0.15);
            --lp-text-main: #0F172A;
            --lp-text-muted: #475569;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--lp-bg);
            color: var(--lp-text-main);
            overflow-x: hidden;
        }

        /* Custom Gradients & Shadows */
        .cta-primary {
            background: linear-gradient(135deg, #4F46E5, #22D3EE);
            color: white;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.25);
            transition: all 0.3s ease;
        }

        .cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(79, 70, 229, 0.35);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .surface-card {
            background: var(--lp-surface);
            border: 1px solid var(--lp-border);
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.05);
        }

        /* Text Gradients */
        .text-gradient {
            background: linear-gradient(135deg, #4F46E5, #22D3EE);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .fade-in-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <style>
       *, ::before, ::after
       {--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }::backdrop{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }/* ! tailwindcss v3.4.17 | MIT License | https://tailwindcss.com */*,::after,::before{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}::after,::before{--tw-content:''}:host,html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;font-family:ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;letter-spacing:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}button,input:where([type=button]),input:where([type=reset]),input:where([type=submit]){-webkit-appearance:button;background-color:transparent;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dd,dl,figure,h1,h2,h3,h4,h5,h6,hr,p,pre{margin:0}fieldset{margin:0;padding:0}legend{padding:0}menu,ol,ul{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}[role=button],button{cursor:pointer}:disabled{cursor:default}audio,canvas,embed,iframe,img,object,svg,video{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]:where(:not([hidden=until-found])){display:none}.pointer-events-none{pointer-events:none}.fixed{position:fixed}.absolute{position:absolute}.relative{position:relative}.inset-0{inset:0px}.left-1\/2{left:50%}.right-0{right:0px}.top-0{top:0px}.top-1\/2{top:50%}.-z-10{z-index:-10}.z-10{z-index:10}.z-50{z-index:50}.order-1{order:1}.order-2{order:2}.mx-auto{margin-left:auto;margin-right:auto}.mb-10{margin-bottom:2.5rem}.mb-16{margin-bottom:4rem}.mb-2{margin-bottom:0.5rem}.mb-24{margin-bottom:6rem}.mb-3{margin-bottom:0.75rem}.mb-4{margin-bottom:1rem}.mb-6{margin-bottom:1.5rem}.mb-8{margin-bottom:2rem}.ml-1{margin-left:0.25rem}.mt-0\.5{margin-top:0.125rem}.mt-2\.5{margin-top:0.625rem}.mt-8{margin-top:2rem}.block{display:block}.flex{display:flex}.inline-flex{display:inline-flex}.grid{display:grid}.hidden{display:none}.h-1{height:0.25rem}.h-1\.5{height:0.375rem}.h-12{height:3rem}.h-14{height:3.5rem}.h-2{height:0.5rem}.h-20{height:5rem}.h-6{height:1.5rem}.h-8{height:2rem}.h-\[120\%\]{height:120%}.h-\[500px\]{height:500px}.h-full{height:100%}.w-1\.5{width:0.375rem}.w-1\/3{width:33.333333%}.w-12{width:3rem}.w-14{width:3.5rem}.w-2{width:0.5rem}.w-6{width:1.5rem}.w-8{width:2rem}.w-\[120\%\]{width:120%}.w-\[800px\]{width:800px}.w-full{width:100%}.max-w-2xl{max-width:42rem}.max-w-3xl{max-width:48rem}.max-w-4xl{max-width:56rem}.max-w-7xl{max-width:80rem}.max-w-lg{max-width:32rem}.max-w-md{max-width:28rem}.flex-shrink-0{flex-shrink:0}.-translate-x-1\/2{--tw-translate-x:-50%;transform:translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.-translate-y-1\/2{--tw-translate-y:-50%;transform:translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.-rotate-2{--tw-rotate:-2deg;transform:translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.rotate-2{--tw-rotate:2deg;transform:translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.rotate-3{--tw-rotate:3deg;transform:translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.transform{transform:translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.flex-col{flex-direction:column}.items-start{align-items:flex-start}.items-center{align-items:center}.justify-center{justify-content:center}.justify-between{justify-content:space-between}.gap-12{gap:3rem}.gap-16{gap:4rem}.gap-2{gap:0.5rem}.gap-3{gap:0.75rem}.gap-4{gap:1rem}.gap-5{gap:1.25rem}.gap-6{gap:1.5rem}.gap-8{gap:2rem}.space-y-4 > :not([hidden]) ~ :not([hidden]){--tw-space-y-reverse:0;margin-top:calc(1rem * calc(1 - var(--tw-space-y-reverse)));margin-bottom:calc(1rem * var(--tw-space-y-reverse))}.space-y-8 > :not([hidden]) ~ :not([hidden]){--tw-space-y-reverse:0;margin-top:calc(2rem * calc(1 - var(--tw-space-y-reverse)));margin-bottom:calc(2rem * var(--tw-space-y-reverse))}.overflow-hidden{overflow:hidden}.rounded{border-radius:0.25rem}.rounded-2xl{border-radius:1rem}.rounded-3xl{border-radius:1.5rem}.rounded-full{border-radius:9999px}.rounded-lg{border-radius:0.5rem}.rounded-xl{border-radius:0.75rem}.border{border-width:1px}.border-2{border-width:2px}.border-b{border-bottom-width:1px}.border-t{border-top-width:1px}.border-\[\#4F46E5\]{--tw-border-opacity:1;border-color:rgb(79 70 229 / var(--tw-border-opacity, 1))}.border-\[\#4F46E5\]\/20{border-color:rgb(79 70 229 / 0.2)}.border-\[\#4F46E5\]\/30{border-color:rgb(79 70 229 / 0.3)}.border-\[\#4F46E5\]\/60{border-color:rgb(79 70 229 / 0.6)}.border-\[\#EEF2FF\]{--tw-border-opacity:1;border-color:rgb(238 242 255 / var(--tw-border-opacity, 1))}.border-slate-700{--tw-border-opacity:1;border-color:rgb(51 65 85 / var(--tw-border-opacity, 1))}.border-slate-800{--tw-border-opacity:1;border-color:rgb(30 41 59 / var(--tw-border-opacity, 1))}.border-transparent{border-color:transparent}.border-white{--tw-border-opacity:1;border-color:rgb(255 255 255 / var(--tw-border-opacity, 1))}.border-white\/50{border-color:rgb(255 255 255 / 0.5)}.bg-\[\#0F172A\]{--tw-bg-opacity:1;background-color:rgb(15 23 42 / var(--tw-bg-opacity, 1))}.bg-\[\#22D3EE\]{--tw-bg-opacity:1;background-color:rgb(34 211 238 / var(--tw-bg-opacity, 1))}.bg-\[\#4F46E5\]{--tw-bg-opacity:1;background-color:rgb(79 70 229 / var(--tw-bg-opacity, 1))}.bg-\[\#EEF2FF\]{--tw-bg-opacity:1;background-color:rgb(238 242 255 / var(--tw-bg-opacity, 1))}.bg-\[\#F1F5F9\]{--tw-bg-opacity:1;background-color:rgb(241 245 249 / var(--tw-bg-opacity, 1))}.bg-\[\#F8FAFC\]{--tw-bg-opacity:1;background-color:rgb(248 250 252 / var(--tw-bg-opacity, 1))}.bg-gray-200{--tw-bg-opacity:1;background-color:rgb(229 231 235 / var(--tw-bg-opacity, 1))}.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255 / var(--tw-bg-opacity, 1))}.bg-white\/70{background-color:rgb(255 255 255 / 0.7)}.bg-gradient-to-br{background-image:linear-gradient(to bottom right, var(--tw-gradient-stops))}.bg-gradient-to-l{background-image:linear-gradient(to left, var(--tw-gradient-stops))}.bg-gradient-to-r{background-image:linear-gradient(to right, var(--tw-gradient-stops))}.bg-gradient-to-tr{background-image:linear-gradient(to top right, var(--tw-gradient-stops))}.from-\[\#06B6D4\]{--tw-gradient-from:#06B6D4 var(--tw-gradient-from-position);--tw-gradient-to:rgb(6 182 212 / 0) var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from), var(--tw-gradient-to)}.from-\[\#4338CA\]{--tw-gradient-from:#4338CA var(--tw-gradient-from-position);--tw-gradient-to:rgb(67 56 202 / 0) var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from), var(--tw-gradient-to)}.from-\[\#4F46E5\]{--tw-gradient-from:#4F46E5 var(--tw-gradient-from-position);--tw-gradient-to:rgb(79 70 229 / 0) var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from), var(--tw-gradient-to)}.from-\[\#4F46E5\]\/10{--tw-gradient-from:rgb(79 70 229 / 0.1) var(--tw-gradient-from-position);--tw-gradient-to:rgb(79 70 229 / 0) var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from), var(--tw-gradient-to)}.from-\[\#4F46E5\]\/5{--tw-gradient-from:rgb(79 70 229 / 0.05) var(--tw-gradient-from-position);--tw-gradient-to:rgb(79 70 229 / 0) var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from), var(--tw-gradient-to)}.from-white\/50{--tw-gradient-from:rgb(255 255 255 / 0.5) var(--tw-gradient-from-position);--tw-gradient-to:rgb(255 255 255 / 0) var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from), var(--tw-gradient-to)}.to-\[\#22D3EE\]{--tw-gradient-to:#22D3EE var(--tw-gradient-to-position)}.to-\[\#22D3EE\]\/10{--tw-gradient-to:rgb(34 211 238 / 0.1) var(--tw-gradient-to-position)}.to-\[\#22D3EE\]\/5{--tw-gradient-to:rgb(34 211 238 / 0.05) var(--tw-gradient-to-position)}.to-\[\#4F46E5\]{--tw-gradient-to:#4F46E5 var(--tw-gradient-to-position)}.to-\[\#6366F1\]{--tw-gradient-to:#6366F1 var(--tw-gradient-to-position)}.to-transparent{--tw-gradient-to:transparent var(--tw-gradient-to-position)}.object-cover{object-fit:cover}.p-10{padding:2.5rem}.p-4{padding:1rem}.p-6{padding:1.5rem}.p-8{padding:2rem}.px-10{padding-left:2.5rem;padding-right:2.5rem}.px-3{padding-left:0.75rem;padding-right:0.75rem}.px-5{padding-left:1.25rem;padding-right:1.25rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.px-8{padding-left:2rem;padding-right:2rem}.py-1{padding-top:0.25rem;padding-bottom:0.25rem}.py-10{padding-top:2.5rem;padding-bottom:2.5rem}.py-2\.5{padding-top:0.625rem;padding-bottom:0.625rem}.py-20{padding-top:5rem;padding-bottom:5rem}.py-24{padding-top:6rem;padding-bottom:6rem}.py-4{padding-top:1rem;padding-bottom:1rem}.pb-20{padding-bottom:5rem}.pt-32{padding-top:8rem}.text-center{text-align:center}.text-2xl{font-size:1.5rem;line-height:2rem}.text-3xl{font-size:1.875rem;line-height:2.25rem}.text-4xl{font-size:2.25rem;line-height:2.5rem}.text-5xl{font-size:3rem;line-height:1}.text-9xl{font-size:8rem;line-height:1}.text-\[10px\]{font-size:10px}.text-lg{font-size:1.125rem;line-height:1.75rem}.text-sm{font-size:0.875rem;line-height:1.25rem}.text-xl{font-size:1.25rem;line-height:1.75rem}.text-xs{font-size:0.75rem;line-height:1rem}.font-bold{font-weight:700}.font-medium{font-weight:500}.font-semibold{font-weight:600}.uppercase{text-transform:uppercase}.leading-\[1\.1\]{line-height:1.1}.leading-relaxed{line-height:1.625}.tracking-tight{letter-spacing:-0.025em}.tracking-wider{letter-spacing:0.05em}.text-\[\#0F172A\]{--tw-text-opacity:1;color:rgb(15 23 42 / var(--tw-text-opacity, 1))}.text-\[\#22D3EE\]{--tw-text-opacity:1;color:rgb(34 211 238 / var(--tw-text-opacity, 1))}.text-\[\#475569\]{--tw-text-opacity:1;color:rgb(71 85 105 / var(--tw-text-opacity, 1))}.text-\[\#4F46E5\]{--tw-text-opacity:1;color:rgb(79 70 229 / var(--tw-text-opacity, 1))}.text-\[\#818CF8\]{--tw-text-opacity:1;color:rgb(129 140 248 / var(--tw-text-opacity, 1))}.text-slate-300{--tw-text-opacity:1;color:rgb(203 213 225 / var(--tw-text-opacity, 1))}.text-slate-400{--tw-text-opacity:1;color:rgb(148 163 184 / var(--tw-text-opacity, 1))}.text-slate-500{--tw-text-opacity:1;color:rgb(100 116 139 / var(--tw-text-opacity, 1))}.text-white{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity, 1))}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.opacity-10{opacity:0.1}.opacity-20{opacity:0.2}.shadow-2xl{--tw-shadow:0 25px 50px -12px rgb(0 0 0 / 0.25);--tw-shadow-colored:0 25px 50px -12px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.shadow-lg{--tw-shadow:0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);--tw-shadow-colored:0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.shadow-sm{--tw-shadow:0 1px 2px 0 rgb(0 0 0 / 0.05);--tw-shadow-colored:0 1px 2px 0 var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.shadow-xl{--tw-shadow:0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);--tw-shadow-colored:0 20px 25px -5px var(--tw-shadow-color), 0 8px 10px -6px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.shadow-cyan-500\/30{--tw-shadow-color:rgb(6 182 212 / 0.3);--tw-shadow:var(--tw-shadow-colored)}.shadow-indigo-500\/30{--tw-shadow-color:rgb(99 102 241 / 0.3);--tw-shadow:var(--tw-shadow-colored)}.shadow-indigo-500\/50{--tw-shadow-color:rgb(99 102 241 / 0.5);--tw-shadow:var(--tw-shadow-colored)}.shadow-indigo-700\/30{--tw-shadow-color:rgb(67 56 202 / 0.3);--tw-shadow:var(--tw-shadow-colored)}.blur-3xl{--tw-blur:blur(64px);filter:var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)}.blur-\[100px\]{--tw-blur:blur(100px);filter:var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)}.drop-shadow-2xl{--tw-drop-shadow:drop-shadow(0 25px 25px rgb(0 0 0 / 0.15));filter:var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)}.backdrop-blur-md{--tw-backdrop-blur:blur(12px);-webkit-backdrop-filter:var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia);backdrop-filter:var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia)}.transition-all{transition-property:all;transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1);transition-duration:150ms}.transition-colors{transition-property:color, background-color, border-color, fill, stroke, -webkit-text-decoration-color;transition-property:color, background-color, border-color, text-decoration-color, fill, stroke;transition-property:color, background-color, border-color, text-decoration-color, fill, stroke, -webkit-text-decoration-color;transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1);transition-duration:150ms}.transition-opacity{transition-property:opacity;transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1);transition-duration:150ms}.transition-transform{transition-property:transform;transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1);transition-duration:150ms}.delay-100{transition-delay:100ms}.delay-200{transition-delay:200ms}.delay-300{transition-delay:300ms}.duration-300{transition-duration:300ms}.duration-500{transition-duration:500ms}.hover\:rotate-0:hover{--tw-rotate:0deg;transform:translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.hover\:gap-2:hover{gap:0.5rem}.hover\:border-\[\#4F46E5\]\/20:hover{border-color:rgb(79 70 229 / 0.2)}.hover\:bg-\[\#E0E7FF\]:hover{--tw-bg-opacity:1;background-color:rgb(224 231 255 / var(--tw-bg-opacity, 1))}.hover\:bg-\[\#EEF2FF\]:hover{--tw-bg-opacity:1;background-color:rgb(238 242 255 / var(--tw-bg-opacity, 1))}.hover\:bg-white:hover{--tw-bg-opacity:1;background-color:rgb(255 255 255 / var(--tw-bg-opacity, 1))}.hover\:bg-white\/5:hover{background-color:rgb(255 255 255 / 0.05)}.hover\:text-\[\#0F172A\]:hover{--tw-text-opacity:1;color:rgb(15 23 42 / var(--tw-text-opacity, 1))}.hover\:text-\[\#4F46E5\]:hover{--tw-text-opacity:1;color:rgb(79 70 229 / var(--tw-text-opacity, 1))}.hover\:text-white:hover{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity, 1))}.group:hover .group-hover\:bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255 / var(--tw-bg-opacity, 1))}.group:hover .group-hover\:text-\[\#4F46E5\]{--tw-text-opacity:1;color:rgb(79 70 229 / var(--tw-text-opacity, 1))}.group:hover .group-hover\:opacity-20{opacity:0.2}.group:hover .group-hover\:shadow-md{--tw-shadow:0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);--tw-shadow-colored:0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}@media (min-width: 640px){.sm\:flex-row{flex-direction:row}}@media (min-width: 768px){.md\:flex{display:flex}.md\:grid-cols-3{grid-template-columns:repeat(3, minmax(0, 1fr))}.md\:flex-row{flex-direction:row}.md\:text-4xl{font-size:2.25rem;line-height:2.5rem}.md\:text-5xl{font-size:3rem;line-height:1}}@media (min-width: 1024px){.lg\:order-1{order:1}.lg\:order-2{order:2}.lg\:col-span-5{grid-column:span 5 / span 5}.lg\:col-span-7{grid-column:span 7 / span 7}.lg\:h-\[600px\]{height:600px}.lg\:grid-cols-12{grid-template-columns:repeat(12, minmax(0, 1fr))}.lg\:grid-cols-2{grid-template-columns:repeat(2, minmax(0, 1fr))}.lg\:pb-32{padding-bottom:8rem}.lg\:pt-48{padding-top:12rem}.lg\:text-6xl{font-size:3.75rem;line-height:1}.lg\:text-xl{font-size:1.25rem;line-height:1.75rem}}</style><link type="text/css" href="https://fonts.googleapis.com/css2?family=Google+Symbols:opsz,wght,FILL,GRAD,ROND@24,400,0,0,50&amp;icon_names=link" rel="stylesheet"><style id="dyn-img-link-styles-di-script">
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
        box-shadow: 0 1px 2px rgba(0,0,0,0.2);
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
        box-shadow: 0 1px 2px rgba(0,0,0,0.2);
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
        box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        color: white;
      }
    </style></head>
<body class="antialiased">

    <!-- Navigation (Minimalist) -->
    <nav class="fixed top-0 w-full z-50 transition-all duration-300 backdrop-blur-md bg-white/70 border-b border-white/50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <!-- Logo mark -->
                <img 
  src="<?= base_url('assets/icon/logonus.png') ?>" 
  alt="Logo N"
  class="w-8 h-8"
/>
                <span class="font-bold text-xl tracking-tight text-[#0F172A]">NusaShare</span>
            </div>
            <div class="hidden md:flex gap-8 text-sm font-medium text-[#475569]">
                <a href="#solusi" class="hover:text-[#4F46E5] transition-colors">Solusi</a>
                <a href="#cara-kerja" class="hover:text-[#4F46E5] transition-colors">Cara Kerja</a>
                <a href="#untuk-kreator" class="hover:text-[#4F46E5] transition-colors">Kreator</a>
            </div>
            <div>
                <a href="<?= base_url('login') ?>" class="px-5 py-2.5 rounded-full text-sm font-medium text-[#4F46E5] bg-[#EEF2FF] hover:bg-[#E0E7FF] transition-colors">Masuk</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
            
            <!-- Text Content -->
            <div class="max-w-2xl z-10 fade-in-up">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-[#4F46E5]/20 text-[#4F46E5] text-xs font-semibold mb-6 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-[#22D3EE]"></span>
                    Gerbang Masa Depan Kreator Indonesia
                </div>
                
                <h1 class="text-5xl lg:text-6xl font-bold leading-[1.1] tracking-tight text-[#0F172A] mb-6">
                    Masa depan karya kreatif Indonesia <span class="text-gradient">dimulai di sini.</span>
                </h1>
                
                <p class="text-lg lg:text-xl text-[#475569] mb-10 leading-relaxed max-w-lg">
                    Bagikan karya. Bangun reputasi. Dapatkan apresiasi yang nyata dari komunitas yang menghargai proses.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?= base_url('explore') ?>" class="cta-primary px-8 py-4 rounded-full font-semibold text-lg flex items-center justify-center gap-2">
                        Jelajahi Karya
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                    <a href="<?= base_url('register') ?>" class="px-8 py-4 rounded-full font-medium text-[#475569] hover:text-[#0F172A] hover:bg-white transition-all flex items-center justify-center gap-2 border border-transparent hover:border-[#4F46E5]/20">
                        Jadi Kreator
                    </a>
                </div>
            </div> 

            <!-- Visual Content -->
            <div class="relative lg:h-[600px] flex items-center justify-center fade-in-up delay-200">
                <!-- Abstract 3D Element representing creativity/future -->
                <img src="blob:https://1f6z04bmpaihrbd0l895yx8ayb9hl821inkombljm2t5h0l10r-h845251650.scf.usercontent.goog/6da632c1-50c9-48a1-8009-c8db1d52efc5" go-data-src="/gen?prompt=abstract+3d+glassmorphism+shape+floating+indigo+and+cyan+gradient+clean+white+background+minimalist+apple+style+high+quality&amp;aspect=1:1" alt="NusaShare Abstract Art" class="relative z-10 w-full max-w-md animate-float drop-shadow-2xl">
                
                <!-- Background decorative blurs -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[120%] bg-gradient-to-tr from-[#4F46E5]/10 to-[#22D3EE]/10 blur-3xl rounded-full -z-10"></div>
            </div>
        </div>
    </section>

    <!-- Problem Section -->
    <section class="py-24 bg-white relative">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-[#0F172A] mb-16 fade-in-up">Masalahnya bukan kualitas karya.</h2>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Insight 1 -->
                <div class="group p-6 rounded-2xl hover:bg-[#EEF2FF] transition-colors duration-300 fade-in-up delay-100">
                    <div class="w-12 h-12 mx-auto bg-[#F1F5F9] rounded-xl flex items-center justify-center mb-6 group-hover:bg-white group-hover:shadow-md transition-all">
                        <span class="material-symbols-outlined text-[#475569] group-hover:text-[#4F46E5]">graphic_eq</span>
                    </div>
                    <p class="text-[#475569] font-medium leading-relaxed">
                        Karya bagus seringkali tenggelam oleh bisingnya algoritma.
                    </p>
                </div>

                <!-- Insight 2 -->
                <div class="group p-6 rounded-2xl hover:bg-[#EEF2FF] transition-colors duration-300 fade-in-up delay-200">
                    <div class="w-12 h-12 mx-auto bg-[#F1F5F9] rounded-xl flex items-center justify-center mb-6 group-hover:bg-white group-hover:shadow-md transition-all">
                        <span class="material-symbols-outlined text-[#475569] group-hover:text-[#4F46E5] js-replaced-missing-icon">radio_button_unchecked</span>
                    </div>
                    <p class="text-[#475569] font-medium leading-relaxed">
                        Monetisasi yang tidak adil dan seringkali tidak transparan.
                    </p>
                </div>

                <!-- Insight 3 -->
                <div class="group p-6 rounded-2xl hover:bg-[#EEF2FF] transition-colors duration-300 fade-in-up delay-300">
                    <div class="w-12 h-12 mx-auto bg-[#F1F5F9] rounded-xl flex items-center justify-center mb-6 group-hover:bg-white group-hover:shadow-md transition-all">
                        <span class="material-symbols-outlined text-[#475569] group-hover:text-[#4F46E5]">military_tech</span>
                    </div>
                    <p class="text-[#475569] font-medium leading-relaxed">
                        Sulit membangun jalur prestise jangka panjang.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Solution Section -->
    <section id="solusi" class="py-24 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-[#0F172A] mb-4">NusaShare dirancang untuk kreator yang serius.</h2>
                <p class="text-[#475569]">Platform yang mengembalikan nilai pada setiap karya yang dibuat.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Pillar 1 -->
                <div class="surface-card p-8 rounded-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-9xl text-[#4F46E5]">verified</span>
                    </div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#4F46E5] to-[#6366F1] flex items-center justify-center text-white mb-6 shadow-lg shadow-indigo-500/30">
                            <span class="material-symbols-outlined text-2xl">lock_open</span>
                        </div>
                        <h3 class="text-xl font-bold text-[#0F172A] mb-3">Apresiasi Nyata</h3>
                        <p class="text-[#475569] leading-relaxed">
                            Karya bisa diakses secara eksklusif, memberikan nilai lebih daripada sekadar konten yang di-scroll sepintas lalu.
                        </p>
                    </div>
                </div>

                <!-- Pillar 2 -->
                <div class="surface-card p-8 rounded-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-9xl text-[#22D3EE]">show_chart</span>
                    </div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#06B6D4] to-[#22D3EE] flex items-center justify-center text-white mb-6 shadow-lg shadow-cyan-500/30">
                            <span class="material-symbols-outlined text-2xl">account_balance_wallet</span>
                        </div>
                        <h3 class="text-xl font-bold text-[#0F172A] mb-3">Monetisasi Transparan</h3>
                        <p class="text-[#475569] leading-relaxed">
                            Sistem kredit internal yang jelas dan adil, memastikan kreator mendapatkan haknya tanpa potongan tersembunyi.
                        </p>
                    </div>
                </div>

                <!-- Pillar 3 -->
                <div class="surface-card p-8 rounded-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <span class="material-symbols-outlined text-9xl text-[#818CF8]">stars</span>
                    </div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#4338CA] to-[#4F46E5] flex items-center justify-center text-white mb-6 shadow-lg shadow-indigo-700/30">
                            <span class="material-symbols-outlined text-2xl">trending_up</span>
                        </div>
                        <h3 class="text-xl font-bold text-[#0F172A] mb-3">Reputasi &amp; Pertumbuhan</h3>
                        <p class="text-[#475569] leading-relaxed">
                            Setiap interaksi membangun prestise. Bukan manipulasi angka, melainkan pertumbuhan karir yang organik.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Works Section -->
    <section id="karya-unggulan" class="py-24 bg-[#EEF2FF]/50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl md:text-4xl font-bold text-[#0F172A] mb-4">Inspirasi dari Kreator Kami</h2>
                    <p class="text-[#475569]">Lihat beberapa karya terbaru yang sedang tren di NusaShare saat ini.</p>
                </div>
                <a href="<?= base_url('explore') ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white border border-[#4F46E5]/20 text-[#4F46E5] font-semibold hover:bg-[#4F46E5] hover:text-white transition-all shadow-sm">
                    Jelajahi Semua Karya <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php if (!empty($featuredWorks)): ?>
                    <?php foreach ($featuredWorks as $work): ?>
                        <div class="group relative bg-white rounded-2xl overflow-hidden border border-[#4F46E5]/10 shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-1">
                            <!-- Image Container -->
                            <div class="aspect-[4/3] overflow-hidden relative">
                                <img 
                                    src="<?= $work['cover_url'] ?>" 
                                    alt="<?= esc($work['title']) ?>" 
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                    onerror="this.src='https://placehold.co/600x450?text=No+Image'"
                                />
                                <!-- Badge status/type if needed -->
                                <?php if ($work['is_paid']): ?>
                                    <div class="absolute top-3 right-3 px-2 py-1 rounded-lg bg-[#4F46E5] text-white text-[10px] font-bold uppercase tracking-wider backdrop-blur-md shadow-lg">
                                        Premium
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Content -->
                            <div class="p-5">
                                <h3 class="font-bold text-[#0F172A] mb-1 line-clamp-1 group-hover:text-[#4F46E5] transition-colors">
                                    <?= esc($work['title']) ?>
                                </h3>
                                <div class="flex items-center gap-2 text-sm text-[#475569]">
                                    <span class="material-symbols-outlined text-xs text-[#22D3EE]">person</span>
                                    <span class="font-medium"><?= esc($work['creator_name']) ?></span>
                                </div>
                            </div>

                            <!-- Overlay Link -->
                            <a href="<?= base_url('works/' . $work['id']) ?>" class="absolute inset-0 z-10" aria-label="View Work"></a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Minimalist placeholder if no data -->
                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <div class="bg-white rounded-2xl p-6 border border-dashed border-[#4F46E5]/20 flex flex-col items-center justify-center text-center opacity-60">
                            <div class="w-12 h-12 rounded-full bg-[#F1F5F9] flex items-center justify-center mb-4 text-[#475569]">
                                <span class="material-symbols-outlined">image_not_supported</span>
                            </div>
                            <p class="text-xs text-[#475569]">Karya segera hadir</p>
                        </div>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="cara-kerja" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-[#0F172A] mb-8">Cara kerjanya sederhana.</h2>
                    
                    <div class="space-y-8">
                        <div class="flex gap-6">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full border-2 border-[#4F46E5] text-[#4F46E5] font-bold flex items-center justify-center">1</div>
                            <div>
                                <h4 class="text-lg font-semibold text-[#0F172A]">Kreator mengunggah karya</h4>
                                <p class="text-[#475569]">Upload karya terbaikmu dengan pengaturan akses yang fleksibel.</p>
                            </div>
                        </div>
                        <div class="flex gap-6">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full border-2 border-[#4F46E5]/60 text-[#4F46E5] font-bold flex items-center justify-center">2</div>
                            <div>
                                <h4 class="text-lg font-semibold text-[#0F172A]">Pengunjung menikmati preview</h4>
                                <p class="text-[#475569]">Audiens melihat cuplikan berkualitas untuk menilai relevansi.</p>
                            </div>
                        </div>
                        <div class="flex gap-6">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full border-2 border-[#4F46E5]/30 text-[#4F46E5] font-bold flex items-center justify-center">3</div>
                            <div>
                                <h4 class="text-lg font-semibold text-[#0F172A]">Akses penuh dibuka</h4>
                                <p class="text-[#475569]">Apresiasi diberikan untuk membuka akses penuh ke karya.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="relative">
                    <!-- Visual representation of the flow -->
                    <div class="absolute inset-0 bg-gradient-to-r from-[#4F46E5]/5 to-[#22D3EE]/5 rounded-3xl transform rotate-3"></div>
                    <img style="aspect-ratio: 4/3" src="blob:https://1f6z04bmpaihrbd0l895yx8ayb9hl821inkombljm2t5h0l10r-h845251650.scf.usercontent.goog/26a1bae7-ad2a-4424-9d73-c6b0681c0526" go-data-src="/gen?prompt=clean+minimalist+ui+mockup+isometric+view+of+uploading+digital+art+and+unlocking+content+white+background+soft+shadows&amp;aspect=4:3" alt="NusaShare Workflow" class="relative rounded-3xl shadow-2xl border border-white z-10">
                </div>
            </div>
        </div>
    </section>

    <!-- Segmentations (Creators & Fans) -->
    <section id="untuk-kreator" class="py-24 bg-[#EEF2FF] relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-white/50 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <!-- For Creators -->
            <div class="grid lg:grid-cols-12 gap-12 mb-24 items-center">
                <div class="lg:col-span-7 order-2 lg:order-1">
                    <div class="surface-card p-10 rounded-3xl bg-white shadow-xl">
                        <span class="text-[#4F46E5] font-semibold tracking-wider text-sm uppercase mb-2 block">Untuk Kreator</span>
                        <h2 class="text-3xl font-bold text-[#0F172A] mb-6">Untuk kreator yang ingin melangkah lebih jauh.</h2>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[#22D3EE] mt-0.5">check_circle</span>
                                <span class="text-[#475569]"><strong>Kontrol penuh</strong> atas hak cipta, distribusi, dan harga karya.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[#22D3EE] mt-0.5">check_circle</span>
                                <span class="text-[#475569]"><strong>Statistik mendalam</strong> tentang siapa yang mengapresiasi karyamu.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[#22D3EE] mt-0.5">check_circle</span>
                                <span class="text-[#475569]"><strong>Lingkungan sehat</strong> tanpa drama, fokus pada esensi kreativitas.</span>
                            </li>
                        </ul>
                        
                        <a href="#" class="inline-flex items-center text-[#4F46E5] font-semibold hover:gap-2 transition-all">
                            Pelajari fitur kreator <span class="material-symbols-outlined text-sm ml-1">arrow_forward</span>
                        </a>
                    </div>
                </div>
                <div class="lg:col-span-5 order-1 lg:order-2">
                    <img style="aspect-ratio: 1/1" src="blob:https://1f6z04bmpaihrbd0l895yx8ayb9hl821inkombljm2t5h0l10r-h845251650.scf.usercontent.goog/578b9ab6-9bf1-41e0-99c1-ece99a67b3c3" go-data-src="/gen?prompt=close+up+of+creative+professional+workspace+digital+tablet+stylus+clean+lighting+indigo+accent+minimalist&amp;aspect=1:1" class="rounded-3xl shadow-lg rotate-2 hover:rotate-0 transition-transform duration-500 w-full object-cover" alt="Creator Workspace">
                </div>
            </div>

            <!-- For Fans -->
            <div class="grid lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-5">
                    <img src="blob:https://1f6z04bmpaihrbd0l895yx8ayb9hl821inkombljm2t5h0l10r-h845251650.scf.usercontent.goog/084fcf37-3a6d-4ee3-9c85-2d09d0f42999" go-data-src="/gen?prompt=person+relaxing+looking+at+tablet+screen+displaying+beautiful+digital+art+cozy+atmosphere+clean+style&amp;aspect=1:1" class="rounded-3xl shadow-lg -rotate-2 hover:rotate-0 transition-transform duration-500 w-full object-cover" alt="Enjoying Content">
                </div>
                <div class="lg:col-span-7">
                    <div class="p-8">
                        <span class="text-[#4F46E5] font-semibold tracking-wider text-sm uppercase mb-2 block">Untuk Penikmat Karya</span>
                        <h2 class="text-3xl font-bold text-[#0F172A] mb-6">Untuk penikmat yang menghargai proses.</h2>
                        
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#4F46E5] mt-2.5"></span>
                                <span class="text-[#475569]">Akses ke karya berkualitas tinggi yang terkurasi.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#4F46E5] mt-2.5"></span>
                                <span class="text-[#475569]">Mendukung kreator favorit secara langsung dan transparan.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#4F46E5] mt-2.5"></span>
                                <span class="text-[#475569]">Pengalaman menikmati konten yang bebas distraksi iklan mengganggu.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust & Direction -->
    <section class="py-20 bg-white border-t border-[#EEF2FF]">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <h3 class="text-2xl font-bold text-[#0F172A] mb-6">Dibangun secara bertahap.</h3>
            <p class="text-[#475569] mb-4">
                Kami fokus pada kualitas, bukan mengejar kuantitas pengguna semata. 
                Platform ini akan terus dikembangkan bersama masukan dari komunitas kreator pertama kami.
            </p>
            <p class="text-[#475569] text-sm">
                Berakar di Indonesia, berorientasi global.
            </p>
            
            <!-- Subtle trust indicator -->
            <div class="mt-8 flex justify-center gap-2">
                <div class="w-12 h-1 bg-gray-200 rounded-full"></div>
                <div class="w-12 h-1 bg-gray-200 rounded-full"></div>
                <div class="w-12 h-1 bg-[#4F46E5] rounded-full"></div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-24 bg-[#0F172A] relative overflow-hidden text-center">
    <!-- Abstract background glow -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-[#4F46E5] opacity-20 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="max-w-4xl mx-auto px-6 relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-8 tracking-tight">
            Mulai dari satu karya.
        </h2>

        <p class="text-slate-400 mb-10 text-lg">
            Bergabunglah dengan gelombang pertama kreator di NusaShare.
        </p>
        
        <!-- Main CTA -->
        <div class="flex flex-col sm:flex-row justify-center gap-5 mb-6">
            <a href="<?= base_url('login') ?>" class="cta-primary px-10 py-4 rounded-full font-bold text-lg text-white shadow-lg shadow-indigo-500/50">
                Masuk ke NusaShare
            </a>

            <a href="<?= base_url('register') ?>" class="px-10 py-4 rounded-full font-medium text-slate-300 border border-slate-700 hover:bg-white/5 hover:text-white transition-all">
                Daftar sebagai kreator
            </a>
        </div>

        <!-- Explore CTA (tertiary) -->
        <a href="<?= base_url('explore') ?>" class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition">
            Jelajahi karya kreator
            <span class="text-lg">→</span>
        </a>
    </div>
</section>

    <!-- Simple Footer -->
    <footer class="bg-[#0F172A] border-t border-slate-800 py-10">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2 text-white font-semibold">
            <img 
  src="<?= base_url('assets/icon/logonus.png') ?>" 
  alt="Logo N"
  class="w-8 h-8"
/>                NusaShare
            </div>
            <p class="text-slate-500 text-sm">© <?php echo date('Y') ?> NusaShare. All rights reserved.</p>
        </div>
    </footer>

    <!-- Full Image URL List -->
    <!--
    "/gen?prompt=abstract+3d+glassmorphism+shape+floating+indigo+and+cyan+gradient+clean+white+background+minimalist+apple+style+high+quality&aspect=1:1"
    "/gen?prompt=clean+minimalist+ui+mockup+isometric+view+of+uploading+digital+art+and+unlocking+content+white+background+soft+shadows&aspect=4:3"
    "/gen?prompt=close+up+of+creative+professional+workspace+digital+tablet+stylus+clean+lighting+indigo+accent+minimalist&aspect=1:1"
    "/gen?prompt=person+relaxing+looking+at+tablet+screen+displaying+beautiful+digital+art+cozy+atmosphere+clean+style&aspect=1:1"
    -->



<script src="<?= base_url('assets/js/pwa.js') ?>"></script>
</body></html>