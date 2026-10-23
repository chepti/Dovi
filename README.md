# Memorial: אתר זיכרון משפחתי לוורדפרס

[עברית](#עברית) · [English](#english)

---

## עברית

**Memorial** הוא קוד פתוח וחינמי (GPL-2.0-or-later) להקמת אתר זיכרון משפחתי בעברית, מימין לשמאל, על וורדפרס. הוא נולד מאתר הזיכרון של משפחה אחת, ונוקה והוכלל כדי שגם משפחות אחרות יוכלו להקים אתר כזה, בלי לדעת לתכנת.

> **חשוב לדעת מראש:** זה עובד על **וורדפרס בלבד**. אי אפשר להעלות את הקבצים לאתר סטטי, ל-Wix, לסקווארספייס או לשירותי "בנה אתר" אחרים. הסבר מלא על אחסון בהמשך.

### מה מקבלים

- **סיפורים**: חברים ומשפחה כותבים סיפור קצר (אפשר עם תמונות) דרך טופס באתר. הסיפור **לא מתפרסם** עד שמישהו מהמשפחה קורא ולוחץ "פרסום".
- **פרקי חיים וערכים**: קטגוריות שאתם מגדירים (ילדות, שירות, משפחה... חיבור, נתינה...), כל אחת עם צבע ואייקון.
- **מקומות הנצחה**: ספסלים, נטיעות, פינות זיכרון, על מפה, עם תמונות ודרכי הגעה. מבקרים יכולים לשלוח תמונה מהביקור.
- **יצירות**: ציורים, מתקנים, כתבים של האדם.
- **אלבום תמונות**, **הספדים והנצחה**, **ציר זמן**.
- **באנר אזכרה** שמופיע לבד שלושה שבועות לפני יום הזיכרון העברי.
- **נגישות** (סרגל נגישות, ניווט במקלדת), **חיפוש בגוגל** (SEO), מסך **ניהול תמונות** נוח.

---

### חלק א: לפני שמתחילים, מה צריך?

כדי שאתר יהיה באינטרנט צריך שלושה דברים. אל תיבהלו, זה פשוט:

1. **אחסון (Hosting)**: מחשב של חברה שמחזיק את האתר פתוח 24/7.
2. **וורדפרס**: תוכנת בניית האתר. כמעט כל חברת אחסון מתקינה אותה בלחיצה אחת.
3. **דומיין** (כתובת, למשל `shem-hamenuach.org.il`): לא חובה, אבל מומלץ מאוד. בלי דומיין הכתובת תהיה ארוכה ומוזרה.

#### האם אפשר בחינם? תשובה ישרה

האתר הזה בנוי על **וורדפרס**, שדורש שרת עם PHP ובסיס נתונים (MySQL). לכן:

| מה | מתאים? | למה |
|---|---|---|
| Supabase, Firebase, Netlify, Vercel, GitHub Pages, Cloudflare Pages | **לא** | אלה שירותים לאתרים סטטיים או לאפליקציות. אין בהם PHP ו-MySQL, ווורדפרס לא ירוץ שם |
| WordPress.com בתוכנית החינמית | **לא** | לא מאפשרת להעלות תוספים וערכות נושא משלכם. זה אפשרי רק בתוכניות בתשלום גבוהות יחסית |
| אחסון וורדפרס **חינמי** (למשל InfinityFree ודומיו) | **אפשרי, אבל לא מומלץ** | יש כאלה שנותנים PHP ו-MySQL בחינם עם כתובת משנה. בדרך כלל יש מגבלות: האתר איטי, לפעמים נופל או מושעה, שליחת מייל (ההתראות על סיפורים חדשים) לרוב לא עובדת, ולפעמים יש פרסומות. מי שמעלה סיפורים ותמונות של יקיריו בדרך כלל לא רוצה אתר שיכול להיעלם |
| **אחסון וורדפרס בתשלום** | **מומלץ** | עולה בערך כמה עשרות שקלים בחודש (בדקו מחיר עדכני, זה משתנה). יציב, כולל תעודת אבטחה (https), מייל, וגיבויים |
| להריץ על המחשב שלכם | לניסוי בלבד | תוכנת **Local** (חינמית) מאפשרת להקים וורדפרס על המחשב כדי לשחק ולעצב. האתר לא יהיה זמין לאחרים |

**ההמלצה שלי:** להתחיל בניסוי חינמי במחשב (או באחסון חינמי) כדי להתרשם, ולהעביר לאחסון בתשלום זול כשמוכנים לפתוח למשפחה. דומיין עולה בערך עשרות שקלים לשנה.

איך בוחרים אחסון? חפשו: "אחסון וורדפרס" עם: PHP גרסה 7.4 ומעלה, תעודת SSL חינמית, התקנת וורדפרס בלחיצה, גיבויים, ותמיכה בעברית אם חשוב לכם. יש חברות ישראליות וחברות בינלאומיות, ושתיהן מתאימות. כדאי לשאול את החברים אם יש אחסון שהם מרוצים ממנו. (אני לא מאשרת כאן חברה מסוימת ולא את המחירים שלה, כי הם משתנים.)

---

### חלק ב: התקנה, צעד אחרי צעד

#### שלב 1: להקים וורדפרס
בחשבון האחסון שלכם חפשו "התקנת וורדפרס" (או WordPress) ולחצו. תבחרו שם לאתר, שם משתמש וסיסמה. בסוף תקבלו כתובת כניסה לניהול, בדרך כלל `הכתובת-שלכם/wp-admin`. היכנסו.

#### שלב 2: להכין את שני הקבצים להעלאה
בפרויקט הזה יש שני חלקים שצריך להעלות:
- **תוסף** בשם `memorial-core` (התיקייה `wp-content/plugins/memorial-core`).
- **ערכת נושא** בשם `memorial` (התיקייה `wp-content/themes/memorial`).

איך מכינים? בעמוד הפרויקט בגיטהאב לחצו **Code** ← **Download ZIP**. פתחו את הקובץ שירד (חילוץ), היכנסו לתיקייה `wp-content`, ושם תמצאו `plugins/memorial-core` ו-`themes/memorial`. כעת כל אחת מהן צריכה להיות קובץ ZIP בנפרד:
- לחצו קליק ימני על התיקייה `memorial-core` ← "שלח אל" ← "תיקייה דחוסה" (Windows), או "דחיסה" (Mac). קיבלתם `memorial-core.zip`.
- אותו דבר עם התיקייה `memorial` ← `memorial.zip`.

(חשוב: בתוך הזיפ צריכה להיות תיקייה אחת בשם הנכון, ובתוכה הקבצים. אם בתוך הזיפ רואים ישר קבצים בלי תיקייה, ההתקנה תיכשל.)

#### שלב 3: להעלות ולהפעיל
1. בניהול וורדפרס: **תוספים** ← **הוספת תוסף** ← **העלאת תוסף** ← בחרו `memorial-core.zip` ← **התקנה** ← **הפעלה**.
2. **מראה** ← **ערכות נושא** ← **הוספת ערכה** ← **העלאת ערכה** ← `memorial.zip` ← **התקנה** ← **הפעלה**.

(אם העלאה דרך הניהול חסומה אצל חברת האחסון, אפשר להעלות את התיקיות ישירות דרך "מנהל קבצים" של האחסון או FTP: `memorial-core` אל `wp-content/plugins` ו-`memorial` אל `wp-content/themes`.)

#### שלב 4: להקים את המבנה
1. לחצו בתפריט הצד על **הגדרות** ← **אתר הזיכרון**.
2. בראש העמוד לחצו **יצירת עמודים וערכי ברירת מחדל**. זה יוצר לבד את העמודים הבסיסיים (סיפורים, אודות, מקומות הנצחה, יצירות, אלבום, שליחת סיפור, הספדים, נגישות, פרטיות) ואת פרקי החיים והערכים לדוגמה. אפשר ללחוץ שוב בלי חשש, זה לא דורס כלום.
3. **הגדרות** ← **קישורי קבע** ← בחרו **שם הפוסט** ← **שמירה**. (חשוב, אחרת הקישורים לא יעבדו.)

#### שלב 5: למלא את הפרטים (הגדרות ← אתר הזיכרון)
- **שם מלא** ו**שם פרטי**: השם הפרטי משמש בכותרות כמו "ספרו לנו על ___".
- **תואר או ברכה**: למשל ז״ל או הי״ד.
- **שנת לידה** ו**תאריך פטירה**: מהתאריך האתר מחשב לבד את יום הזיכרון העברי ומציג באנר אזכרה.
- **תמונה ראשית**: תמונה אנכית או ריבועית טובה. תופיע בדף הבית וגם כשמשתפים את האתר בוואטסאפ.
- **פסקת פתיחה** ו**משפט קצר לפוטר**: כמה שורות על האדם.
- **ציר זמן** (לא חובה): שורה לכל תחנה, בצורה `מתי | כותרת | טקסט קצר`. למשל: `1975 | נולד בחיפה | ילדות בשכונה`.
- **מיילים**: ראו שלב 8.
- **שורת הפוטר**: ראו בהמשך.
לחצו **שמירה** בתחתית.

#### שלב 6: פרקי חיים וערכים (סיפורים ← פרקי חיים / ערכים)
- לחצו על פרק קיים כדי לערוך: שם, **צבע**, **אייקון**, **תיאור**, **סדר הצגה**. אפשר למחוק פרקים שלא מתאימים ולהוסיף חדשים.
- **ערכים**: אותו דבר, ובנוסף **ציטוט** (משפט שהאדם כתב או שאמרו עליו) ו**מי אמר**.
- בשדה "מזהה" (slug) כתבו אנגלית בלי רווחים (למשל `childhood`). רק אם תרצו תמונות בעמוד פרק, ראו "אלבומים" בהמשך.

#### שלב 7: להוסיף תוכן
| מה | איפה בתפריט הצד | מה למלא |
|---|---|---|
| **סיפור** | סיפורים ← סיפור חדש | כותרת, הטקסט, "שם הכותב/ת", "הקשר" (למשל "אחות"), בחירת פרק וערך בצד, תמונה ראשית בצד ימין למטה. לחצו **פרסום** |
| **מקום הנצחה** | מקומות הנצחה ← מקום חדש | שם, תיאור, תמונה. בתיבת "פרטי המקום": הדביקו **קישור ממפות גוגל** והמיקום יתמלא לבד בשמירה |
| **יצירה** | יצירות ← יצירה חדשה | כותרת, תמונה ראשית (התמונה של היצירה), וסוג יצירה (למשל "ציורים") |
| **הספד או כתבה** | פוסטים ← הוספה | כותרת וטקסט. הקטגוריה שתבחרו (למשל "הספדים", "בתקשורת") תהפוך לקבוצה בעמוד "הספדים והנצחה". אם רק הדבקתם קישור לכתבה, יוצג כרטיס יפה |
| **עמוד "אודות"** | עמודים ← אודות | כתבו כאן את הסיפור המלא. זה העמוד הראשי על האדם |
| **תמונות** | מדיה ← הוספה | העלו תמונות, ואז ראו "ניהול תמונות" |

סיפורים ותמונות ששולחים אנשים דרך האתר יגיעו אליכם כ"ממתין לבדיקה", וגם יופיעו בלוח הבקרה. רק מה שתפרסמו יראו.

#### שלב 8: להגדיר מיילים (כדי לקבל התראות)
כשמישהו שולח סיפור, תמונה, תקבלו מייל עם קישור לקריאה ופרסום.
בהגדרות ← אתר הזיכרון:
- **מיילים שמקבלים סיפורים**: כתבו כתובת אחת או כמה מופרדות בפסיק. אם תשאירו ריק, זה יגיע למייל של מנהל האתר.
- **מייל ליצירת קשר**: מופיע בעמודי הנגישות והפרטיות.
- **כתובת השולח**: מומלץ כתובת באותו דומיין של האתר (למשל `noreply@שם-האתר.org.il`).

**אם לא מגיעים מיילים:** זו בעיה נפוצה באחסון (חברות חוסמות מייל, או ג'ימייל מסנן). הפתרון המקובל: להתקין תוסף חינמי בשם **WP Mail SMTP** (או דומה) ולחבר אותו לחשבון מייל. בכל מקרה סיפורים ממתינים תמיד מופיעים בלוח הבקרה, גם אם המייל לא הגיע.

#### שלב 9: ניהול תמונות ואלבומים
- **אלבומים**: **מדיה** ← **אלבומים**. צרו אלבום (למשל "ילדות", "משפחה", "צבא") ושם.
- **ניהול תמונות**: **מדיה** ← **ניהול תמונות**. מסך שמציג את כל התמונות בטבלה: תמונה קטנה, **כיתוב** (מופיע מתחת לתמונה), **טקסט חלופי** (תיאור קצר של התמונה, חשוב לעיוורים ולגוגל), ו**אלבום**. ממלאים הכול, ולוחצים **שמירת שינויים** פעם אחת. אפשר לסנן "ללא אלבום" או "בלי טקסט חלופי" כדי לראות מה חסר.
- אם תתנו לאלבום מזהה (slug) זהה למזהה של פרק (למשל שניהם `childhood`), התמונות של האלבום יופיעו גם בעמוד אותו פרק.
- אם יש לכם תוסף LiteSpeed Cache, המטמון מתנקה לבד אחרי שמירה.

#### שלב 10: התפריט והעמוד הראשי
- בלי שעשיתם כלום, בתפריט העליון יופיעו אוטומטית הקישורים לעמודים שקיימים.
- אם רוצים תפריט משלכם: **מראה** ← **תפריטים** ← צרו תפריט, ובחרו מיקום "תפריט ראשי". אפשר גם "תפריט תחתון" שיחליף את הקישורים בפוטר.
- עמוד הבית נבנה לבד מהתוכן שהכנסתם. כל חלק מופיע רק אם יש לו תוכן.

#### שלב 11: הפוטר (תחתית האתר)
**הגדרות** ← **אתר הזיכרון** ← "שורת הפוטר התחתונה". אפשר לכתוב כל טקסט, כולל קישור. אם משאירים ריק, מוצגת ברירת מחדל: זכויות יוצרים, שם האתר וקרדיט **"חולמים תקשוב"** עם קישור. הקרדיט חלק מברירת המחדל ואפשר לערוך אותו כרצונכם, ונשמח אם יישאר. אין באתר כפתור משוב.

#### שלב 12: לפני שפותחים למשפחה
- [ ] הכניסו לאתר מטלפון וממחשב ובדקו שהכול נראה טוב.
- [ ] שלחו סיפור בדיקה דרך הטופס, וודאו שהוא מגיע ל"ממתין לבדיקה" (ומייל, אם הגדרתם).
- [ ] עברו על עמודי **הצהרת נגישות** ו**מדיניות פרטיות** (עמודים ← ערכו) והתאימו.
- [ ] ודאו שהכתובת מתחילה ב-`https` (מנעול). זה בדרך כלל חינמי אצל חברת האחסון.
- [ ] תנו לאנשים לנסות לפני שמפיצים. ושמרו גיבוי (רוב האחסונים מציעים גיבוי אוטומטי).

---

### שאלות נפוצות

**העמוד נראה ריק או בלי עיצוב.** ודאו שהפעלתם **גם** את התוסף **וגם** את ערכת הנושא, ושלחצתם "יצירת עמודים" בהגדרות.

**הקישורים (למשל "סיפורים") נותנים 404.** הגדרות ← קישורי קבע ← "שם הפוסט" ← שמירה.

**ההעלאה נכשלת ("אין תיקייה" או "חבילה לא תקינה").** כנראה הזיפ נבנה לא נכון. התיקייה צריכה להיות בשורש הזיפ. ראו שלב 2.

**אני לא רואה את המפה.** צריך לפחות שני מקומות הנצחה עם מיקום (קישור ממפות גוגל) כדי שתופיע מפה כללית. מקום אחד מציג מפה בעמוד שלו.

**התאריך העברי בבאנר לא נכון בדיוק.** האתר מחשב לפי התאריך הלועזי. מי שנפטר אחרי השקיעה אולי נחשב ליום העברי הבא. כך תוכלו לבדוק ולהתאים (אפשר להזיז תאריך הפטירה ביום אחד בהגדרות, או לשים הודעה מיוחדת בבאנר).

**אפשר לשנות את הצבעים והעיצוב?** כן, בקובץ `assets/css/main.css` בערכת הנושא, בתחילת הקובץ (`:root`). זה כבר דורש קצת עריכת קוד. עדיף לבקש מחבר שמבין, או מעוזר בינה מלאכותית.

---

### האם זה עובד רק על וורדפרס? ומה עם סוכן בינה מלאכותית?

**כן, כרגע זה וורדפרס בלבד.** התוסף והערכה כתובים ב-PHP ומשתמשים במנגנוני וורדפרס (סוגי תוכן, מסך ניהול, טפסים, בסיס נתונים).

מי שרוצה פלטפורמה אחרת (אתר סטטי, Next.js, Supabase וכו') ויבקש מסוכן בינה מלאכותית "לתרגם" את הפרויקט, יכול לנסות, אבל זו **בנייה מחדש** ולא העתקה: צריך לבנות מחדש את מבנה הנתונים (סיפורים, מקומות, יצירות), את מסך הניהול, את הטפסים (כולל אישור לפני פרסום, הגנה מספאם ושליחת מייל) ואת ניהול התמונות. את העיצוב (`main.css`, `main.js`), הפונטים, המפה והאיורים אפשר לקחת כמעט כמו שהם. למי שבונה כך, מה שכדאי להעביר לסוכן: את הקבצים של התוסף כמפרט התנהגות, ואת התיקייה `themes/memorial/assets` כמו שהיא.

---

### למפתחים: מבנה הפרויקט

```
wp-content/
  plugins/memorial-core/      # לוגיקה, סוגי תוכן, טפסים, SEO, ניהול
    memorial-core.php
    inc/helpers.php           # הגדרות, תאריך עברי, פרקים וערכים, ציר זמן
    inc/post-types.php        # story, bench, creation, visit + chapter, value, medium, album
    inc/terms.php             # צבע, אייקון, סדר, ציטוט לפרקים ולערכים
    inc/meta-boxes.php        # שדות מותאמים (בלי ACF), פענוח קישור מפות גוגל
    inc/settings.php          # מסך "אתר הזיכרון"
    inc/setup.php             # יצירת עמודים וערכי ברירת מחדל
    inc/forms.php             # טפסי סיפור וביקור (honeypot, הגבלת קצב, בדיקת קבצים)
    inc/seo.php               # כותרות, Open Graph, JSON-LD, robots, llms.txt
    inc/routing.php           # /random-story/ והרחבת חיפוש
    inc/media-manager.php     # מסך ניהול תמונות
    inc/admin.php             # וידג'ט "ממתינים", עורך קלאסי
  themes/memorial/            # תצוגה
    templates/page-*.php      # תבניות לפי slug של העמוד
    assets/                   # css, js, פונטים, Leaflet
```

- קידומת אחת בכל מקום: `memorial_` / `memorial-`. האפשרויות ב-`memorial_options`.
- המזהה הפנימי של "מקומות הנצחה" נשאר `bench` (שדות `bench_*`).
- הטקסטים בתבניות בעברית ובלשון זכר כברירת מחדל. את ניסוח הבאנר מגדירים בהגדרות.
- אין סיסמאות, מפתחות או מיילים בקוד. הכול בבסיס הנתונים של האתר.
- בדיקת תחביר: `php -l` על כל קובצי ה-PHP.
- דרישות: וורדפרס 6.3 ומעלה, PHP 7.4 ומעלה עם `mbstring` ו-`calendar`.

### רישיון וקרדיט

GPL-2.0-or-later (ראו `LICENSE`). פונטים, מפה ואייקונים ברישיונות פתוחים נפרדים, ראו `CREDITS.md`. נשמח שקרדיט **"חולמים תקשוב"** (https://chepti.com) יישאר בפוטר, אך זו החלטה שלכם.

---

## English

**Memorial** is a free, open-source (GPL-2.0-or-later) WordPress theme + plugin for a Hebrew, right-to-left family memorial website. It was extracted from one family's site and generalised so others can build one without coding.

> **It is WordPress-only.** It needs PHP and MySQL. It will not run on static hosts or app backends (GitHub Pages, Netlify, Vercel, Cloudflare Pages, Supabase, Firebase) or on site builders (Wix, Squarespace).

### What you get

Stories (submitted via a form and published only after approval), configurable life chapters and values (colour and icon), memorial places on a map, creations, photo albums, tributes, a timeline, an automatic yahrzeit banner, an accessibility toolbar, SEO/JSON-LD/`llms.txt`, and a bulk image manager.

### Hosting: do you have to pay?

| Option | Works? | Notes |
|---|---|---|
| Supabase, Firebase, Netlify, Vercel, GitHub Pages | No | No PHP/MySQL |
| WordPress.com free plan | No | Cannot upload custom themes/plugins (only on higher paid plans) |
| Free WordPress hosts (e.g. InfinityFree) | Possible, not recommended | Slow, suspensions, ads, outgoing mail usually blocked (no notification emails) |
| Cheap paid WordPress hosting | **Recommended** | A few dollars a month (check current prices). HTTPS, mail, backups |
| WordPress on your own computer (the free "Local" app) | Trial only | Not visible to others |

A domain is optional but recommended (a small yearly fee). Suggested path: try it for free on your computer, then move to cheap paid hosting when you are ready to invite the family.

### Install (step by step)

1. Create a WordPress site at your host (one-click install) and log in to `/wp-admin`.
2. Download this repo (Code > Download ZIP), unzip it, and zip **each** folder separately: `wp-content/plugins/memorial-core` and `wp-content/themes/memorial` (the folder must be the zip's root).
3. **Plugins > Add New > Upload** `memorial-core.zip`, activate. **Appearance > Themes > Add New > Upload** `memorial.zip`, activate. (Or upload the folders via the host's file manager/FTP.)
4. **Settings > Memorial site (אתר הזיכרון)**: press **Create pages and default values**. Then **Settings > Permalinks > Post name > Save**.
5. Fill in the settings: names, honorific, birth year, date of passing (the Hebrew date and banner are computed from it), hero image, intro text, optional timeline (`when | title | text` per line), emails, and the footer line.
6. Edit chapters/values under **Stories**. Add stories, memorial places (paste a Google Maps link to fill coordinates), creations, tributes (regular posts, grouped by category), and write the **About** page.
7. Photos: upload to Media, create albums (**Media > Albums**), then use **Media > Image manager** to set caption, alt text and album for many images at once, with one Save (it also purges the LiteSpeed cache if present).
8. Emails: set the notification recipients (default: site admin). If mail doesn't arrive, install an SMTP plugin such as WP Mail SMTP. Pending stories always appear on the dashboard anyway.
9. Footer: **Settings > Memorial site > Footer line** is free text (links allowed). Empty = default line with the "חולמים תקשוב" credit (https://chepti.com), which you can edit or remove; we'd appreciate it staying. There is no feedback button.

Before launch: test on a phone, send a test story, review the accessibility and privacy pages, make sure the site uses https, keep backups.

### Other platforms / AI agents

Porting to another stack is a **rebuild**, not a copy: you must recreate the data model, admin screens, moderated forms (spam protection, email) and the image manager. The CSS/JS (`themes/memorial/assets`), fonts, map and illustrations can be reused almost as-is; the plugin's PHP is a good behavioural spec for an agent.

### For developers

One prefix: `memorial_` / `memorial-`; options in `memorial_options`; internal post type for places stays `bench`; template strings are Hebrew (masculine by default); no secrets in code. Requires WordPress 6.3+, PHP 7.4+ with `mbstring` and `calendar`. See the tree above.

### License and credit

GPL-2.0-or-later (`LICENSE`); third-party components in `CREDITS.md`. Please consider keeping the "חולמים תקשוב" credit (https://chepti.com) in the footer; it is your choice.
