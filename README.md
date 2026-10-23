# Memorial: אתר זיכרון משפחתי לוורדפרס

[עברית](#עברית) · [English](#english)

---

## עברית

**Memorial** הוא קוד פתוח (GPL-2.0-or-later) לבניית אתר זיכרון משפחתי בעברית, מימין לשמאל, על וורדפרס, בלי בונה עמודים. הוא נולד מאתר הזיכרון של משפחה אחת, ונוקה והוכלל כדי שגם משפחות אחרות יוכלו להקים אתר כזה בלי מתכנת.

הפרויקט כולל שני חלקים שעובדים יחד:

| חלק | תיקייה | מה הוא עושה |
|---|---|---|
| תוסף **Memorial Core** | `wp-content/plugins/memorial-core` | סוגי תוכן (סיפורים, מקומות הנצחה, יצירות), פרקי חיים וערכים שמגדירים מהניהול, טפסים עם אישור לפני פרסום, ניהול תמונות, SEO ו-JSON-LD, `llms.txt`, מפת אתר, ציר זמן |
| ערכת נושא **Memorial** | `wp-content/themes/memorial` | העיצוב: דף בית, עמודי תוכן, מפה (Leaflet), גלריות עם הגדלה, סרגל נגישות, פונטים מקומיים, ללא תלות בשירותים חיצוניים (פרט לאריחי המפה) |

### מה יש באתר

- **סיפורים**: חברים ומשפחה כותבים סיפור קצר (עם תמונות). הוא ממתין לאישור, ומתפרסם רק אחרי שהמשפחה בדקה אותו. סינון לפי פרק חיים וערך, חיפוש, סיפור היום, סיפור אקראי.
- **פרקי חיים וערכים**: טקסונומיות שמגדירים בעצמכם (שם, צבע, אייקון, תיאור, סדר; לערכים גם ציטוט).
- **מקומות הנצחה**: ספסלים, נטיעות, פינות זיכרון. כל מקום עם תמונות, איך מגיעים, סרטון (אופציונלי), ומפה. קישור ממפות גוגל ממלא קואורדינטות אוטומטית. מבקרים יכולים לשלוח תמונה מהביקור.
- **יצירות**: ציורים, מתקנים, כתבים, מסודרים לפי סוג יצירה.
- **אלבום תמונות**: האלבומים מוגדרים על קבצי המדיה.
- **הספדים והנצחה**: פוסטים רגילים של וורדפרס, מקובצים לפי קטגוריה.
- **ציר זמן**: שורה לכל תחנה, בהגדרות האתר.
- **באנר אזכרה**: מוצג אוטומטית שלושה שבועות לפני יום הזיכרון העברי ועד שבוע אחריו (מחושב מתאריך הפטירה).
- **נגישות**: סרגל נגישות, ניווט במקלדת, ניגודיות, טקסט חלופי ברירת מחדל, עיצוב RTL מלא.
- **SEO**: כותרות, תיאורים, Open Graph, JSON-LD, מפת אתר, `robots.txt`, `llms.txt`.
- **ניהול תמונות**: מסך אחד לעריכת כיתוב, טקסט חלופי ואלבום לכל תמונה בספרייה, עם שמירה בבת אחת וניקוי מטמון LiteSpeed.

### דרישות

- וורדפרס 6.3 ומעלה (הערכה משתמשת ב-`strategy` ל-`wp_enqueue_script`).
- PHP 7.4 ומעלה, עם התוספים `mbstring` ו-`calendar` (ל-`calendar`: חישוב התאריך העברי; בלעדיו באנר האזכרה פשוט לא יוצג).
- שליחת מייל תקינה מהשרת (מומלץ תוסף SMTP), כדי לקבל התראות על סיפורים חדשים.

### התקנה

1. הורידו את הריפו (Code > Download ZIP) או שכפלו אותו.
2. העתיקו את התיקייה `wp-content/plugins/memorial-core` אל `wp-content/plugins` באתר שלכם, ואת `wp-content/themes/memorial` אל `wp-content/themes`. אפשר גם לדחוס כל תיקייה לקובץ ZIP בנפרד (התיקייה צריכה להיות בשורש הזיפ) ולהעלות דרך לוח הניהול.
3. בניהול: **תוספים** > הפעלת **Memorial Core**. אחר כך **מראה** > **ערכות נושא** > הפעלת **Memorial**.
4. ב**הגדרות** > **אתר הזיכרון** לחצו **יצירת עמודים וערכי ברירת מחדל**. זה יוצר את העמודים הבסיסיים ופרקי חיים וערכים לדוגמה. לא דורס כלום.
5. ב**הגדרות** > **קריאה**: בחרו שדף הבית מציג "הדפים האחרונים שלך" או עמוד סטטי כרצונכם (תבנית דף הבית נטענת בכל מקרה).
6. **הגדרות** > **קישורי קבע**: בחרו "שם הפוסט" ולחצו שמירה (פעם אחת, כדי שהכתובות יעבדו).

### הגדרה ראשונית

ב**הגדרות** > **אתר הזיכרון**:

- שם מלא, שם פרטי, תואר או ברכה (למשל ז״ל), שנת לידה ותאריך פטירה.
- תמונה ראשית (דף הבית ושיתוף ברשתות). בלי תמונה מוצג איור ברירת מחדל.
- פסקת פתיחה, תפקידים, ומשפט קצר לפוטר ולתיאור האתר.
- ציר זמן (אופציונלי), בפורמט `מתי | כותרת | טקסט קצר | מזהה פרק`.
- מיילים להתראות (ראו בהמשך) ומזהה Google Analytics (אופציונלי).

#### פרקי חיים

**סיפורים** > **פרקי חיים**. לכל פרק: שם, מזהה (slug באנגלית), תיאור, **צבע**, **אייקון** ו**סדר הצגה**. הפרקים מופיעים בדף הבית, בסינון הסיפורים ובציר הזמן. אפשר למחוק את הדוגמאות ולהגדיר פרקים משלכם.

#### ערכים

**סיפורים** > **ערכים**. אותו דבר, ובנוסף **ציטוט** ו**מי אמר**. דוגמה: ערך "הקשבה" עם ציטוט ממחברת או מדברי חבר. הערכים מוצגים בדף הבית ובעמוד "אודות".

#### אלבומים

**מדיה** > **אלבומים** (או **ניהול תמונות** > "ניהול רשימת האלבומים"): יוצרים אלבום ונותנים לו שם, למשל "ילדות" או "משפחה". אם יוצרים אלבום שה-slug שלו זהה ל-slug של פרק חיים, התמונות שלו יופיעו גם בעמוד אותו פרק כפס תמונות.

### איך מוסיפים תוכן

| מה | איפה | הערות |
|---|---|---|
| סיפור | **סיפורים** > סיפור חדש | שם הכותב, הקשר, פרק וערך, תמונה ראשית ותמונות נוספות. סיפורים שנשלחו מהאתר מופיעים כ"ממתין לבדיקה" |
| מקום הנצחה | **מקומות הנצחה** > מקום חדש | הדביקו קישור ממפות גוגל והקואורדינטות יתמלאו בשמירה. "מיקום משוער" מציג עיגול במקום סיכה |
| יצירה | **יצירות** > יצירה חדשה | כותרת, תמונה מייצגת, סוג יצירה (נוצר בסיווג "סוגי יצירה") |
| הספד או כתבה | **פוסטים** > הוספה | קטגוריות מגדירות את הקבצים בעמוד "הספדים והנצחה". פוסט שתוכנו קישור בלבד יוצג ככרטיס קישור |
| תמונות לאלבום | **מדיה** > הוספה, ואז **ניהול תמונות** | כיתוב, טקסט חלופי ואלבום לכל תמונה במקום אחד |

#### ניהול תמונות

**מדיה** > **ניהול תמונות**. טבלה עם תמונה ממוזערת, שדה כיתוב, שדה טקסט חלופי (alt) ובחירת אלבום לכל תמונה. אפשר לסנן לפי אלבום, "ללא אלבום" ו"בלי טקסט חלופי", ולחפש. לחצו **שמירת שינויים** כדי לשמור את כל השורות יחד. אחרי השמירה התוסף מפעיל `do_action('litespeed_purge_all')` אם תוסף LiteSpeed Cache פעיל, כדי שהשינויים יופיעו מיד בעמודים.

### התראות במייל

כשמישהו שולח סיפור, תמונה או משוב, נשלח מייל בעברית (מיושר לימין) עם קישור לעריכה ולפרסום.

- **מיילים שמקבלים סיפורים ותמונות**: ב**הגדרות** > **אתר הזיכרון**, כתובות מופרדות בפסיק. אם ריק, ההתראות יגיעו למייל מנהל האתר.
- **מייל למשוב** ו**מייל ליצירת קשר** (מופיע בהצהרת הנגישות והפרטיות).
- **כתובת השולח**: מומלץ כתובת בדומיין של האתר (למשל `noreply@yourdomain.org`), ותוסף SMTP, כדי שההודעות לא ייפלו לספאם.

הגנות מובנות: שדה מלכודת (honeypot), הגבלת קצב לכתובת IP, בדיקת סוג וגודל קבצים (תמונות עד 12MB), סינון ספאם בסיסי, ואישור הסכמה לפרסום. שום תוכן שנשלח לא מתפרסם לפני אישור.

### כתובות העמודים

הערכה מזהה עמודים לפי ה-slug שלהם ומחילה עליהם את התבנית המתאימה:

| slug | תוכן |
|---|---|
| `stories` | כל הסיפורים, סינון וחיפוש, ציר זמן |
| `about` | אודות (תוכן העמוד עצמו, ציר זמן וערכים) |
| `places` | מקומות הנצחה: מפה, רשימה וטופס ביקור |
| `creations` | יצירות |
| `photos` | אלבום תמונות |
| `share` | שליחת סיפור או תמונה |
| `tributes` | הספדים והנצחה |
| `accessibility`, `privacy` | הצהרת נגישות ומדיניות פרטיות (נוצרות עם נוסח התחלתי שכדאי לעבור עליו) |

תפריט ראשי: בלי תפריט שהוגדר, הערכה מציגה אוטומטית קישורים לעמודים שקיימים. אפשר גם להגדיר תפריט משלכם במיקום "תפריט ראשי".

### פיתוח ושינויים

- כל הפונקציות, האפשרויות, ה-meta וה-handles משתמשים בקידומת אחת: `memorial_` / `memorial-`. האפשרויות נשמרות ב-`memorial_options`.
- הטקסטים נמצאים בקבצי התבנית (`wp-content/themes/memorial`) ובעברית. הטקסטים כתובים בלשון זכר כברירת מחדל (למשל "מה שיצר"); מי שצריך לשון נקבה או ניסוח אחר, יערוך את התבניות. את ניסוח הבאנר ("לפטירתו של") מגדירים בהגדרות.
- המזהה הפנימי של "מקומות הנצחה" נשאר `bench` (ושם ה-meta `bench_*`) מטעמי תאימות.
- אין סיסמאות, מפתחות או מיילים בקוד. כל ההגדרות נשמרות בבסיס הנתונים של האתר שלכם.
- בדיקות: `php -l` על כל קבצי ה-PHP.

### בקשה קטנה: קרדיט

הערכה והתוסף חופשיים לשימוש ולשינוי. נשמח אם תשאירו בפוטר את הקרדיט **"חולמים תקשוב"** עם הקישור ל-**https://chepti.com**. זה מה שמאפשר להמשיך לפתח ולשתף. הקרדיט מופיע בפוטר כברירת מחדל ואפשר להשאיר אותו כמו שהוא.

### רישיון

GPL-2.0-or-later (ראו `LICENSE`). פונטים, מפה ואייקונים ברישיונות פתוחים נפרדים, ראו `CREDITS.md`.

---

## English

**Memorial** is an open-source (GPL-2.0-or-later) WordPress theme and plugin for building a Hebrew, right-to-left family memorial website without a page builder. It started as the memorial site of one family and was cleaned up and generalised so other families can build the same thing without a developer.

Two parts that work together:

| Part | Folder | What it does |
|---|---|---|
| **Memorial Core** plugin | `wp-content/plugins/memorial-core` | Content types (stories, memorial places, creations), life chapters and values configured from the admin, submission forms with moderation, image manager, SEO and JSON-LD, `llms.txt`, sitemap, timeline |
| **Memorial** theme | `wp-content/themes/memorial` | Front end: home page, content pages, Leaflet map, lightbox galleries, accessibility toolbar, local fonts, no third-party dependencies except map tiles |

### Features

- **Stories**: friends and family submit short stories (with photos). They wait for approval and are published only after the family reviews them. Filter by chapter and value, search, story of the day, random story.
- **Chapters and values**: taxonomies you define yourself (name, colour, icon, description, order; values also take a quote).
- **Memorial places**: benches, trees, memorial corners. Photos, directions, optional video and a map. Pasting a Google Maps link fills the coordinates. Visitors can send a photo from their visit.
- **Creations** grouped by type, **photo albums** (defined on media files), **tributes** (regular posts grouped by category), and a configurable **timeline**.
- **Yahrzeit banner**: shown automatically from three weeks before the Hebrew-calendar anniversary until a week after (derived from the date of passing).
- **Accessibility**: toolbar, keyboard navigation, contrast, default alt text, full RTL.
- **SEO**: titles, descriptions, Open Graph, JSON-LD, sitemap, `robots.txt`, `llms.txt`.
- **Image manager**: one screen to edit caption, alt text and album for every library image, saved in one go, purging the LiteSpeed cache afterwards.

### Requirements

- WordPress 6.3+ (script `strategy` support).
- PHP 7.4+ with `mbstring` and `calendar` (the latter computes the Hebrew date; without it the yahrzeit banner is simply hidden).
- Working outgoing mail (an SMTP plugin is recommended) to receive new-submission notifications.

### Installation

1. Download this repo (Code > Download ZIP) or clone it.
2. Copy `wp-content/plugins/memorial-core` into your site's `wp-content/plugins`, and `wp-content/themes/memorial` into `wp-content/themes`. You can also zip each folder separately (the folder must be at the root of the zip) and upload through the admin.
3. In the admin: **Plugins** > activate **Memorial Core**; then **Appearance** > **Themes** > activate **Memorial**.
4. Go to **Settings** > **אתר הזיכרון** (Memorial site) and press **Create pages and default values** (creates the basic pages plus sample chapters and values; never overwrites anything).
5. **Settings** > **Permalinks**: choose "Post name" and save once.

### Setup

Under **Settings** > **אתר הזיכרון**: full name, first name, honorific, birth year, date of passing, hero image, intro text, roles, a short footer sentence, optional timeline (`when | title | short text | chapter slug` per line), notification emails and an optional Google Analytics ID.

- **Chapters**: *Stories > Life chapters*. Name, slug, description, colour, icon, order.
- **Values**: *Stories > Values*. Same, plus a quote and its source.
- **Albums**: *Media > Albums*. Give an album the same slug as a chapter to show its photos as a strip on that chapter's page.

### Adding content

Stories, memorial places and creations each have their own menu. Tributes are regular posts (categories become sections on the tributes page). Photos go to the media library; then use **Media > Image manager** to set captions, alt text and albums in bulk. The table supports filtering by album, "no album" and "missing alt text", and saving everything with one button.

### Email notifications

Submissions send a Hebrew, right-aligned HTML email with an edit/publish link. Configure recipients (comma separated; defaults to the site admin email), the feedback and contact addresses, and the sender address (use an address on your own domain and an SMTP plugin to avoid spam folders). Built-in protections: honeypot, per-IP rate limiting, file type and size checks (12MB images), basic spam filtering, and a publishing consent checkbox. Nothing submitted is published without approval.

### Page slugs

The theme applies templates by page slug: `stories`, `about`, `places`, `creations`, `photos`, `share`, `tributes` (plus `accessibility` and `privacy`, created with starter text that you should review). Without a custom menu, the header and footer link automatically to the pages that exist.

### Notes for developers

- One prefix everywhere: `memorial_` / `memorial-` (functions, options, meta, admin-post actions, script handles). Options live in `memorial_options`.
- Template strings are Hebrew, in the theme files, and default to masculine grammatical forms; edit the templates for other phrasing. The banner wording is a setting.
- The internal post type for memorial places stays `bench` (meta keys `bench_*`) for compatibility.
- No passwords, keys or personal emails are in the code; all settings are stored in your site's database.
- Check syntax with `php -l` on all PHP files.

### A small request: credit

Free to use and modify. Please keep the **"חולמים תקשוב"** credit in the footer, linking to **https://chepti.com**. It ships that way by default.

### License

GPL-2.0-or-later (see `LICENSE`). Fonts, map library and icons have their own open licenses, see `CREDITS.md`.
