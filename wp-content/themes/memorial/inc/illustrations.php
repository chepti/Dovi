<?php
/**
 * איורי SVG מצוירים (רב־שכבתיים עם קו מתאר): ספסל מתחת לאלון מול גבעות, וגלי גבעות לפוטר.
 */
if (!defined('ABSPATH')) {
    exit;
}

/** הסצנה: ספסל מתחת לאלון, שקיעה וגבעות */
function memorial_illus_bench($class = '')
{
    $ink = '#1f2b45';
    ob_start(); ?>
<svg class="illus illus--bench <?php echo esc_attr($class); ?>" viewBox="0 0 600 380" preserveAspectRatio="xMidYMid slice" role="img" aria-label="איור: ספסל מתחת לעץ אלון מול נוף של גבעות ושקיעה" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="ib-sky" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#ffd9a8"/>
      <stop offset=".55" stop-color="#ffe9c9"/>
      <stop offset="1" stop-color="#fff4de"/>
    </linearGradient>
    <linearGradient id="ib-sun" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#ffb347"/>
      <stop offset="1" stop-color="#f06a3a"/>
    </linearGradient>
  </defs>
  <rect width="600" height="380" fill="url(#ib-sky)"/>
  <circle cx="430" cy="150" r="84" fill="#ffc474" opacity=".28"/>
  <circle cx="430" cy="150" r="64" fill="#ffb347" opacity=".35"/>
  <circle cx="430" cy="150" r="46" fill="url(#ib-sun)" stroke="<?php echo $ink; ?>" stroke-width="2.5"/>
  <g fill="none" stroke="<?php echo $ink; ?>" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M488 86q8-9 16 0q8-9 16 0"/>
    <path d="M522 112q6-7 12 0q6-7 12 0"/>
    <path d="M356 70q6-7 12 0q6-7 12 0"/>
  </g>
  <g stroke-linejoin="round" stroke-linecap="round" stroke="<?php echo $ink; ?>" stroke-width="2.5">
    <path d="M0 250C60 205 130 200 200 228C270 255 330 215 400 205C470 195 540 225 600 215L600 380L0 380Z" fill="#d8e8c8"/>
    <path d="M0 285C70 245 150 250 230 275C310 300 380 255 460 252C520 250 565 268 600 262L600 380L0 380Z" fill="#a8d093"/>
    <path d="M0 332C90 302 190 298 280 308C380 320 470 298 600 314L600 380L0 380Z" fill="#6aa85a"/>
  </g>
  <g fill="none" stroke="<?php echo $ink; ?>" stroke-width="1.6" stroke-linecap="round" opacity=".35">
    <path d="M40 350q14-6 28 0M120 362q16-6 32 0M470 352q16-6 32 0M520 366q14-5 28 0"/>
  </g>
  <!-- אלון -->
  <g stroke="<?php echo $ink; ?>" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round">
    <path d="M132 322C138 294 134 272 126 244C148 252 164 252 180 240C172 268 174 298 188 324Z" fill="#8a5a33"/>
    <path d="M150 300C156 286 158 274 156 262" fill="none" stroke-width="1.8" opacity=".5"/>
    <circle cx="106" cy="204" r="44" fill="#3f8a46"/>
    <circle cx="160" cy="176" r="54" fill="#3f8a46"/>
    <circle cx="212" cy="206" r="44" fill="#3f8a46"/>
    <circle cx="136" cy="228" r="40" fill="#3f8a46"/>
    <circle cx="190" cy="234" r="34" fill="#3f8a46"/>
  </g>
  <g fill="#62b05c" opacity=".85">
    <circle cx="150" cy="160" r="22"/>
    <circle cx="98" cy="194" r="16"/>
    <circle cx="206" cy="196" r="16"/>
    <circle cx="130" cy="218" r="14"/>
  </g>
  <g fill="#2e6e38" opacity=".55">
    <circle cx="178" cy="238" r="12"/>
    <circle cx="120" cy="240" r="10"/>
  </g>
  <!-- ספסל -->
  <ellipse cx="300" cy="350" rx="76" ry="7" fill="<?php echo $ink; ?>" opacity=".18"/>
  <g stroke="<?php echo $ink; ?>" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" transform="translate(246 282)">
    <path d="M14 0L12 36M98 0L100 36" fill="none"/>
    <rect x="0" y="0" width="112" height="10" rx="3" fill="#dba35f"/>
    <rect x="0" y="14" width="112" height="9" rx="3" fill="#dba35f"/>
    <rect x="-2" y="34" width="116" height="11" rx="3" fill="#e8b878"/>
    <path d="M10 45L7 68M104 45L107 68" fill="none"/>
  </g>
</svg>
<?php
    return ob_get_clean();
}

/** גלי גבעות לקצה הפוטר: צבע התחתית נקבע ב-$base */
function memorial_illus_wave($base = '#1f2b45', $class = '')
{
    $ink = '#1f2b45';
    ob_start(); ?>
<svg class="illus illus--wave <?php echo esc_attr($class); ?>" viewBox="0 0 1440 150" preserveAspectRatio="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
  <g stroke="<?php echo $ink; ?>" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round">
    <path d="M0 78C220 18 470 112 720 64C960 18 1200 104 1440 54L1440 150L0 150Z" fill="#8fc27d"/>
    <path d="M0 104C290 56 540 126 810 92C1070 60 1260 112 1440 86L1440 150L0 150Z" fill="#5a9b52"/>
    <path d="M0 128C330 96 640 140 960 116C1180 100 1320 122 1440 110L1440 150L0 150Z" fill="<?php echo esc_attr($base); ?>"/>
  </g>
</svg>
<?php
    return ob_get_clean();
}

/** קישוט: שמש מצוירת (לשימוש חופשי בתבניות) */
function memorial_illus_sun($class = '')
{
    ob_start(); ?>
<svg class="illus illus--sun <?php echo esc_attr($class); ?>" viewBox="0 0 120 120" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
  <g stroke="#1f2b45" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="60" cy="60" r="30" fill="#ffb347"/>
    <circle cx="60" cy="60" r="18" fill="#f06a3a"/>
    <path d="M60 10v12M60 98v12M10 60h12M98 60h12M24.6 24.6l8.5 8.5M86.9 86.9l8.5 8.5M24.6 95.4l8.5-8.5M86.9 33.1l8.5-8.5" fill="none"/>
  </g>
</svg>
<?php
    return ob_get_clean();
}
